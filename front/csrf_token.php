<?php

// Any logged-in user already has a CSRF token in the page, so login is enough here.
// Both the config screen and the "Assign devices" modal use this endpoint.
Session::checkLoginUser();

header('Content-Type: application/json');
echo json_encode(['token' => plugin_protocolsmanager_csrf_token()]);
