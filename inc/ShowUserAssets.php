<?php

class ShowUserAssets
{
    private $userID;
    private $tablesForUser;

    public function __construct()
    {
        $this->setUserTables();
        $this->setUserId();
    }

    public function getAssets()
    {
        $this->headerHtml();
        $this->tableContentHtml($this->setContentData());

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
        global $DB;
        $result = [];
        foreach($this->tablesForUser as $tableName)
        {
            $iterator_params = [
                'FROM' => $tableName,
                'WHERE' => ['users_id' => $this->userID]
            ];
            $iterator_params['WHERE']['is_template'] = 0;
            $iterator_params['WHERE']['is_deleted'] = 0;
            $item_iterator = $DB->request($iterator_params);
            $data = $item_iterator->current();
            if(count($data) > 0){
                $data['type'] = getItemTypeForTable($tableName);
                array_push($result, $data);
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