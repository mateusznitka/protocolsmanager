<?php
class CheckAccess
{
	static function checkRightsToMyAssets()
	{
		self::checkAcces("my_assets");
	}
	
	static function checkRightsToSignProtocolsPage()
	{
		self::checkAcces("sign_protocol_form");
	}
	
	private static function checkAcces(string $page) :void
	{
		global $DB, $CFG_GLPI;
		$active_profile = $_SESSION['glpiactiveprofile']['id'];
		$req = $DB->request('glpi_plugin_protocolsmanager_profiles',
			['profile_id' => $active_profile]);

		if ($row = $req->current()) {
			$my_assets = $row[$page];
		} else {
			$my_assets = "";
		}
		
		if($my_assets != 'w')
		{
			echo "<div align='center'><br><img src='".$CFG_GLPI['root_doc']."/pics/warning.png'><br>".__('Access denied')."</div>";
			exit();
		}
	}
}