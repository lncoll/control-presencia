<?php

if (!file_exists(dirname(__FILE__) . "/config.php") && !isset($_POST['crearconfig'])) {
    include_once 'creaconfig.php';
    exit();
}

if (!isset($_POST['crearconfig'])) {
    // load config parameters
    include_once 'config.php';

    // Start the session
    session_set_cookie_params(1800,"/");
    session_start();

    // Connect to the database
    $conn = new mysqli($dbserver, $dbuser, $dbpass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}