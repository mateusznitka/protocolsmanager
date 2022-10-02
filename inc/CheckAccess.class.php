<?php
class CheckAccess
{
    static function checkRightsToMyAssets() {
        global $DB, $CFG_GLPI;
        $active_profile = $_SESSION['glpiactiveprofile']['id'];
        $req = $DB->request('glpi_plugin_protocolsmanager_profiles',
            ['profile_id' => $active_profile]);

        if ($row = $req->current()) {
            $my_assets = $row["my_assets"];
        } else {
            $my_assets = "";
        }

        if($my_assets != 'w')
        {
            echo "<div align='center'><br><img src='".$CFG_GLPI['root_doc']."/pics/warning.png'><br>".__("Access denied")."</div>";
            exit();
        }
    }
}