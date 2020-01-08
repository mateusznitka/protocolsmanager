<?php

/**
 * Called when user click on Install - Needed
 */
function plugin_protocolsmanager_install() 
{ 
	global $DB, $CFG_GLPI;
	$version = plugin_version_protocolsmanager();
	$migration = new Migration($version['version']);
	
	
	if (!$DB->tableExists("glpi_plugin_protocolsmanager_profiles")) 
	{
   
	$query = "CREATE TABLE `glpi_plugin_protocolsmanager_profiles` (
				`id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_profiles (id)',
				`right` char(1) collate utf8_unicode_ci default NULL,
				PRIMARY KEY  (`id`)
			  ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

	$DB->query($query) or die($DB->error());

	$id = $_SESSION['glpiactiveprofile']['id'];
	$query = "INSERT INTO glpi_plugin_protocolsmanager_profiles VALUES ('$id','w')";

	$DB->query($query) or die($DB->error());
	}
	
	if (!$DB->tableExists('glpi_plugin_protocolsmanager_configs')) {
      
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
	
	if ($DB->tableExists('glpi_plugin_protocolsmanager_configs'))
	{
		return true;
	}	
	
	if ($DB->tableExists('glpi_plugin_protocolsmanager_protocols'))
	{
		return true;
	}


	//execute the whole migration
	$migration->executeMigration();
	
	return true; 
}
 
/**
 * Called when user click on Uninstall - Needed
 */
function plugin_protocolsmanager_uninstall() { 

	global $DB;
	
	$tables = array("glpi_plugin_protocolsmanager_protocols", "glpi_plugin_protocolsmanager_config", "glpi_plugin_protocolsmanager_profiles");

	foreach($tables as $table) 
		{$DB->query("DROP TABLE IF EXISTS `$table`;");}
	
	return true; 
	
	}

?>