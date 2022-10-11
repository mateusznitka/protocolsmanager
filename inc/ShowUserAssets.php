<?php
require_once dirname(__DIR__) . '/inc/CheckAccess.class.php';

class ShowUserAssets
{
    private $userID;
    private $tablesForUser;
    private $user_field;
    private $container_name;

    public function __construct()
    {
        $this->setFieldUser();
        $this->setUserTables();
        $this->setUserId();
    }

    public function getAssets()
    {
        CheckAccess::checkRightsToMyAssets();
        $this->headerHtml();
        $this->tableContentHtml($this->setContentData());

    }

    private function setFieldUser()
    {
        global $DB;
        $query = (['FROM' => 'glpi_plugin_protocolsmanager_settings', 'WHERE' => ['id' => 1]]);
        $result = $DB->request($query)->current();
        if(strpos( $result['user_fields'],','))
        {
            $this->user_field = isset(explode(',',$result['user_fields'])[0]) ? explode(',',$result['user_fields'])[0] : '';
            $this->container_name = isset(explode(',',$result['user_fields'])[1]) ? explode(',',$result['user_fields'])[1] : '';
        }else{
            $this->user_field = $result['user_fields'];
            $this->container_name = '';
        }

    }

    private function headerHtml()
    {

        $header = "<div class='spaced'><table class='tab_cadre_fixehov' id='additional_table' style='text-align: center'>";
        $header .= "<th>".__('Asset')."</th>";
        $header .= "<th>".__('Name')."</th>";
        $header .= "<th>".__('Serial number')."</th>";
        $header .= "<th>".__('Created date')."</th>";
        echo $header;
    }

    private function setContentData()//: array
    {
        global $DB, $CFG_GLPI;
        $result = [];
        $fieldsitemtableprefix = 'glpi_plugin_fields_';
        $item_type_user = $CFG_GLPI['linkuser_types'];

        foreach($item_type_user as $itemtype) {
            if (!($item = getItemForItemtype($itemtype))) {
                continue;
            }
            if ($item->canView()) {
                $itemtable = getTableForItemType($itemtype);
                $fieldsitemtablewithuser = strtolower("$fieldsitemtableprefix" . "$itemtype" . "$this->container_name" . 's');

                if (($this->user_field == 'users_id') or ($this->user_field == 'users_id_tech')) {
                    $iterator_params = [
                        'FROM' => $itemtable,
                        'WHERE' => [$this->user_field => $this->userID]
                    ];
                }
                else {
                    $sub_query = new QuerySubQuery([
                        'SELECT' => 'items_id',
                        'FROM' => $fieldsitemtablewithuser,
                        'WHERE' => ['itemtype' => $itemtype, $this->user_field => $this->userID]
                    ]);

                    $iterator_params = [
                        'FROM' => $itemtable,
                        'WHERE' => ['id' => $sub_query]
                    ];
                }
                if ($item->maybeTemplate()) {
                    $iterator_params['WHERE']['is_template'] = 0;
                }
                
                if ($item->maybeDeleted()) {
                    $iterator_params['WHERE']['is_deleted'] = 0;
                }
                
                $item_iterator = $DB->request($iterator_params);
                $data = $item_iterator->current();
                if(count($data) > 0){
                    $data['type'] = getItemTypeForTable($tableName);
                    array_push($result, $data);
                }
            }
        }
        return $result;
    }

    private function tableContentHtml(array $content): void
    {
        $tableContent = '';
        foreach ($content as $key=>$cnt)
        {
            $tableContent .= "<tr>
                                <td>". $cnt['type']."</td>
                                <td>". $cnt['name']."</td>
                                <td>". $cnt['serial']."</td>
                                <td>". $cnt['date_creation']."</td>
                            </tr>";
        }
        $tableContent .= '</table></div>';
        echo $tableContent;
    }

    private function setUserTables()
    {
        global $CFG_GLPI;
        $this->tablesForUser = [];
        foreach ($CFG_GLPI['linkuser_types'] as $type)
        {
            $table = getTableForItemType($type);
            array_push($this->tablesForUser, $table);
        }
    }

    private function setUserId()
    {
        $this->userID = Session::getLoginUserID();
    }



}