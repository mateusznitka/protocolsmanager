<?php

function plugin_protocolsmanager_install() {
	global $DB;

	$profileRight = new ProfileRight();
	$profiles_id  = $_SESSION['glpiactiveprofile']['id'];
	foreach (['plugin_protocolsmanager_config' => ALLSTANDARDRIGHT, 'plugin_protocolsmanager_tab' => READ] as $right => $value) {
		if (!countElementsInTable('glpi_profilerights', ['profiles_id' => $profiles_id, 'name' => $right])) {
			$profileRight->add(['profiles_id' => $profiles_id, 'name' => $right, 'rights' => $value]);
		}
	}

	if (!$DB->tableExists('glpi_plugin_protocolsmanager_configs')) {
		$DB->doQuery("CREATE TABLE glpi_plugin_protocolsmanager_configs (
			id int NOT NULL auto_increment,
			name varchar(255),
			font varchar(255),
			fontsize varchar(255),
			logo varchar(255),
			content text,
			footer text,
			city varchar(255),
			serial_mode int,
			column1 varchar(255),
			column2 varchar(255),
			orientation varchar(10),
			breakword int,
			email_mode int,
			email_template int,
			upper_content text,
			PRIMARY KEY (id)
		) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

		$DB->insert('glpi_plugin_protocolsmanager_configs', [
			'name'        => 'Equipment report',
			'font'        => 'Roboto',
			'fontsize'    => '9',
			'content'     => "User: \n I have read the terms of use of IT equipment in the Example Company.",
			'footer'      => "Example Company \n Example Street 21 \n 01-234 Example City",
			'city'        => 'Example city',
			'serial_mode' => 1,
			'orientation' => 'Portrait',
			'breakword'   => 1,
			'email_mode'  => 2,
		]);
	}

	if (!$DB->tableExists('glpi_plugin_protocolsmanager_emailconfig')) {
		$DB->doQuery("CREATE TABLE glpi_plugin_protocolsmanager_emailconfig (
			id int NOT NULL auto_increment,
			tname varchar(255),
			send_user int,
			email_content text,
			email_subject varchar(255),
			email_footer varchar(255),
			recipients varchar(255),
			PRIMARY KEY (id)
		) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
	}

	if (!$DB->tableExists('glpi_plugin_protocolsmanager_protocols')) {
		$DB->doQuery("CREATE TABLE glpi_plugin_protocolsmanager_protocols (
			id int NOT NULL auto_increment,
			name varchar(255),
			user_id int,
			gen_date datetime,
			author varchar(255),
			document_id int,
			document_type varchar(255),
			PRIMARY KEY (id)
		) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
	}

	return true;
}


function plugin_protocolsmanager_uninstall() {
	global $DB;

	$tables = [
		'glpi_plugin_protocolsmanager_protocols',
		'glpi_plugin_protocolsmanager_configs',
		'glpi_plugin_protocolsmanager_emailconfig',
	];

	foreach ($tables as $table) {
		$DB->doQuery("DROP TABLE IF EXISTS `$table`");
	}

	$DB->delete('glpi_profilerights', [
		'name' => ['plugin_protocolsmanager_config', 'plugin_protocolsmanager_tab'],
	]);

	return true;
}
