<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="estilo.css" rel="stylesheet" type="text/css"> 
        <title>Configuracion inicial</title>
    </head>
    <body>
    <div id="page-container">
            <div id="content-wrap">
<?php
if (isset($mensaje)) echo "<h2 id='mensaje'>" . $mensaje . "</h2>";
if (isset($_POST['crearconfig'])) {
    $dbserver = $_POST['dbserver'];
    $dbuser = $_POST['dbuser'];
    $dbname = $_POST['dbname'];
    $nombreempresa = $_POST['nombreempresa'];
    $nifempresa = $_POST['nifempresa'];
    $username = $_POST['username'];
    $nombre = $_POST['nombre'];
    $NIF = $_POST['NIF'];
    $email = $_POST['email'];
} else {
    $dbserver = "";
    $dbuser = "";
    $dbname = "";
    $nombreempresa = "";
    $nifempresa = "";
    $username = "";
    $nombre = "";
    $NIF = "";
    $email = "";
}
?>        
                <h1>Configuración inicial</h1>
                <form method="post" class="login-form">
                    <h2>Base de datos</h2>
                    <label for="dbserver">Servidor</label>
                    <input type="text" name="dbserver" placeholder="localhost" value="<?= $dbserver ?>" required>
                    <label for="dbuser">Usuario</label>
                    <input type="text" name="dbuser" value="<?= $dbuser ?>" required>
                    <label for="dbpass">Contraseña
                    <input type="password" name="dbpass" placeholder="********" required>
                    <label for="dbname">Base de datos</label>
                    <input type="text" name="dbname" placeholder="fichaje" value="<?= $dbname ?>" required>
                    <h2>Empresa</h2>
                    <label for="nombreempresa">Nombre</label>
                    <input type="text" name="nombreempresa" value="<?= $nombreempresa ?>" required>
                    <label for="nifempresa">NIF</label>
                    <input type="text" name="nifempresa" value="<?= $nifempresa ?>" required>
                    <hr>
                    <h2>Crear Administrador</h2>
                    <input type="hidden" name="role" value="10">
                    <input type="hidden" name="dentro" value="0">

                    <label for="username">Alias</label>
                    <input type="text" name="username" placeholder="Admin" value="<?= $username ?>" required >

                    <label for="nombre">Nombre completo</label>
                    <input type="text" name="nombre" placeholder="Nombre" value="<?= $nombre ?>" required>

                    <label for="NIF">NIF</label>
                    <input type="text" name="NIF" placeholder="NIF" value="<?= $NIF ?>" required>

                    <label for="email">Correo e</label>
                    <input type="email" name="email" placeholder="e-mail" value="<?= $email ?>" required>

                    <label for="password">contraseña</label>
                    <input type="password" name="password" placeholder="********" required>
                
                    <label for="password2">Repetir contraseña</label>
                    <input type="password" name="password2" placeholder="********" required>

                    <button type="submit" name="crearconfig">Crear configuración</button>
                </form>
<?php
include 'pie.php';