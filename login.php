<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EMACO</title>
        <link href="estilo.css" rel="stylesheet" type="text/css">
        <style> body { display: flex; } </style>
    </head>
   <body>
        <form method="post" class="login-form">
<?php
if (isset($mensaje)) echo "        <h2 id='mensaje'>" . $mensaje . "</h2><br />";
?>
            <h2>EMACO</h2>
            <label for="username">Usuario</label>
            <input type="text" name="username" placeholder="Username" required>
            <label for="password">Contraseña</label>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Entrar</button>
        </form>
    </body>
</html>