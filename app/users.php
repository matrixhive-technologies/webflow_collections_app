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
    return;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    # Get the Child Users of the Logged in Users
    if ($_REQUEST['action'] == 'list_users') {
        $userId = $_SESSION['LoggedInUser']['id'];

        $fetchChildUsers = "SELECT * FROM app_users WHERE parent_user_id= '" . mysqli_real_escape_string($app_connection, $userId) . "'";
        $isChildExists = mysqli_query($connection, $fetchChildUsers);
        if (mysqli_num_rows($isChildExists) > 0) {
            while ($row = mysqli_fetch_assoc($isChildExists)) {
                $chiledUsers[] = [
                    'id'         => $row['id'],
                    'first_name' => $row['first_name'],
                    'last_name'  => $row['last_name'],
                    'email'      => $row['email'],
                ];
            }
            echo json_encode(['code' => 200, 'childUsers' => json_encode($chiledUsers)]);
        } else {
            echo json_encode(['code' => 400, 'childUsers' => json_encode([])]);
        }
    }

    # Add the Child User to the Logged in User
    if ($_REQUEST['action'] == 'add_user') {

        $userId = $_SESSION['LoggedInUser']['id'];
        $fetchParentUserToken = "SELECT * FROM app_users WHERE id= '" . mysqli_real_escape_string($app_connection, $userId) . "'";
        $parentUserToken = mysqli_query($connection, $fetchParentUserToken);
        $row = mysqli_fetch_assoc($parentUserToken);
        $accessToken = $row['access_token'];

        $userData = json_decode($_REQUEST['params'], TRUE);

        $hashedPassword = md5($userData['password']);

        # Email Already Exists Validaiton
        $checkEmailExists =  "SELECT * FROM app_users WHERE email= '" . $userData['email'] . "'";
        $emailExists = mysqli_query($connection, $checkEmailExists);
        if (mysqli_num_rows($emailExists) > 0) {
            echo json_encode(['code' => 400, 'message' => 'Email Already Exists']);
            return;
        }

        $addUser = "INSERT INTO `app_users`(`parent_user_id`,`first_name`, `last_name`, `email`, `password`,`access_token`, `created_at`) 
                            VALUES (
                            '" . $userId . "',
                            '" . $userData['first_name'] . "',
                            '" . $userData['last_name'] . "',
                            '" . $userData['email'] . "',
                            '" . $hashedPassword . "',
                            '" . $accessToken . "',
                            '" . date("Y-m-d H:i:s") . "')";

        if (mysqli_query($connection, $addUser)) {
            echo json_encode(['code' => 200, 'message' => 'Child User Added Successfully']);
        } else {
            echo json_encode(['code' => 400, 'message' => 'Something went wrong']);
        }
    }

    # Delete the Child User
    if ($_REQUEST['action'] == 'delete_user') {
        $userId = $_SESSION['LoggedInUser']['id'];

        $childUserId = $_REQUEST['params'];

        $deleteUser = "DELETE FROM app_users WHERE id= '" . mysqli_real_escape_string($app_connection, $childUserId) . "' AND parent_user_id='" . mysqli_real_escape_string($app_connection, $userId) . "'";
        $isDeleted = mysqli_query($app_connection, $deleteUser);

        if ($isDeleted) {
            echo json_encode(['message' => 'User is deleted', 'code' => 200]);
        } else {
            echo json_encode(['message' => 'Error deleting user', 'code' => 400]);
        }
    }

    # Update the Child User
    if ($_REQUEST['action'] == 'update_user') {
        $userId = $_SESSION['LoggedInUser']['id'];

        $userData = json_decode($_REQUEST['params'], TRUE);

        # Email Already Exists Validaiton
        $checkEmailExists =  "SELECT * FROM app_users WHERE email= '" . $userData['email'] . "' AND id!='" . mysqli_real_escape_string($app_connection, $userData['id']) . "'";
        $emailExists = mysqli_query($connection, $checkEmailExists);
        if (mysqli_num_rows($emailExists) > 0) {
            echo json_encode(['code' => 400, 'message' => 'Email Already Exists']);
            return;
        }

        # Udpate the User Details
        if (!empty($userData['password'])) {
            $updateUser = "UPDATE `app_users` SET 
        `email`='" . $userData['email'] . "',
        `first_name`='" . $userData['first_name'] . "',
        `last_name`='" . $userData['last_name'] . "',
        `password`='" . md5($userData['password']) . "',
        `updated_at`='" . date("Y-m-d H:i:s") . "'
         WHERE id='" . $userData["id"] . "'";
        } else {
            $updateUser = "UPDATE `app_users` SET 
        `email`='" . $userData['email'] . "',
        `first_name`='" . $userData['first_name'] . "',
        `last_name`='" . $userData['last_name'] . "',
        `updated_at`='" . date("Y-m-d H:i:s") . "'
         WHERE id='" . $userData["id"] . "'";
        }

        if (mysqli_query($connection, $updateUser)) {
            echo json_encode(['code' => 200, 'message' => 'Child User updated Successfully']);
        } else {
            echo json_encode(['code' => 400, 'message' => 'Something went wrong']);
        }
    }
}
