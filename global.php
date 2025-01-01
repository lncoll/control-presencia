<?php
// Connect to the database
$servername = "localhost";
$username = "emaco";
$password = "ocame";
$dbname = "emaco";

session_set_cookie_params(1800,"/");
session_start();
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
