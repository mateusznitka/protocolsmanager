<?php

class PluginProtocolsmanagerProfile extends Profile
{
    public static $rightname = 'profile';

    public static function getAllRights()
    {
        return [
            [
                'itemtype' => 'PluginProtocolsmanagerConfig',
                'label'    => __('Plugin configuration', 'protocolsmanager'),
                'field'    => 'plugin_protocolsmanager_config',
                'rights'   => [READ => __('Access')],
            ],
            [
                'itemtype' => 'PluginProtocolsmanagerGenerate',
                'label'    => __('Protocols tab access', 'protocolsmanager'),
                'field'    => 'plugin_protocolsmanager_tab',
                'rights'   => [READ => __('Access')],
            ],
        ];
    }

    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if ($item instanceof Profile) {
            if ($item->fields['interface'] == 'central') {
                return self::createTabEntry('Protocols manager');
            }
        }
        return '';
    }

    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if ($item instanceof Profile) {
            $profile = new self();
            self::addDefaultProfileInfos($item->getID(), [
                'plugin_protocolsmanager_config' => 0,
                'plugin_protocolsmanager_tab'    => 0,
            ]);
            $profile->showForm($item->fields['id']);
        }
        return true;
    }

    public static function addDefaultProfileInfos($profiles_id, $rights)
    {
        $profileRight = new ProfileRight();
        foreach ($rights as $right => $value) {
            if (!countElementsInTable('glpi_profilerights', ['profiles_id' => $profiles_id, 'name' => $right])) {
                $profileRight->add([
                    'profiles_id' => $profiles_id,
                    'name'        => $right,
                    'rights'      => $value,
                ]);
                $_SESSION['glpiactiveprofile'][$right] = $value;
            }
        }
    }

    public static function createFirstAccess($profiles_id)
    {
        self::addDefaultProfileInfos($profiles_id, [
            'plugin_protocolsmanager_config' => ALLSTANDARDRIGHT,
            'plugin_protocolsmanager_tab'    => READ,
        ]);
    }

    public function showForm($ID, $options = [])
    {
        $canedit = Session::haveRightsOr(self::$rightname, [CREATE, UPDATE, PURGE]);

        echo "<div class='firstbloc'>";
        if ($canedit) {
            $profile = new Profile();
            echo "<form method='post' action='" . $profile->getFormURL() . "'>";
        }

        $profile = new Profile();
        $profile->getFromDB($ID);

        $profile->displayRightsChoiceMatrix(self::getAllRights(), [
            'canedit'       => $canedit,
            'default_class' => 'tab_bg_2',
            'title'         => 'Protocols manager',
        ]);

        if ($canedit) {
            echo "<div class='center'>";
            echo Html::hidden('id', ['value' => $ID]);
            echo Html::submit(_sx('button', 'Save'), ['name' => 'update']);
            echo "</div>\n";
            Html::closeForm();
        }
        echo "</div>";
        return true;
    }
}
