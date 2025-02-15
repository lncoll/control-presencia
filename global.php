<?php
include_once 'sys_config.php';
include_once 'usr_config.php';

function tiempostr($ent, $sal) {
    global $bloquetiempo;
    
    $lapso = $ent->diff($sal);
    $dias = $lapso->days;
    $horas = $lapso->h;
    $min = $lapso->i;
    $minutos = $dias * 1440 + $horas * 60 + $min;
    $minutos += (int)($bloquetiempo / 3);
    $minutos -= $minutos % $bloquetiempo;
    if ($dias > 0) {
        $tiempo = sprintf("%dd %02d:%02d", $minutos/1440, ($minutos%1440)/60, $minutos%60);
    } else {
        if (0 < $minutos)
            $tiempo = sprintf("%02d:%02d", ($minutos%1440)/60, $minutos%60);
        else
            $tiempo = "<$bloquetiempo min";        
    }
    return $tiempo;
}

function getTimezoneFromLocation($latitude, $longitude, $timestamp) {
    global $mapsAPI;
    $url = "https://maps.googleapis.com/maps/api/timezone/json?location=$latitude,$longitude&timestamp=$timestamp&key=$mapsAPI";

    $response = file_get_contents($url);
    $data = json_decode($response);
/*
    // Para debug de TimeZone API
    $fichero = fopen("timezone.log", "a");
    fwrite($fichero, $url."\n");
    fwrite($fichero, print_R($data, true)."\n");
    fclose($fichero);
*/
    if ($data->status == 'OK') {
        return $data->timeZoneId;
    } else {
        return false;
    }
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