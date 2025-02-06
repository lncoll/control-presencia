<?php
/*
 * Crea la cabecera de la página, incluido el menú superior.
 * Adaptando el menú al nivel del usuario.
 * La variable $titulo debe estar definida en el script que incluya este archivo como título de la página.
 * La variable $mensaje se mostrará al principio de la página si está definida.
*/

include_once 'global.php';

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: '.dirname($_SERVER['PHP_SELF']));
    exit();
}

// Comprobamos si el usuario es administrador y en ese caso vemos si hay solicitudes de cambio pendientes.
if ($_SESSION['role'] == 10) {
    $query = "SELECT count(*) FROM cambios WHERE estado = 0;";
    try {
        $stmt_cab = $conn->prepare($query);
        $stmt_cab->execute();
        $stmt_cab->bind_result($cambios_pend);
        $stmt_cab->fetch();
        $stmt_cab->close();
    } catch (Exception $e) {
        $mensaje = "Error al comprobar solicitudes de cambio.";
    }
}

// Comprobamos si el usuario tiene mensajes pendientes de leer
$query = "SELECT count(*) FROM mensajes WHERE para = ? AND estado = 0;";
try {
    $stmt_cab = $conn->prepare($query);
    $stmt_cab->bind_param('i', $_SESSION['user_id']);
    $stmt_cab->execute();
    $stmt_cab->bind_result($mensajes_pend);
    $stmt_cab->fetch();
    $stmt_cab->close();
} catch (Exception $e) {
    $mensaje = "Error al comprobar mensajes pendientes.";
}

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="estilo.css" rel="stylesheet" type="text/css"> 
        <title><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></title>
        <link rel="stylesheet" href="/js/leaflet/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <script src="/js/leaflet/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    </head>
    <body>
        <div id="page-container">
        <div id="content-wrap">
        <div class="navbar">
            <form method="post">
                <button type="submit" name="dashboard">Inicio</button>
<?php
// Opciones de administrador
if ($_SESSION['role'] == 10) {
    echo '                <button type="submit" name="crear">Crear usuario</button>
                <button type="submit" name="users">Usuarios</button>\n';
    if ($cambios_pend) {
        echo "<button type='submit' name='cambios'>Solicitudes de cambio $cambios_pend <img src='img/aviso.gif' width='16'></button>\n";
    } else {
        echo '<button type="submit" name="cambios">Solicitudes de cambio</button>\n';
    }
}
if ($mensajes_pend) {
    echo "<button type='submit' name='mensajes'>Mensajes $mensajes_pend <img src='img/aviso.gif' width='16'></button>\n";
} else {
    echo '<button type="submit" name="mensajes">Mensajes</button>\n';
}
?>
                <button type="submit" name="editame">Mis datos</button>
                <button type="submit" name="logout">Salir</button>
            </form>
        </div>
<?php
if (isset($mensaje)) {
    echo "<h2 id='mensaje'>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</h2>\n";
}
?>
