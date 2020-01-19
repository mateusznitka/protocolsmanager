<?php
	
	include ('../../../inc/includes.php');
	
	Session::haveRight("config", UPDATE);
	
	Html::header(PluginProtocolsmanagerConfig::getTypeName(1),
               $_SERVER['PHP_SELF'], "plugins", "protocolsmanager", "config");
			   
	$PluginProtocolsmanagerConfig = new PluginProtocolsmanagerConfig();
	
	if (isset($_REQUEST['save'])) {
		$PluginProtocolsmanagerConfig::saveConfigs();
	}	
	
	if (isset($_REQUEST['delete'])) {
		$PluginProtocolsmanagerConfig::deleteConfigs($_POST["conf_id"]);
	}	
	
	$PluginProtocolsmanagerConfig->showFormProtocolsmanager();
	
		
	/*$_POST["template_name"], $_POST["template_content"], $_POST["footer_text"], $_POST["font"], $_POST["city"], $_POST["mode"], $_FILES["logo"]*/
?>
	