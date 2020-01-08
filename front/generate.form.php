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

/*$_POST['number'], $_POST['type_name'], $_POST['man_name'], $_POST['mod_name'], $_POST['serial'], $_POST['item_name'], $_POST['owner'], $_POST['user_id'], $_POST['author'], $_POST['list']*/

?>