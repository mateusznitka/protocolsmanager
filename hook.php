<?php
function plugin_protocolsmanager_redefine_menus($menu) {
	global $DB;
	if (empty($menu)) {
		return $menu;
	}
	if(isset($_SESSION['glpiID'])){
		$id = $_SESSION['glpiID'];
		$query = "SELECT COUNT(id) as field_count FROM `glpi_plugin_protocolsmanager_receipt` 
					where profile_id = " . $id . " AND confirmed = 0";
		$query2 = 'SELECT * FROM `glpi_plugin_protocolsmanager_settings` WHERE id = 1';
		$result = $DB->request($query)->current();
		$result2 = $DB->request($query2)->current();

		if($result['field_count'] > 0 && $result2['protocols_save_on']){
			$menu['protocols'] = [
				'default' => '/plugins/protocolsmanager/front/protocols.form.php',
				'title'   => __('Sign protocol','protocolsmanager'),
				'content' => [false],
				'icon'    => 'ti ti-alert-circle'
			];
		}
	}
	if($result2['show_own_assets']){
		$menu['myAssets'] = [
			'default' => '/plugins/protocolsmanager/front/myAssets.form.php',
			'title'   => __('My assets','protocolsmanager'),
			'content' => [false],
			'icon'    => 'ti ti-alert-circle'
		];
	}
	return $menu;
}

