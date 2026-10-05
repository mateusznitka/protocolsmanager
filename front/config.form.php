<?php

include('../../../inc/includes.php');

Session::checkLoginUser();
Session::checkRight('plugin_protocolsmanager_config', READ);

$PluginProtocolsmanagerConfig = new PluginProtocolsmanagerConfig();

global $CFG_GLPI;
$base_url = $CFG_GLPI['root_doc'] . '/plugins/protocolsmanager/front/config.form.php';

if (!empty($_POST['save'])) {
    $PluginProtocolsmanagerConfig::saveConfigs();
    Html::redirect($base_url);
}

if (!empty($_POST['delete'])) {
    $PluginProtocolsmanagerConfig::deleteConfigs();
    Html::redirect($base_url);
}

if (!empty($_POST['save_email'])) {
    $PluginProtocolsmanagerConfig::saveEmailConfigs();
    Html::redirect($base_url . '?tab=email');
}

if (!empty($_POST['delete_email'])) {
    $PluginProtocolsmanagerConfig::deleteEmailConfigs();
    Html::redirect($base_url . '?tab=email');
}

if (!empty($_POST['toggle_default'])) {
    $PluginProtocolsmanagerConfig::toggleDefault((int)$_POST['id']);
    Html::redirect($base_url);
}

Html::header(PluginProtocolsmanagerConfig::getTypeName(1),
             $_SERVER['PHP_SELF'], "plugins", "protocolsmanager", "config");

$PluginProtocolsmanagerConfig->showFormProtocolsmanager();

Html::footer();
