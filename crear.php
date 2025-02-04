<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: '.dirname($_SERVER['PHP_SELF']));
    exit();
}

$titulo = "Crear nuevo usuario";
include 'cabecera.php';
?>
   <body>
        <h1>Crear nuevo usuario</h1>
        <form method="post" class="login-form">
            <label for="username">Usuario</label>
            <input type="text" name="username" placeholder="Username" required>

            <label for="nombre">Nombre completo</label>
            <input type="text" name="nombre" placeholder="Nombre" required>

            <label for="password">Contraseña</label>
            <input type="password" name="password" placeholder="Password" required>

            <label for="NIF">NIF</label>
            <input type="text" name="NIF" placeholder="NIF" required>

            <label for="email">Correo e</label>
            <input type="email" name="email" placeholder="e-mail" required>

            <label for="role">Nivel</label>
            <select name="role" required >
                <option value="0" >Desabilitado</option>
                <option value="1" selected >Usuario</option>
                <option value="10" >Administrador</option>
            </select>

            <button type="submit" name="create_employee">Crear</button>
        </form>
<?php
include 'pie.php';