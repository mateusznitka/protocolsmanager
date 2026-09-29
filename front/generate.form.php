<?php
include ('../../../inc/includes.php');

Session::checkLoginUser();
Session::checkRight('plugin_protocolsmanager_tab', READ);

$PluginProtocolsmanagerGenerate = new PluginProtocolsmanagerGenerate();

if (isset($_POST['generate'])) {
	$PluginProtocolsmanagerGenerate::makeProtocol();
	Html::back();
}

if (isset($_POST['delete'])) {
	$PluginProtocolsmanagerGenerate::deleteDocs();
	Html::back();
}

if (isset($_POST['send'])) {
	$id = (int) ($_POST['user_id'] ?? 0);
	$PluginProtocolsmanagerGenerate::sendOneMail($id);
	Html::back();
}

?>
