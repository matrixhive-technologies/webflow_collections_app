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
	$fetchRatio = "SELECT * FROM collection_aspect_ratio WHERE site_id= '" . mysqli_real_escape_string($app_connection, $_REQUEST['siteId']) . "' and collection_id='" . mysqli_real_escape_string($app_connection, $_REQUEST['collectionID']) . "'";


	$ratioExists = mysqli_query($app_connection, $fetchRatio);
    $data = [
        [ 'width' => '100 px' , 'height' => '100 px', 'aspectRatio' => '1:1'],
        ['width' => '300 px' , 'height' => '300 px', 'aspectRatio' => '4:3'],
        ['width' => '1600 px' , 'height' => '900 px', 'aspectRatio' => '16:9'],
    ];
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
                'creaetd_on'    => $row['creaetd_at'],
				'aspectRatio'   => $aspectRatioWidth . ':' . $aspectRatioHeight
			];
		}
		echo json_encode(['message' => 'fetch successful', 'listData' => $data]);
	} else {
		echo json_encode(['message' => 'no records', 'listData' => $data]);
	}
} else if ($_REQUEST['action'] == 'delete') {
	$deleteRatio = "DELETE FROM collection_aspect_ratio WHERE id= '" . mysqli_real_escape_string($app_connection,$_REQUEST['itemId']) . "'";
	$isDeleted = mysqli_query($app_connection, $deleteRatio);
	if ($isDeleted) {
		echo json_encode(['message' => 'Aspect ratio deleted', 'code' => 200]);
	} else {
		echo json_encode(['message' => 'Error deleting aspect ratio', 'code' => 400]);
	}
} else if ($_REQUEST['action'] == 'save') {

	$addRatio = "INSERT INTO `collection_aspect_ratio`(`site_id`,`collection_id`, `width`, `height`, `created_at`) 
								VALUES (
								'" . mysqli_real_escape_string($app_connection,$_REQUEST['selectedSiteId']) . "',
								'" . mysqli_real_escape_string($app_connection,$_REQUEST['collection_id']) . "',
								'" . mysqli_real_escape_string($app_connection,$_REQUEST['width']) . "',
								'" . mysqli_real_escape_string($app_connection,$_REQUEST['height']) . "',
								'" . date("Y-m-d H:i:s") . "')";

                               
	if (mysqli_query($app_connection, $addRatio)) {
		echo json_encode(['message' => 'Aspect ratio saved', 'code' => 200]);
	} else {
		echo json_encode(['message' => 'Error saving aspect ratio  '.mysqli_error($app_connection), 'code' => 400]);
	}
}
