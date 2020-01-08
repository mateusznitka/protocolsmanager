<?php

class PluginProtocolsmanagerProfile extends CommonDBTM
{

	//class PluginProtocolsmanagerProfile extends CommonGLPI
	
		function getTabNameForItem(CommonGLPI $item, $withtemplate=0)
		{
			return self::createTabEntry('Protocols manager');
		}
		/**
		 * This function is called from GLPI to render the form when the user click
		 *  on the menu item generated from getTabNameForItem()
		 */
		static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0)
		{
			echo "TODO";
			return true;
		}
	
}
?>