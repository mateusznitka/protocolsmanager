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

	if (isset($_REQUEST['save_email'])) {
		$PluginProtocolsmanagerConfig::saveEmailConfigs();
		Html::back();
	}	
	
	if (isset($_REQUEST['delete_email'])) {
		$PluginProtocolsmanagerConfig::deleteEmailConfigs();
		Html::back();
	}	
	
	if (isset($_REQUEST['cancel'])) {
		Html::back();
	}
	
	$PluginProtocolsmanagerConfig->showFormProtocolsmanager();
	
?>

<script>

$(function(){
	$("#template_button").click(function(){
		$("#template_settings").show();
		$("#show_configs").show();
		$("#email_settings").hide();
		$("#show_emailconfigs").hide();
	});	
	$("#email_button").click(function(){
		$("#template_settings").hide();
		$("#show_configs").hide();
		$("#email_settings").show();
		$("#show_emailconfigs").show();
	});
});	

</script>