<?php

$servername = "localhost";
$username   = "rupesh";
$password   = "Rupesh@Odoo@1";
$dbname     = "webflow_collections_db";
$app_dbname = "webflow_collections_db";
$user_dbname= "webflow_collections_db";

// Create connection
$connection = mysqli_connect($servername, $username, $password, $dbname);

$app_connection  = mysqli_connect($servername, $username, $password, $app_dbname);
$user_connection = mysqli_connect($servername, $username, $password, $user_dbname);
// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
