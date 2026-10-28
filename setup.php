<?php

// Supported GLPI versions: MIN <= GLPI < MAX (MAX is exclusive), i.e. all of 11.x and 12.x.
define('PLUGIN_PROTOCOLSMANAGER_MIN_GLPI', '11.0.0');
define('PLUGIN_PROTOCOLSMANAGER_MAX_GLPI', '13.0.0');

function plugin_version_protocolsmanager() {
    return [
        'name'           => "Protocols manager",
        'version'        => '2.2.0',
        'author'         => 'Mateusz Nitka',
        'license'        => 'GPLv3+',
        'homepage'       => 'https://github.com/mateusznitka/protocolsmanager',
        'requirements'   => [
            'glpi' => [
                'min' => PLUGIN_PROTOCOLSMANAGER_MIN_GLPI,
                'max' => PLUGIN_PROTOCOLSMANAGER_MAX_GLPI,
            ],
        ],
    ];
}

// GLPI 12 no longer uses CSRF tokens (it checks Sec-Fetch-Site / Origin) and Session::getNewCSRFToken()
// is deprecated there. GLPI 11 still requires the token.
function plugin_protocolsmanager_csrf_token(): string {
    if (version_compare(GLPI_VERSION, '12', '>=')) {
        return '';
    }
    return Session::getNewCSRFToken();
}

function plugin_protocolsmanager_csrf_field(): string {
    $token = plugin_protocolsmanager_csrf_token();
    return $token === '' ? '' : '<input type="hidden" name="_glpi_csrf_token" value="' . $token . '">';
}

function plugin_protocolsmanager_check_config() {
    return true;
}

function plugin_protocolsmanager_getMenuContent() {
    global $CFG_GLPI;
    $page = $CFG_GLPI['root_doc'] . '/plugins/protocolsmanager/front/config.form.php';
    return [
        'title' => 'Protocols Manager',
        'icon'  => 'ti ti-file-description',
        'page'  => $page,
        'links' => [
            'search' => $page,
        ],
    ];
}
 

function plugin_protocolsmanager_check_prerequisites() {
    if (
        version_compare(GLPI_VERSION, PLUGIN_PROTOCOLSMANAGER_MIN_GLPI, '>=')
        && version_compare(GLPI_VERSION, PLUGIN_PROTOCOLSMANAGER_MAX_GLPI, '<')
    ) {
        return true;
    }
    echo "This plugin requires GLPI " . PLUGIN_PROTOCOLSMANAGER_MIN_GLPI . " or higher and lower than " . PLUGIN_PROTOCOLSMANAGER_MAX_GLPI . ".";
    return false;
}

function plugin_init_protocolsmanager() {
	global $PLUGIN_HOOKS;

	$PLUGIN_HOOKS['csrf_compliant']['protocolsmanager'] = true;
	$PLUGIN_HOOKS['config_page']['protocolsmanager']   = 'front/config.form.php';
	$PLUGIN_HOOKS['menu_toadd']['protocolsmanager']    = ['config' => 'PluginProtocolsmanagerConfig'];
   
    Plugin::registerClass('PluginProtocolsmanagerGenerate', ['addtabon' => ['User']]);
    Plugin::registerClass('PluginProtocolsmanagerProfile', ['addtabon' => ['Profile']]);
    Plugin::registerClass('PluginProtocolsmanagerConfig', ['addtabon' => ['Config']]);

    $PLUGIN_HOOKS['add_css']['protocolsmanager'] = 'css/styles.css';
	
}

?>