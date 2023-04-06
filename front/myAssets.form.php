<?php
include ("../../../inc/includes.php");
require_once dirname(__DIR__) . '/inc/ShowUserAssets.php';
Session::checkValidSessionId();
Html::header('Self-service');
$userAssets = new ShowUserAssets();
$userAssets->getAssets();

Html::footer();
