<?php

$servername = "localhost";
$username   = "rupesh";
$password   = "Rupesh@Odoo@1";
$dbname     = "webflow_collections_db";

// Create connection
$connection = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
