<?php
require_once('config/check_session.php');
error_reporting(1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE PATCH');

ini_set('display_errors', true);
require_once('config/app.php');
require_once('config/database.php');

if (!$_SESSION['LoggedInUser']) {
    echo json_encode(['code' => 403, 'message' => 'Unauthorized Request']);
}

# Delete the Cache Results
$deleteUserCache = "DELETE FROM  cached_results WHERE user_id= '" . $_SESSION['LoggedInUser']['id'] . "'";
$isDeleted = mysqli_query($connection, $deleteUserCache);

if ($isDeleted) {
    echo json_encode(['code' => 200, 'message' => 'Cache Removed Successfully']);
} else {
    echo json_encode(['code' => 400, 'message' => 'Something went wrong']);
}
