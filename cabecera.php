<?php
include_once 'global.php';

if ($_SESSION['role'] == 10) {
    $query = "SELECT count(*) FROM cambios WHERE estado = 0;";
    $result = $conn->query($query);
    $row = $result->fetch_row();
    $pendientes = $row[0];
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="estilo.css" rel="stylesheet" type="text/css"> 
        <title><?= $titulo ?></title>
    </head>
    <body>
        <div class = "navbar">
            <form method="post">
                <button type = submit name = dashboard>Inicio</button>
<?php
if ($_SESSION['role'] == 10) {

    echo "                <button type = submit name = crear>Crear usuario</button>
                <button type = submit name = users>Usuarios</button>
                <button type = submit name = cambios>Solicitudes de cambio ".$pendientes."</button>\n";
}
?>
                <button type = submit name = editame>Mis datos</button>
                <button type = submit name = logout>Salir</button>
            </form>
        </div>
<?php
if (isset($mensaje)) echo "<h2 id='mensaje'>" . $mensaje . "</h2>";
