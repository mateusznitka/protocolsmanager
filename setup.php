<?php

function plugin_version_protocolsmanager() {
    return [
        'name'           => "Protocols manager",
        'version'        => '2.0.0',
        'author'         => 'Mateusz Nitka',
        'license'        => 'GPLv3+',
        'homepage'       => 'https://github.com/mateusznitka/protocolsmanager',
        'minGlpiVersion' => '10.0',
    ];
}

function plugin_protocolsmanager_check_config() {
    return true;
}
 

function plugin_protocolsmanager_check_prerequisites() {
    if (version_compare(GLPI_VERSION, '10.0', '>=')) {
        return true;
    }
    echo "This plugin requires GLPI 10.0 or higher.";
    return false;
}

function plugin_init_protocolsmanager() {
	global $PLUGIN_HOOKS;

	$PLUGIN_HOOKS['csrf_compliant']['protocolsmanager'] = true;
	
	$PLUGIN_HOOKS['config_page']['protocolsmanager'] = 'front/config.form.php';
   
    Plugin::registerClass('PluginProtocolsmanagerGenerate', ['addtabon' => ['User']]);
    Plugin::registerClass('PluginProtocolsmanagerProfile', ['addtabon' => ['Profile']]);
    Plugin::registerClass('PluginProtocolsmanagerConfig', ['addtabon' => ['Config']]);

    $PLUGIN_HOOKS['add_css']['protocolsmanager'] = 'css/styles.css';
	
}

?>