function plugin_protocolsmanager_install() {
	global $DB, $CFG_GLPI;
	$version = plugin_version_protocolsmanager();
	$migration = new Migration($version['version']);

	if (!countElementsInTable('glpi_crontasks', ['name' => 'PluginProtocolsmanagerReminder'])) {
		Crontask::Register('PluginProtocolsmanagerReminder', 'PluginProtocolsmanagerReminder', DAY_TIMESTAMP, [
			'param' => '',
			'mode'  => CronTask::MODE_EXTERNAL
		]);
	}

	if (!$DB->tableExists("glpi_plugin_protocolsmanager_mails_templates")) {

		$query = "CREATE TABLE glpi_plugin_protocolsmanager_mails_templates (
					id int(11) NOT NULL auto_increment,
					template_name varchar(255),
					template_title TEXT,
					template_body TEXT,
					PRIMARY KEY (id)
					) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$DB->query($query) or die($DB->error());
	}

	if (!$DB->tableExists("glpi_plugin_protocolsmanager_settings")) {
		$query = "CREATE TABLE glpi_plugin_protocolsmanager_settings (
					id int(11) NOT NULL auto_increment,
					protocols_save_on int(2),
					mail_confirm_on int(2),
					reminder_on int(2),
					first_emial_reminder varchar(255),
					second_emial_reminder varchar(255),
					how_often_remind int(11),
					show_own_assets int(1),
					user_fields varchar(255),
					PRIMARY KEY (id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$DB->query($query) or die($DB->error());

		$query2 = "INSERT INTO `glpi_plugin_protocolsmanager_settings`(
                    `id`,
                    `protocols_save_on`,
                    `mail_confirm_on`,
                    `reminder_on`,
                    `first_emial_reminder`,
                    `second_emial_reminder`,
                    `how_often_remind`,
                    `show_own_assets`,
                    `user_fields`) 
                    VALUES (1,0,0,0,null,null,0,0,'')";

		$DB->queryOrDie($query2, $DB->error());
	}

	if (!$DB->tableExists("glpi_plugin_protocolsmanager_receipt")) {

		$query = "CREATE TABLE glpi_plugin_protocolsmanager_receipt (
					id int(11) NOT NULL auto_increment,
					profile_id int(11),
					confirmed int(11),
					protocol_id int(11),
					modified datetime,
					PRIMARY KEY (id)
					) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$DB->query($query) or die($DB->error());
	}

	if (!$DB->tableExists("glpi_plugin_protocolsmanager_confirm")) {

		$query = "CREATE TABLE glpi_plugin_protocolsmanager_confirm (
					id int(11) NOT NULL auto_increment,
					id_user int(11),
					id_protocol int(11),
					code_confirm int(11),
					modified datetime,
					PRIMARY KEY (id)
					) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$DB->query($query) or die($DB->error());
	}

	if (!$DB->tableExists("glpi_plugin_protocolsmanager_profiles")) {

		$query = "CREATE TABLE glpi_plugin_protocolsmanager_profiles (
					id int(11) NOT NULL auto_increment,
					profile_id int(11),
					plugin_conf char(1) collate utf8_unicode_ci default NULL,
					tab_access char(1) collate utf8_unicode_ci default NULL,
					make_access char(1) collate utf8_unicode_ci default NULL,
					delete_access char(1) collate utf8_unicode_ci default NULL,
					PRIMARY KEY  (`id`)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$DB->query($query) or die($DB->error());

		$id = $_SESSION['glpiactiveprofile']['id'];
		$query = "INSERT INTO glpi_plugin_protocolsmanager_profiles (profile_id, plugin_conf, tab_access, make_access, delete_access) VALUES ('$id','w', 'w', 'w', 'w')";

		$DB->query($query) or die($DB->error());
	}

	//update profiles table if updating from 0.8
	if (!$DB->FieldExists('glpi_plugin_protocolsmanager_profiles', 'plugin_conf')) {

		$query = "DROP TABLE glpi_plugin_protocolsmanager_profiles";

		$DB->query($query) or die($DB->error());

		$query = "CREATE TABLE glpi_plugin_protocolsmanager_profiles (
					id int(11) NOT NULL auto_increment,
					profile_id int(11),
					plugin_conf char(1) collate utf8_unicode_ci default NULL,
					tab_access char(1) collate utf8_unicode_ci default NULL,
					make_access char(1) collate utf8_unicode_ci default NULL,
					delete_access char(1) collate utf8_unicode_ci default NULL,
					my_assets char(1) collate utf8_unicode_ci default NULL,
					PRIMARY KEY  (`id`)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$DB->query($query) or die($DB->error());

		$id = $_SESSION['glpiactiveprofile']['id'];
		$query = "INSERT INTO glpi_plugin_protocolsmanager_profiles (profile_id, plugin_conf, tab_access, make_access, delete_access, my_assets) VALUES ('$id','w', 'w', 'w', 'w', 'w')";

		$DB->query($query) or die($DB->error());
	}


	if (!$DB->tableExists('glpi_plugin_protocolsmanager_config')) {

		$query = "CREATE TABLE glpi_plugin_protocolsmanager_config (
					id INT(11) NOT NULL auto_increment,
					name VARCHAR(255),
					font varchar(255),
					fontsize varchar(255),
					logo varchar(255),
					content text,
					footer text,
					city varchar(255),
					serial_mode int(2),
					column1 varchar(255),
					column2 varchar(255),
					orientation varchar(10),
					breakword int(2),
					email_mode int(2),
					email_template int(2),
					PRIMARY KEY (id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$DB->queryOrDie($query, $DB->error());

		$query2 = "INSERT INTO glpi_plugin_protocolsmanager_config (
					name, font, fontsize, content, footer, city, serial_mode, orientation, breakword)
					VALUES ('Equipment report',
							'Roboto',
							'9',
							'User: \n I have read the terms of use of IT equipment in the Example Company.',
							'Example Company \n Example Street 21 \n 01-234 Example City',
							'Example city',
							1,
							'Portrait',
							1)";

		$DB->queryOrDie($query2, $DB->error());
	}

	//update config table if upgrading from 1.0
	if (!$DB->FieldExists('glpi_plugin_protocolsmanager_config', 'orientation')) {

		$query = "ALTER TABLE glpi_plugin_protocolsmanager_config
					ADD serial_mode int(2)
						AFTER city,
					ADD column1 varchar(255)
						AFTER serial_mode,
					ADD column2 varchar(255)
						AFTER column1,
					ADD orientation varchar(10)
						AFTER column2";

		$DB->queryOrDie($query, $DB->error());

	}

	//update config table if upgrading from 1.1.2
	if (!$DB->FieldExists('glpi_plugin_protocolsmanager_config', 'fontsize')) {

		$query = "ALTER TABLE glpi_plugin_protocolsmanager_config
					ADD fontsize varchar(255)
						AFTER font,
					ADD breakword int(2)
						AFTER fontsize";

		$DB->queryOrDie($query, $DB->error());

		$query = "UPDATE glpi_plugin_protocolsmanager_config
					SET serial_mode=1, orientation='p', fontsize='9', breakword=1";

		$DB->queryOrDie($query, $DB->error());

	}


	//update config table if upgrading from 1.2
	if (!$DB->FieldExists('glpi_plugin_protocolsmanager_config', 'email_mode')) {

		$query = "ALTER TABLE glpi_plugin_protocolsmanager_config
					ADD email_mode int(2)
						AFTER breakword,
					ADD email_template int(2)
						AFTER email_mode";

		$DB->queryOrDie($query, $DB->error());

		$query = "UPDATE glpi_plugin_protocolsmanager_config
					SET email_mode=2";

		$DB->queryOrDie($query, $DB->error());

	}

	//update config table if upgrading from 1.3
	if (!$DB->FieldExists('glpi_plugin_protocolsmanager_config', 'upper_content')) {

		$query = "ALTER TABLE glpi_plugin_protocolsmanager_config
					ADD upper_content text
						AFTER email_mode";

		$DB->queryOrDie($query, $DB->error());
	}

	//update email_content field
	if ($DB->FieldExists('glpi_plugin_protocolsmanager_emailconfig', 'email_content')) {

		$query = "ALTER TABLE glpi_plugin_protocolsmanager_emailconfig MODIFY COLUMN email_content TEXT";

		$DB->queryOrDie($query, $DB->error());
	}

	//add new column glpi_plugin_protocolsmanager_settings
	if (($DB->tableExists('glpi_plugin_protocolsmanager_emailconfig')) &&
		!$DB->FieldExists('glpi_plugin_protocolsmanager_settings', 'show_own_assets')) {

		$query = "ALTER TABLE glpi_plugin_protocolsmanager_settings ADD COLUMN show_own_assets int(1)";
		$query2 = "UPDATE glpi_plugin_protocolsmanager_settings SET show_own_assets = 0 WHERE id=1";

		$DB->queryOrDie($query, $DB->error());
		$DB->queryOrDie($query2, $DB->error());
	}

	//add another new column glpi_plugin_protocolsmanager_settings user_fields
	if (($DB->tableExists('glpi_plugin_protocolsmanager_emailconfig')) &&
		!$DB->FieldExists('glpi_plugin_protocolsmanager_settings', 'user_fields')) {

		$query = "ALTER TABLE glpi_plugin_protocolsmanager_settings ADD COLUMN user_fields varchar(255)";
		$query2 = "UPDATE glpi_plugin_protocolsmanager_settings SET user_fields = '' WHERE id=1";

		$DB->queryOrDie($query, $DB->error());
		$DB->queryOrDie($query2, $DB->error());
	}

	//add new column glpi_plugin_protocolsmanager_profiles - my_assets
	if (($DB->tableExists('glpi_plugin_protocolsmanager_profiles')) &&
		!$DB->FieldExists('glpi_plugin_protocolsmanager_profiles', 'my_assets')) {

		$query = "ALTER TABLE glpi_plugin_protocolsmanager_profiles ADD COLUMN my_assets char(1) collate utf8_unicode_ci default NULL";

		$DB->queryOrDie($query, $DB->error());

	}

	//add new column glpi_plugin_protocolsmanager_profiles - sign_protocol_form
	if (($DB->tableExists('glpi_plugin_protocolsmanager_profiles')) &&
		!$DB->FieldExists('glpi_plugin_protocolsmanager_profiles', 'sign_protocol_form')) {

		$query = "ALTER TABLE glpi_plugin_protocolsmanager_profiles ADD COLUMN sign_protocol_form char(1) collate utf8_unicode_ci default NULL";

		$DB->queryOrDie($query, $DB->error());

	}

	if (!$DB->tableExists('glpi_plugin_protocolsmanager_emailconfig')) {

		$query = "CREATE TABLE glpi_plugin_protocolsmanager_emailconfig (
					id INT(11) NOT NULL auto_increment,
					tname varchar(255),
					send_user int(2),
					email_content text,
					email_subject varchar(255),
					email_footer varchar(255),
					recipients varchar(255),
					PRIMARY KEY (id)
					) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$DB->queryOrDie($query, $DB->error());

	}

	if (!$DB->tableExists('glpi_plugin_protocolsmanager_protocols')) {

		$query = "CREATE TABLE glpi_plugin_protocolsmanager_protocols (
					id INT(11) NOT NULL auto_increment,
					name VARCHAR(255),
					user_id int(11),
					gen_date datetime,
					author varchar(255),
					document_id int(11),
					document_type varchar(255),
					PRIMARY KEY (id)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$DB->queryOrDie($query, $DB->error());
	}

	if ($DB->tableExists('glpi_plugin_protocolsmanager_config')) {
		return true;
	}

	if ($DB->tableExists('glpi_plugin_protocolsmanager_protocols')) {
		return true;
	}

	//execute the whole migration
	$migration->executeMigration();

	return true;
}


function plugin_protocolsmanager_uninstall() {

	global $DB;

	$tables = array("glpi_plugin_protocolsmanager_protocols",
		"glpi_plugin_protocolsmanager_config",
		"glpi_plugin_protocolsmanager_profiles",
		"glpi_plugin_protocolsmanager_emailconfig",
		"glpi_plugin_protocolsmanager_receipt",
		"glpi_plugin_protocolsmanager_settings",
		"glpi_plugin_protocolsmanager_confirm"
	);

	foreach($tables as $table)
	{$DB->query("DROP TABLE IF EXISTS `$table`;");}

	return true;
}

?>
