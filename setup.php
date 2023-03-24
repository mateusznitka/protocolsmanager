<?php

function plugin_version_protocolsmanager() {
	return array('name'				=> __('Protocols manager','protocolsmanager'),
				'version'			=> '1.4.3.1',
				'author'			=> 'Mateusz Nitka,Michał Panasiewicz,Artur Barzdo',
				'license'			=> 'GPLv3+',
				'homepage'			=> 'https://github.com/Wolvverine/protocolsmanager',
				'minGlpiVersion'	=> '10.0');
}

function plugin_protocolsmanager_check_config() {
    return true;
}


function plugin_protocolsmanager_check_prerequisites() {
		if (GLPI_VERSION>=10.0){
			return true;
		} else {
			echo __('GLPI version NOT compatible. Requires GLPI 10.0','protocolsmanager');
		}
}

function plugin_init_protocolsmanager() {
	global $PLUGIN_HOOKS;
	
	$PLUGIN_HOOKS['redefine_menus']['protocolsmanager'] = 'plugin_protocolsmanager_redefine_menus';
	
	$PLUGIN_HOOKS['csrf_compliant']['protocolsmanager'] = true;
	
	$PLUGIN_HOOKS['config_page']['protocolsmanager'] = 'front/config.form.php';
	
	$PLUGIN_HOOKS['protocols_page']['protocolsmanager'] = 'front/protocols.form.php';
	
	Plugin::registerClass('PluginProtocolsmanagerGenerate', array('addtabon' => array('User')));
	
	Plugin::registerClass('PluginProtocolsmanagerProfile', array('addtabon' => array('Profile')));
	
	Plugin::registerClass('PluginProtocolsmanagerConfig', array('addtabon' => array('Config')));
	
	Plugin::registerClass('PluginProtocolsmanagerReminder');
	
	$PLUGIN_HOOKS['add_css']['protocolsmanager'] = 'css/styles.css';
	
}

?>
