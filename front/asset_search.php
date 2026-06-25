<?php

include('../../../inc/includes.php');

Session::checkLoginUser();

header('Content-Type: application/json');

global $DB, $CFG_GLPI;

$itemtype = $_GET['itemtype'] ?? '';
$search   = trim($_GET['search'] ?? '');
$user_id  = (int) ($_GET['user_id'] ?? 0);

if (empty($itemtype) || !in_array($itemtype, $CFG_GLPI['linkuser_types'])) {
    echo json_encode([]);
    exit;
}

if (!class_exists($itemtype) || !$itemtype::canUpdate()) {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$item = getItemForItemtype($itemtype);
if (!$item) {
    echo json_encode([]);
    exit;
}

$itemtable = getTableForItemType($itemtype);

$where = [];
if ($search !== '') {
    $where['OR'] = [
        "$itemtable.name"   => ['LIKE', "%$search%"],
        "$itemtable.serial" => ['LIKE', "%$search%"],
    ];
}
if ($item->maybeTemplate()) {
    $where["$itemtable.is_template"] = 0;
}
if ($item->maybeDeleted()) {
    $where["$itemtable.is_deleted"] = 0;
}
if ($user_id) {
    $where['NOT'] = ["$itemtable.users_id" => $user_id];
}

$results = [];
foreach ($DB->request([
    'SELECT'   => ["$itemtable.id", "$itemtable.name", "$itemtable.serial", "$itemtable.users_id", "glpi_users.name AS current_user_name"],
    'FROM'     => $itemtable,
    'LEFT JOIN' => [
        'glpi_users' => ['ON' => [$itemtable => 'users_id', 'glpi_users' => 'id']],
    ],
    'WHERE'    => $where,
    'ORDER'    => "$itemtable.name",
    'LIMIT'    => 50,
]) as $row) {
    $results[] = [
        'id'                => (int) $row['id'],
        'name'              => $row['name'] ?? '',
        'serial'            => $row['serial'] ?? '',
        'current_user_name' => $row['current_user_name'] ?? '',
    ];
}

echo json_encode($results);
