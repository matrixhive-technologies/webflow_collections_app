<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE PATCH');
require_once('classes/Api.php');
session_start();

$api = new Api();

$response = $api->setEndPoint($_REQUEST['endPoint'])->setMethod($_REQUEST['method'] ?? 'GET')
    ->setAccessToken($_SESSION['access_token'] ?? '5e703545d61fd829d201e040fa19a2153342b09e37b2d801957463ab3a6ca4c3')
    ->setParams($_REQUEST['params'])
    ->callApi()
    ->sendResponse();
