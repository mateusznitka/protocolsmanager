<?php

class PluginProtocolsmanagerProfile extends CommonDBTM
{
    function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
        return self::createTabEntry(__('Protocols manager','protocolsmanager'));
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

        global $CFG_GLPI, $DB;
        $profile_id = $item->getID();
        self::showRightsForm($profile_id);
        return true;
    }

    static function showRightsForm($profile_id) {
        global $CFG_GLPI, $DB;

        $req = $DB->request(
            'glpi_plugin_protocolsmanager_profiles',
            ['profile_id' => $profile_id]);

        if ($row = $req->current()) {
            $plugin_conf = $row['plugin_conf'];
            $tab_access = $row['tab_access'];
            $my_assets = $row['my_assets'];
            $sign_protocol_form = $row['sign_protocol_form'];
        }

        if (count($req) == 0) {
            $edit_flag = 1;
            $plugin_conf ="";
            $tab_access ="";
            $my_assets ="";
        } else {
            $edit_flag = 0;
        }

        echo "<form name='profiles' action='" . $CFG_GLPI["root_doc"] . "/plugins/protocolsmanager/front/profile.form.php' method='post'>";
        echo "<div class='center'>";
        echo "<table class='tab_cadre_fixehov'>";
        echo "<tr class='tab_bg_5'><th colspan='2'>" . __('Protocols manager','protocolsmanager') . "</th></tr>";
        echo "<tr class='tab_bg_2'><td width=30%>" . __('Plugin configuration','protocolsmanager') . "</td><td>";
        Html::showCheckbox(['name' => 'plugin_conf', 'checked' => $plugin_conf, 'value' => 'w']);
        echo "</td></tr>";
        echo "<tr class='tab_bg_2'><td width=30%>" . __('Protocols manager tab access','protocolsmanager') . "</td><td>";
        Html::showCheckbox(['name' => 'tab_access', 'checked' => $tab_access, 'value' => 'w']);
        echo "</td></tr>";
        echo "<tr class='tab_bg_2'><td width=30%>" . __('My assets page access','protocolsmanager') . "</td><td>";
        Html::showCheckbox(['name' => 'my_assets', 'checked' => $my_assets, 'value' => 'w']);
        echo "</td></tr>";
        echo "<tr class='tab_bg_2'><td width=30%>" . __('Sign protocol page access','protocolsmanager') . "</td><td>";
        Html::showCheckbox(['name' => 'sign_protocol_form', 'checked' => $sign_protocol_form, 'value' => 'w']);
        echo "</td></tr>";
        echo "<tr class='tab_bg_5'><th colspan='2'>";
        echo "<input type='submit' class='submit' name='update'>";
        echo "<input type='hidden' name='profile_id' value='$profile_id'>";
        echo "<input type='hidden' name='edit_flag' value='$edit_flag'>";
        echo "</th></tr>";
        echo "</table>";
        Html::closeForm();
        echo "</div>";
    }

    static function updateRights() {
        global $DB;

        if ($_POST['edit_flag'] == 1) {
            $DB->insert('glpi_plugin_protocolsmanager_profiles', [
                    'profile_id' => $_POST['profile_id'],
                    'plugin_conf' => $_POST['plugin_conf'],
                    'tab_access' => $_POST['tab_access'],
                    'my_assets' => $_POST['my_assets'],
                    'sign_protocol_form' => $_POST['sign_protocol_form']
                ]
            );
        } else if ($_POST['edit_flag'] == 0){
            $DB->update('glpi_plugin_protocolsmanager_profiles', [
                'plugin_conf' => $_POST['plugin_conf'],
                'tab_access' => $_POST['tab_access'],
                'my_assets' => $_POST['my_assets'],
                'sign_protocol_form' => $_POST['sign_protocol_form']
            ], [
                    'profile_id' => $_POST['profile_id']
                ]
            );
        }
    }

}
?>