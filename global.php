<?php
include_once 'sys_config.php';
include_once 'usr_config.php';

function tiempostr($ent, $sal) {
    global $bloquetiempo;
    
    $lapso = $ent->diff($sal);
    $minutos = $lapso->days * 1440 + $lapso->h * 60 + $lapso->i;
    $minutos += (int)($bloquetiempo / 3);
    $minutos -= $minutos % $bloquetiempo;
    if ($lapso->days > 0) {
        $tiempo = sprintf("%dd %02d:%02d", floor($minutos/1440), floor($minutos/60), $minutos % 60);
    } else {
        if (0 < $minutos)
            $tiempo = sprintf("%02d:%02d", floor($minutos/60), $minutos % 60);
        else
            $tiempo = "<$bloquetiempo min";        
    }
    return $tiempo;
}

if (!file_exists(dirname(__FILE__) . "/sys_config.php") && !isset($_POST['crearconfig'])) {
    include_once 'creaconfig.php';
    exit();
}

if (!isset($_POST['crearconfig'])) {
    // load config parameters
    include_once 'sys_config.php';
    include_once 'usr_config.php';

    // Start the session
//    session_set_cookie_params(1800,"/");
    session_start();

    // Connect to the database
    $conn = new mysqli($dbserver, $dbuser, $dbpass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}