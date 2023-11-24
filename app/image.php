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

    # Unlink Image goes here.
    if ($_REQUEST['action'] == 'unlinkImg') {
        if (!empty($_REQUEST['img_path'])) {
            unlink($_REQUEST['img_path']);
            echo json_encode(['code' => 200, 'message' => 'Image Unlinked successfully']);
        } else {
            echo json_encode(['code' => 400, 'message' => 'Something went wrong']);
        }
        return false;
    }

    if (isset($_FILES["uploaded_file"])) {
        $file = $_FILES["uploaded_file"];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($_FILES["uploaded_file"]['tmp_name']);
                break;
            case 'png':
                $image = imagecreatefrompng($_FILES["uploaded_file"]['tmp_name']);
                break;
            case 'gif':
                $image = imagecreatefromgif($_FILES["uploaded_file"]['tmp_name']);
                break;
            case 'webp':
                $image = imagecreatefromwebp($_FILES["uploaded_file"]['tmp_name']);
                break;
            default:
                die('Unsupported image type');
        }

        // Desired crop dimensions
        if (!empty($_REQUEST['aspectRatio'])) {
            $aspectRatioArr = explode('/', $_REQUEST['aspectRatio']);
            $width  = $aspectRatioArr[0];
            $height = $aspectRatioArr[1];

            $cropWidth  = $width;
            $cropHeight = $height;

            // Load the image
            $sourceImage = $image;

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


            $outputPath = 'uploads/' . uniqid() . '.webp';

            $outputFolder = dirname($outputPath);
            if (!is_dir($outputFolder)) {
                $isFolderCreated =  mkdir($outputFolder, 0755, true);

                if ($isFolderCreated === false) {
                    $error = error_get_last();
                    echo 'Failed to create directory: ' . $error['message'];
                }
                chmod($outputFolder, 0755);
            }

            // Save the image as WebP
            $imageResponse = imagewebp($croppedImage, $outputPath);

            // Free up memory
            imagedestroy($croppedImage);
            imagedestroy($image);

            $webPImageUrl = UPLOAD_PATH . $outputPath;
            if ($imageResponse) {
                echo json_encode([
                    'code' => 200,
                    'url' => $webPImageUrl,
                    'outputPath' => $outputPath,
                ]);
            } else {
                echo json_encode(['code' => 400]);
            }
        }

        // $outputPath = 'uploads/' . uniqid() . '.webp';

        // $outputFolder = dirname($outputPath);
        // if (!is_dir($outputFolder)) {
        //     $isFolderCreated =  mkdir($outputFolder, 0755, true);

        //     if ($isFolderCreated === false) {
        //         $error = error_get_last();
        //         echo 'Failed to create directory: ' . $error['message'];
        //     }
        //     chmod($outputFolder, 0755);
        // }

        // // Save the image as WebP
        // $imageResponse = imagewebp($image, $outputPath);

        // // Free up memory
        // imagedestroy($image);

        // $webPImageUrl = UPLOAD_PATH . $outputPath;
        // if ($imageResponse) {
        //     echo json_encode([
        //         'code' => 200,
        //         'url' => $webPImageUrl,
        //         'outputPath' => $outputPath,
        //     ]);
        // } else {
        //     echo json_encode(['code' => 400]);
        // }












        // if ($file["error"] === UPLOAD_ERR_OK) {
        //     $inputImagePath = $file["tmp_name"];

        //     // Output image file
        //     $targetFolder   = 'uploads/';
        //     $targetFilename = $targetFolder . uniqid() . '.jpg';

        //     // Desired crop dimensions
        //     $cropWidth  = $_REQUEST['width'] ?? 200;
        //     $cropHeight = $_REQUEST['height'] ?? 150;

        //     // Load the image
        //     $sourceImage = imagecreatefromjpeg($inputImagePath);

        //     // Get the current dimensions of the image
        //     $sourceWidth = imagesx($sourceImage);
        //     $sourceHeight = imagesy($sourceImage);

        //     // Calculate the crop position (centered in this example)
        //     $cropX = ($sourceWidth - $cropWidth) / 2;
        //     $cropY = ($sourceHeight - $cropHeight) / 2;

        //     // Create a new image with the desired crop dimensions
        //     $croppedImage = imagecreatetruecolor($cropWidth, $cropHeight);

        //     // Perform the crop
        //     imagecopy($croppedImage, $sourceImage, 0, 0, $cropX, $cropY, $cropWidth, $cropHeight);

        //     // Save the cropped image
        //     chmod($targetFolder, '0777');
        //     imagejpeg($croppedImage, $targetFilename);

        //     // Free up memory
        //     imagedestroy($sourceImage);
        //     imagedestroy($croppedImage);

        //     echo 'Cropped image saved  and uploaded successfully to ' . $targetFilename;
        // }
    } else {
        $extension = pathinfo($_REQUEST['image_url'], PATHINFO_EXTENSION);

        if ($extension == 'webp') {
            echo json_encode([
                'code' => 200,
                'url' => $_REQUEST['image_url'],
                'optimisedBytes' => 0,
                'originalBytes'  => 0
            ]);
            return false;
        }
        // Download the image content
        $imageContent = file_get_contents($_REQUEST['image_url']);
        if ($imageContent) {
            $originalBytes = strlen($imageContent);
        }


        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($_REQUEST['image_url']);
                break;
            case 'png':
                $image = imagecreatefrompng($_REQUEST['image_url']);
                break;
            case 'gif':
                $image = imagecreatefromgif($_REQUEST['image_url']);
                break;
            default:
                die('Unsupported image type');
        }


        // Desired crop dimensions
        if (!empty($_REQUEST['aspectRatio'])) {
            $aspectRatioArr = explode('/', $_REQUEST['aspectRatio']);
            $width  = $aspectRatioArr[0];
            $height = $aspectRatioArr[1];

            $cropWidth  = $width;
            $cropHeight = $height;

            // Load the image
            $sourceImage = $image;

            // Get the current dimensions of the image
            $sourceWidth = imagesx($sourceImage);
            $sourceHeight = imagesy($sourceImage);


            // Calculate the crop position (centered in this example)
            $cropX = ($sourceWidth - $cropWidth) / 2;
            $cropY = ($sourceHeight - $cropHeight) / 2;

            // Create a new image with the desired crop dimensions
            $image = imagecreatetruecolor($cropWidth, $cropHeight);

            // Perform the crop
            imagecopy($image, $sourceImage, 0, 0, $cropX, $cropY, $cropWidth, $cropHeight);
        }

        $outputPath = 'uploads/' . uniqid() . '.webp';

        $outputFolder = dirname($outputPath);
        if (!is_dir($outputFolder)) {
            $isFolderCreated =  mkdir($outputFolder, 0755, true);

            if ($isFolderCreated === false) {
                $error = error_get_last();
                echo 'Failed to create directory: ' . $error['message'];
            }
            chmod($outputFolder, 0755);
        }

        // Save the image as WebP
        $imageResponse = imagewebp($image, $outputPath);

        // Free up memory
        imagedestroy($image);

        $webPImageUrl = UPLOAD_PATH . $outputPath;
        if ($imageResponse) {
            echo json_encode([
                'code' => 200,
                'url' => $webPImageUrl,
                'optimisedBytes' => filesize($outputPath),
                'originalBytes'  => $originalBytes,
                'outputPath' => $outputPath
            ]);
        } else {
            echo json_encode(['code' => 400]);
        }
    }

    // Output image file
    // $targetFolder   = 'uploads/';
    // $targetFilename = $targetFolder . uniqid() . '.jpg';

    // // Desired crop dimensions
    // $cropWidth  = $_REQUEST['width'];
    // $cropHeight = $_REQUEST['height'];

    // // Load the image
    // $sourceImage = imagecreatefromjpeg($inputImagePath);

    // // Get the current dimensions of the image
    // $sourceWidth = imagesx($sourceImage);
    // $sourceHeight = imagesy($sourceImage);

    // // Calculate the crop position (centered in this example)
    // $cropX = ($sourceWidth - $cropWidth) / 2;
    // $cropY = ($sourceHeight - $cropHeight) / 2;

    // // Create a new image with the desired crop dimensions
    // $croppedImage = imagecreatetruecolor($cropWidth, $cropHeight);

    // // Perform the crop
    // imagecopy($croppedImage, $sourceImage, 0, 0, $cropX, $cropY, $cropWidth, $cropHeight);

    // // Save the cropped image
    // chmod($targetFolder, '0777');
    // imagejpeg($croppedImage, $targetFilename);

    // // Free up memory
    // imagedestroy($sourceImage);
    // imagedestroy($croppedImage);

    // echo 'Cropped image saved  and uploaded successfully to ' . $targetFilename;
}
