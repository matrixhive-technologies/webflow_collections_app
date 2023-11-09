<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE PATCH');
require_once('classes/Api.php');
session_start();

$api = new Api();

$response = $api->setEndPoint($_REQUEST['endPoint'])->setMethod($_REQUEST['method'] ?? 'GET')
    ->setAccessToken($_SESSION['access_token'] ?? 'ab2eddb1c17d4099c97a78d160a1838c68baa17319d45a908540ab9d13dcc7b7')
    ->setParams($_REQUEST['params'])
    ->callApi()
    ->sendResponse();
