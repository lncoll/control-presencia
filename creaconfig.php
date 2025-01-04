<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="estilo.css" rel="stylesheet" type="text/css"> 
        <title>Configuracion inicial</title>
    </head>
    <body>
<?php
if (isset($mensaje)) echo "<h2 id='mensaje'>" . $mensaje . "</h2>";
?>        
        <h1>Configuración inicial</h1>
        <form method="post" class="login-form">
            <h2>Base de datos</h2>
            <label for="dbserver">Servidor</label>
            <input type="text" name="dbserver" placeholder="localhost" required>
            <label for="dbuser">Usuario</label>
            <input type="text" name="dbuser" required>
            <label for="dbpass">Contraseña
            <input type="password" name="dbpass" placeholder="********" required>
            <label for="dbname">Base de datos</label>
            <input type="text" name="dbname" placeholder="fichaje" required>
            <h2>Empresa</h2>
            <label for="nombreempresa">Nombre</label>
            <input type="text" name="nombreempresa" required>
            <hr>
            <h2>Crear Administrador</h2>
            <input type="hidden" name="role" value="10">
            <input type="hidden" name="dentro" value="0">

            <label for="username">Alias</label>
            <input type="text" name="username" placeholder="Admin" required >

            <label for="nombre">Nombre completo</label>
            <input type="text" name="nombre" placeholder="Nombre" required>

            <label for="NIF">NIF</label>
            <input type="text" name="NIF" placeholder="NIF" required>

            <label for="email">Correo e</label>
            <input type="email" name="email" placeholder="e-mail" required>

            <label for="password">contraseña</label>
            <input type="password" name="password" placeholder="********" required>
        
            <label for="password2">Repetir contraseña</label>
            <input type="password" name="password2" placeholder="********" required>

            <button type="submit" name="crearconfig">Crear configuración</button>
        </form>
    </body>
</html>

<?php
