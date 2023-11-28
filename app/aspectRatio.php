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

# List Aspect Ratio Here.
if ($_REQUEST['action'] == 'list') {
    $fetchRatio = "SELECT * FROM collection_aspect_ratio WHERE site_id= '" . $_REQUEST['siteId'] . "' and collection_id='" . $_REQUEST['collectionID'] . "'";


    $ratioExists = mysqli_query($connection, $fetchRatio);

    if (mysqli_num_rows($ratioExists) > 0) {

        while ($row = mysqli_fetch_assoc($ratioExists)) {
            $gcd = function ($a, $b) use (&$gcd) {
                return ($b == 0) ? $a : $gcd($b, $a % $b);
            };

            $divisor = $gcd($row['width'], $row['height']);
            $aspectRatioWidth = $row['width'] / $divisor;
            $aspectRatioHeight = $row['height'] / $divisor;


            $data[] = [
                'id'            => $row['id'],
                'width'         => $row['width'] . 'px',
                'height'        => $row['height'] . 'px',
                'site_id'       => $row['site_id'],
                'collection_id' => $row['collection_id'],
                'aspectRatio'   => $aspectRatioWidth . ':' . $aspectRatioHeight
            ];
        }
        echo json_encode(['message' => 'fetch successful', 'listData' => $data]);
    } else {
        echo json_encode(['message' => 'no records', 'listData' => []]);
    }
} else if ($_REQUEST['action'] == 'delete') {
    $deleteRatio = "DELETE FROM collection_aspect_ratio WHERE id= '" . $_REQUEST['itemId'] . "'";
    $isDeleted = mysqli_query($connection, $deleteRatio);
    if ($isDeleted) {
        echo json_encode(['message' => 'Aspect Ratio Deleted Successfully', 'code' => 200]);
    } else {
        echo json_encode(['message' => 'Something went wrong while deleting', 'code' => 400]);
    }
} else if ($_REQUEST['action'] == 'save') {

    $addRatio = "INSERT INTO `collection_aspect_ratio`(`site_id`,`collection_id`, `width`, `height`, `created_at`) 
                                VALUES (
                                '" . $_REQUEST['selectedSiteId'] . "',
                                '" . $_REQUEST['collection_id'] . "',
                                '" . $_REQUEST['width'] . "',
                                '" . $_REQUEST['height'] . "',
                                '" . date("Y-m-d H:i:s") . "')";

    if (mysqli_query($connection, $addRatio)) {
        echo json_encode(['message' => 'Aspect Ratio Saved Successfully', 'code' => 200]);
    } else {
        echo json_encode(['message' => 'Aspect Ratio Saved Failed', 'code' => 400]);
    }
}
