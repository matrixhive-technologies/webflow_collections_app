<?php
error_reporting(1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE PATCH');
session_start();

ini_set('display_errors', true);
require_once('config/app.php');
require_once('config/database.php');

$existingUser = "SELECT * FROM app_users WHERE email= '" . $_REQUEST["email"] . "' and password='" . md5($_REQUEST['password']) . "'";

$existingUser = mysqli_query($connection, $existingUser);

if (mysqli_num_rows($existingUser) == 1) {
    $row = mysqli_fetch_array($existingUser);
    $_SESSION['LoggedInUser'] = [
        'id'           => $row['id'],
        'first_name'   => $row['first_name'],
        'last_name'    => $row['last_name'],
        'email'        => $row['email'],
        'access_token' => $row['access_token'],
    ];
    $user = $_SESSION['LoggedInUser'];
    $user['access_token'] = session_id().'090##934'.time();

    echo json_encode(['success' => true, 'user' => $user,'message' => 'Login Success!']);
   
} else {
    echo json_encode(['success' => false, 'message' => 'Login Failed!']);
    
}
exit;