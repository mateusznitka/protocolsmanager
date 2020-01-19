<?php

function plugin_protocolsmanager_install() { 
	global $DB, $CFG_GLPI;
	$version = plugin_version_protocolsmanager();
	$migration = new Migration($version['version']);	
	
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
					PRIMARY KEY  (`id`)
				  ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$DB->query($query) or die($DB->error());

		$id = $_SESSION['glpiactiveprofile']['id'];
		$query = "INSERT INTO glpi_plugin_protocolsmanager_profiles (profile_id, plugin_conf, tab_access, make_access, delete_access) VALUES ('$id','w', 'w', 'w', 'w')";

		$DB->query($query) or die($DB->error());
	}
		
	
	if (!$DB->tableExists('glpi_plugin_protocolsmanager_config')) {
      
		$query = "CREATE TABLE glpi_plugin_protocolsmanager_config (
				  id INT(11) NOT NULL auto_increment,
				  name VARCHAR(255),
				  font varchar(255),
				  logo varchar(255),
				  content text,
				  footer text,
				  city varchar(255),
				  PRIMARY KEY (id)
			   ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			   
		$DB->queryOrDie($query, $DB->error());
	  
		$query2 = "INSERT INTO glpi_plugin_protocolsmanager_config (
					name, font, content, footer, city)
					VALUES ('Equipment report',
							'freesans',
							'User: \n I have read the terms of use of IT equipment in the Example Company.',
							'Example Company \n Example Street 21 \n 01-234 Example City',
							'Example city')";
							
		$DB->queryOrDie($query2, $DB->error());
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
	
	$tables = array("glpi_plugin_protocolsmanager_protocols", "glpi_plugin_protocolsmanager_config", "glpi_plugin_protocolsmanager_profiles");

	foreach($tables as $table) 
		{$DB->query("DROP TABLE IF EXISTS `$table`;");}
	
	return true; 
	
	}

?>