<?php

include('../../../inc/includes.php');

if (!Session::haveRight("config", READ)) {
    http_response_code(403);
    exit;
}

header('Content-Type: application/json');
echo json_encode(['token' => Session::getNewCSRFToken()]);
