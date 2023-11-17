<?php
require_once('config/check_session.php');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE PATCH');
// require_once('classes/Api.php');

$_SESSION['last'] = time();

echo json_encode(['message' => $_SESSION]);

