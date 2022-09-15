<?php
include ("../../../inc/includes.php");
require_once dirname(__DIR__) . '/inc/sign.class.php';
Html::header('Self-service');

$PluginProtocolsmanagerProtocols = new PluginProtocolsmanagerProtocols();
$PluginProtocolsmanagerProtocols->showFormProtocols();

if(isset($_REQUEST['sign_protocols_submit'])){
	
	$SignProtocol = new SignProtocol();
	$SignProtocol->trySaveAction($_POST);
	
}

if(isset($_REQUEST['sign_protocols_submit_confirm'])){
	
	$SignProtocol = new SignProtocol();
	$SignProtocol->checkConfirmationCode($_POST);
	
}

Html::footer();
