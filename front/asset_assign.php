<?php

include('../../../inc/includes.php');

Session::checkLoginUser();

header('Content-Type: application/json');

global $CFG_GLPI;

$itemtype = $_POST['itemtype'] ?? '';
$items_id = (int) ($_POST['items_id'] ?? 0);
$user_id  = (int) ($_POST['user_id'] ?? 0);

if (empty($itemtype) || !in_array($itemtype, $CFG_GLPI['linkuser_types']) || empty($items_id) || empty($user_id)) {
    http_response_code(400);
    echo json_encode(['success' => false]);
    exit;
}

if (!class_exists($itemtype) || !$itemtype::canUpdate()) {
    http_response_code(403);
    echo json_encode(['success' => false]);
    exit;
}

$item = new $itemtype();
$success = $item->update(['id' => $items_id, 'users_id' => $user_id]);

echo json_encode(['success' => (bool) $success]);
