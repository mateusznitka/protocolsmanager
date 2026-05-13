<?php
include ('../../../inc/includes.php');
$PluginProtocolsmanagerGenerate = new PluginProtocolsmanagerGenerate();

if (isset($_REQUEST['generate'])) {
	$PluginProtocolsmanagerGenerate::makeProtocol();
	Html::back();
}

if (isset($_REQUEST['delete'])) {
	$PluginProtocolsmanagerGenerate::deleteDocs();
	Html::back();
}

if (isset($_REQUEST['send'])) {
	$id = (int) ($_REQUEST['user_id'] ?? 0);
	$PluginProtocolsmanagerGenerate::sendOneMail($id);
	Html::back();
}

?>