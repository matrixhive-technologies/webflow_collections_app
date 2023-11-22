<?php
error_reporting(1);
ini_set('display_errors', true);
require_once('config/app.php');
require_once('config/database.php');
session_start();
try {

    # Get the Authorization Code.
    if (isset($_GET['code'])) {
        $code = $_GET['code'];
        $params = [
            'client_id'     => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'code'          => $code,
            'grant_type'    => GRANT_TYPE,
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.webflow.com/oauth/access_token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));


        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        if ($result) {
            $responseArr = json_decode($result, true);

            if (isset($responseArr['access_token'])) {
                # Call the API using the extracted Access Token to fetch the User Details.
                $_SESSION['access_token'] = $responseArr['access_token'];

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://api.webflow.com/v2/token/authorized_by');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


                $headers = array();
                $headers[] = 'Accept: application/json';
                $headers[] = 'Authorization: Bearer ' . $responseArr['access_token'];
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);
                $authorizedUserResponseArr = json_decode($result, true);

                # Store the User Details - Starts Here
                if ($connection) {
                    $fetchUser = "SELECT * FROM app_users WHERE email= '" . $authorizedUserResponseArr["email"] . "'";
                    $isUserExists = mysqli_query($connection, $fetchUser);


                    if (mysqli_num_rows($isUserExists) == 0) {
                        $addUser = "INSERT INTO `app_users`(`first_name`, `last_name`, `email`, `access_token`, `created_at`) 
                            VALUES (
                            '" . $authorizedUserResponseArr['firstName'] . "',
                            '" . $authorizedUserResponseArr['lastName'] . "',
                            '" . $authorizedUserResponseArr['email'] . "',
                            '" . $responseArr['access_token'] . "',
                            '" . date("Y-m-d H:i:s") . "')";

                        if (mysqli_query($connection, $addUser)) {
                            $_SESSION['userDetails'] = $authorizedUserResponseArr;
                            header('Location: ' . HOME_PAGE_URL);
                        } else {
                            header('Location: ' . APP_URL);
                        }
                    } else {

                        # Udpate the User Details -- Starts Here
                        $updateUser = "UPDATE `app_users` SET 
                        `email`='" . $authorizedUserResponseArr['email'] . "',
                        `access_token`='" . $responseArr['access_token'] . "',
                        `updated_at`='" . date("Y-m-d H:i:s") . "'
                         WHERE email='" . $authorizedUserResponseArr["email"] . "'";

                        if (mysqli_query($connection, $updateUser)) {
                            unset($_SESSION['userDetails']);
                            $_SESSION['userDetails'] = $authorizedUserResponseArr;
                            header('Location: ' . HOME_PAGE_URL);
                        } else {
                            header('Location: ' . APP_URL);
                        }
                        # Udpate the User Details -- Ends Here

                    }
                } else {
                    header('Location: ' . APP_URL);
                }
            }
            # Store the User Details - Ends Here
        }
    }
} catch (Exception $e) {
    echo $e->getMessage() . ' at line number ' . $e->getLine();
}
