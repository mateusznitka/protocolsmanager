<?php

include('../../../inc/includes.php');

Session::checkLoginUser();

header('Content-Type: application/json');

global $DB, $CFG_GLPI;

$itemtype = $_GET['itemtype'] ?? '';
$search   = trim($_GET['search'] ?? '');
$user_id  = (int) ($_GET['user_id'] ?? 0);

$is_custom_asset = class_exists('Glpi\\Asset\\Asset') && is_subclass_of($itemtype, 'Glpi\\Asset\\Asset');
if (empty($itemtype) || !in_array($itemtype, $CFG_GLPI['assignable_types'] ?? [], true)) {
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

$itemtable = $item->getTable();

$where = [];
// For GLPI 11+ custom assets on shared table: filter by definition FK
if ($is_custom_asset && method_exists($itemtype, 'getDefinition')) {
    $_def_fk = \Glpi\Asset\AssetDefinition::getForeignKeyField();
    if ($DB->fieldExists($itemtable, $_def_fk)) {
        $_def = $itemtype::getDefinition();
        $where["$itemtable.$_def_fk"] = $_def->getID();
    }
}
// Types registered by other plugins do not always have both columns
$has_name   = $DB->fieldExists($itemtable, 'name');
$has_serial = $DB->fieldExists($itemtable, 'serial');
if ($search !== '') {
    $or = [];
    if ($has_name) {
        $or["$itemtable.name"] = ['LIKE', "%$search%"];
    }
    if ($has_serial) {
        $or["$itemtable.serial"] = ['LIKE', "%$search%"];
    }
    if (!$or) {
        echo json_encode([]);
        exit;
    }
    $where['OR'] = $or;
}
if ($item->maybeTemplate()) {
    $where["$itemtable.is_template"] = 0;
}
if ($item->maybeDeleted()) {
    $where["$itemtable.is_deleted"] = 0;
}
if ($item->isEntityAssign()) {
    $where[] = getEntitiesRestrictCriteria($itemtable, '', '', $item->maybeRecursive());
}
if ($user_id) {
    $where['NOT'] = ["$itemtable.users_id" => $user_id];
}

$results = [];
foreach ($DB->request([
    'SELECT'   => array_merge(
        ["$itemtable.id", "$itemtable.users_id", "glpi_users.name AS current_user_name"],
        $has_name ? ["$itemtable.name"] : [],
        $has_serial ? ["$itemtable.serial"] : []
    ),
    'FROM'     => $itemtable,
    'LEFT JOIN' => [
        'glpi_users' => ['ON' => [$itemtable => 'users_id', 'glpi_users' => 'id']],
    ],
    'WHERE'    => $where,
    'ORDER'    => $has_name ? "$itemtable.name" : "$itemtable.id",
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
