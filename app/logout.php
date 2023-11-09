<?php
error_reporting(1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE PATCH');
session_start();

ini_set('display_errors', true);
require_once('config/app.php');

unset($_SESSION['userDetails']);
unset($_SESSION['access_token']);
unset($_SESSION['LoggedInUser']);

header('Location:' . APP_URL);

exit();
