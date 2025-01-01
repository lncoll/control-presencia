<?php
if ($_POST['editame'] > 0) {
    $user_id = $_POST['editame'];
} else {
    $user_id = $_SESSION['user_id'];
}
$stmt = $conn->stmt_init();
$stmt->prepare("SELECT * FROM empleados WHERE user_id = ? ");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $username, $nombre, $password, $NIF, $email, $dentro, $role);
    $stmt->fetch();
} else {
    echo "Usuario no encontrado: ". $user_id;
    exit();
}


$titulo = "Editar usuario";
include 'cabecera.php';
?>
        <h1>Editar usuario</h1>
        <form method="post" class="login-form">
            <input type="hidden" name="user_id" value="<?= $id ?>">
            <input type="hidden" name="role" value="<?= $role ?>">
            <input type="hidden" name="dentro" value="<?= $dentro ?>">
            
            <label for="username">Usuario</label>
            <input type="text" name="username" placeholder="Username" value="<?= $username ?>" readonly>

            <label for="nombre">Nombre completo</label>
            <input type="text" name="nombre" placeholder="Nombre" value="<?= $nombre ?>" required>

            <label for="NIF">NIF</label>
            <input type="text" name="NIF" placeholder="NIF" value="<?= $NIF ?>" required>

            <label for="email">Correo e</label>
            <input type="email" name="email" placeholder="e-mail" value="<?= $email ?>"required>

            <label for="role">Nivel</label>
            <select name="role" required <?php if ($_SESSION['role'] < 10) echo "disabled"; ?>>
                <option value="0" <?php if ($role == 0) echo "selected"; ?>>Desabilitado</option>
                <option value="1" <?php if ($role == 1) echo "selected"; ?>>Usuario</option>
                <option value="10" <?php if ($role == 10) echo "selected"; ?>>Administrador</option>
            </select>
            <button type="submit" name="edit_employee">Aceptar</button>
        </form>
        <br />
        <form method="post" class="login-form">
            <input type="hidden" name="password0" value="<?= $password ?>">
            <input type="hidden" name="user_id" value="<?= $id ?>">

            <label for="password">Contraseña</label>
            <input type="password" name="password1" placeholder="Password" required>

            <label for="password2">Repetir contraseña</label>
            <input type="password" name="password2" placeholder="Password" required>

            <button type="submit" name="edit_password">Cambiar Contraseña</button>
        </form>
    </body>
</html>