<?php

include('../../../inc/includes.php');

Session::haveRight("config", UPDATE);

Html::header(PluginProtocolsmanagerConfig::getTypeName(1),
             $_SERVER['PHP_SELF'], "plugins", "protocolsmanager", "config");

$PluginProtocolsmanagerConfig = new PluginProtocolsmanagerConfig();

global $CFG_GLPI;
$base_url = $CFG_GLPI['root_doc'] . '/plugins/protocolsmanager/front/config.form.php';

if (!empty($_REQUEST['save'])) {
    $PluginProtocolsmanagerConfig::saveConfigs();
    Html::redirect($base_url);
}

if (!empty($_REQUEST['delete'])) {
    $PluginProtocolsmanagerConfig::deleteConfigs();
    Html::redirect($base_url);
}

if (!empty($_REQUEST['save_email'])) {
    $PluginProtocolsmanagerConfig::saveEmailConfigs();
    Html::redirect($base_url . '?tab=email');
}

if (!empty($_REQUEST['delete_email'])) {
    $PluginProtocolsmanagerConfig::deleteEmailConfigs();
    Html::redirect($base_url . '?tab=email');
}

if (!empty($_REQUEST['toggle_default'])) {
    $PluginProtocolsmanagerConfig::toggleDefault((int)$_REQUEST['id']);
    Html::redirect($base_url);
}

$PluginProtocolsmanagerConfig->showFormProtocolsmanager();

Html::footer();
