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
    $stmt_cab = $conn->prepare($query);
    $stmt_cab->execute();
    $stmt_cab->bind_result($pendientes);
    $stmt_cab->fetch();
    $stmt_cab->close();
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="estilo.css" rel="stylesheet" type="text/css"> 
        <title><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></title>
    </head>
    <body>
        <div class="navbar">
            <form method="post">
                <button type="submit" name="dashboard">Inicio</button>
<?php
// Opciones de administrador
if ($_SESSION['role'] == 10) {
    echo '                <button type="submit" name="crear">Crear usuario</button>
                <button type="submit" name="users">Usuarios</button>\n';
    if ($pendientes) {
        echo '<button type="submit" name="cambios">Solicitudes de cambio ' . htmlspecialchars($pendientes, ENT_QUOTES, 'UTF-8') . "<img src='img/aviso.gif' width='16'></button>\n";
    } else {
        echo '<button type="submit" name="cambios">Solicitudes de cambio</button>\n';
    }
}
?>
                <button type="submit" name="mensajes">Mensajes</button>
                <button type="submit" name="editame">Mis datos</button>
                <button type="submit" name="logout">Salir</button>
            </form>
        </div>
<?php
if (isset($mensaje)) {
    echo "<h2 id='mensaje'>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</h2>\n";
}
?>
