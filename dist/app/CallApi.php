<?php
require_once('config/check_session.php');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE PATCH');
require_once('classes/Api.php');

if ($_SESSION['LoggedInUser']) {

    $api = new Api();


    $response = $api->setEndPoint($_REQUEST['endPoint'])->setMethod($_REQUEST['method'] ?? 'GET')
        ->setAccessToken($_SESSION['LoggedInUser']['access_token'])
        ->setParams($_REQUEST['params'])
        ->callApi()
        ->sendResponse();
} else {
    echo json_encode(['code' => 403, 'message' => 'Unauthorized Request']);
}
