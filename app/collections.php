<?php
error_reporting(1);
session_id('hsnqn5amdm8e811dfnhq6vmf3u');
session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE PATCH');

ini_set('display_errors', true);
require_once('config/app.php');
require_once('config/database.php');

if (!$_SESSION['LoggedInUser']) {
    echo json_encode(['code' => 403, 'message' => 'Unauthorized Request']);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_REQUEST['collectionId']) {
        $collectionId = $_REQUEST['collectionId'];
        $userId = $_SESSION['LoggedInUser']['id'];

        $fetchUserCollections = "SELECT * FROM user_collections WHERE user_id= '" . $userId . "' AND collection_id='" . $collectionId . "'";
        $isCollectionExists = mysqli_query($connection, $fetchUserCollections);

        if (mysqli_num_rows($isCollectionExists) > 0) {
            $row = mysqli_fetch_assoc($isCollectionExists);
            $colls = json_decode($row['selected_collections_json'], TRUE);
            if($colls) {
                echo json_encode(['selectedCols' => $colls]);
            }
            exit();
        } else {
            echo json_encode(['message' => 'not found', 'status' => 400]);
        }
    } else {
        echo json_encode(['message' => 'not found', 'status' => 400]);
    }
} else {

    if ($_REQUEST['selectedCols']) {
        $collectionId = $_REQUEST['collectionId'];
        $userId = $_SESSION['LoggedInUser']['id'];
        $deleteCollection = "DELETE FROM  user_collections WHERE user_id= '" . $userId . "' AND collection_id='" . $collectionId . "'";
        $isDeleted = mysqli_query($connection, $deleteCollection);

        $addCollection = "INSERT INTO `user_collections`(`user_id`, `collection_id`, `selected_collections_json`, `created_at`)
                            VALUES (
                            '" .  $userId . "',
                            '" . $collectionId . "',
                            '" . $_REQUEST['selectedCols'] . "',
                            '" . date("Y-m-d H:i:s") . "')";

        $addCollection = mysqli_query($connection, $addCollection);
    }
}
