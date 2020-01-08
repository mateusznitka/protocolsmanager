<?php


/**
 * Get the name and the version of the plugin - Needed
 */
function plugin_version_protocolsmanager() 
{
	return array('name'           => "Protocols manager",
                'version'        => '0.8',
                'author'         => 'Mateusz Nitka',
                'license'        => 'GPLv3+',
                'homepage'       => 'https://github.com/mateusznitka',
                'minGlpiVersion' => '9.0');
}

function plugin_protocolsmanager_check_config() 
{
    return true;
}
 
/**
 * Check if the prerequisites of the plugin are satisfied - Needed
 */
function plugin_protocolsmanager_check_prerequisites() 
{ 
		if (GLPI_VERSION>=9.0){
                return true;
        } else {
                echo "GLPI version NOT compatible. Requires GLPI 9.0";
        }
}

function plugin_init_protocolsmanager() 
{
	global $PLUGIN_HOOKS;

	$PLUGIN_HOOKS['csrf_compliant']['protocolsmanager'] = true;
	
	$PLUGIN_HOOKS['config_page']['protocolsmanager'] = 'front/config.form.php';
   
	Plugin::registerClass('PluginProtocolsmanagerGenerate', array('addtabon' => array('User')));
	
	Plugin::registerClass('PluginProtocolsmanagerProfile', array('addtabon' => array('Profile')));
	
	Plugin::registerClass('PluginProtocolsmanagerConfig', array('addtabon' => array('Config')));
	
}

?>