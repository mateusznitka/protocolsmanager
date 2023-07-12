<?php

include('../../../inc/includes.php');
header("Content-Type: text/html; charset=UTF-8");

Html::header_nocache();
Session::checkLoginUser();
Session::checkValidSessionId();
Session::haveRight("config", UPDATE);
	
	$PluginProtocolsmanagerConfig = new PluginProtocolsmanagerConfig();
	
    if (isset($_REQUEST['witch_field_settings'])){
		
		$postData = array(
			"menu_mode" => "e",
			"checkVal"  => $_POST["checkVal"],
			"witch_field_settings" => $_POST["witch_field_settings"],
			"service_settings"  => "Change",
		);
		
		global $DB;

	try{
			$DB->update('glpi_plugin_protocolsmanager_settings',
				[
					$postData['witch_field_settings'] => $postData['checkVal'],
				],
				[
					'id' => 1
				]
			);

$token = Session::getNewCSRFToken();
	 $res = array("result" => "res_ok", "token" => $token);
	$res_json = json_encode($res);
			die($res_json);
		}catch(Exception $e){
			die('Error - field not updated');
		}
	}	
?>
