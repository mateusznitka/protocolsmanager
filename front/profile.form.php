<?php
include ('../../../inc/includes.php');
Session::checkValidSessionId();
$PluginProtocolsmanagerProfile = new PluginProtocolsmanagerProfile();

if (isset($_REQUEST['update'])) {
	$PluginProtocolsmanagerProfile::updateRights();
	Html::back();
}

?>