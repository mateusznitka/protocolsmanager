<?php
	
	include ('../../../inc/includes.php');
	
	Session::haveRight("config", UPDATE);
	
	Html::header(PluginProtocolsmanagerConfig::getTypeName(1),
               $_SERVER['PHP_SELF'], "plugins", "protocolsmanager", "config");
			   
	$PluginProtocolsmanagerConfig = new PluginProtocolsmanagerConfig();
	
	if (isset($_REQUEST['save'])) {
		$PluginProtocolsmanagerConfig::saveConfigs();
		Html::back();
	}	
	
	if (isset($_REQUEST['delete'])) {
		$PluginProtocolsmanagerConfig::deleteConfigs();
		Html::back();
	}	
	
	if (isset($_REQUEST['cancel'])) {
		Html::back();
	}
	
	$PluginProtocolsmanagerConfig->showFormProtocolsmanager();
	
?>
	