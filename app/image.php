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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_FILES["uploaded_file"])) {
        $file = $_FILES["uploaded_file"];
        if ($file["error"] === UPLOAD_ERR_OK) {
            $inputImagePath = $file["tmp_name"];
        }
    } else {
        $inputImagePath = $_REQUEST['image_url'];
    }

    // Output image file
    $targetFolder   = 'uploads/';
    $targetFilename = $targetFolder . uniqid() . '.jpg';

    // Desired crop dimensions
    $cropWidth  = $_REQUEST['width'];
    $cropHeight = $_REQUEST['height'];

    // Load the image
    $sourceImage = imagecreatefromjpeg($inputImagePath);

    // Get the current dimensions of the image
    $sourceWidth = imagesx($sourceImage);
    $sourceHeight = imagesy($sourceImage);

    // Calculate the crop position (centered in this example)
    $cropX = ($sourceWidth - $cropWidth) / 2;
    $cropY = ($sourceHeight - $cropHeight) / 2;

    // Create a new image with the desired crop dimensions
    $croppedImage = imagecreatetruecolor($cropWidth, $cropHeight);

    // Perform the crop
    imagecopy($croppedImage, $sourceImage, 0, 0, $cropX, $cropY, $cropWidth, $cropHeight);

    // Save the cropped image
    chmod($targetFolder, '0777');
    imagejpeg($croppedImage, $targetFilename);

    // Free up memory
    imagedestroy($sourceImage);
    imagedestroy($croppedImage);

    echo 'Cropped image saved  and uploaded successfully to ' . $targetFilename;
}
