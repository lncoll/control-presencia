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

// Verificar que el usuario tiene permisos de administrador
if ($_SESSION['role'] != 10) {
    $mensaje = "No tiene permisos para editar la configuración";
    header('Location: '.dirname($_SERVER['PHP_SELF']));
    exit();
}
// formulario para editar los campos de usr_config.php
$title = "Editar configuración";
include 'cabecera.php';
?>
        <h1>Editar configuración</h1>
        <form method="post" class="login-form" enctype="multipart/form-data">
            <label for="nombreempresa">Nombre de la empresa</label>
            <input type="text" name="nombreempresa" placeholder="Nombre de la empresa" value="<?= $nombreempresa ?>" required>
            <label for="nifempresa">NIF de la empresa</label>
            <input type="text" name="nifempresa" placeholder="NIF de la empresa" value="<?= $nifempresa ?>" required>
            <label for="bloquetiempo">Bloque de tiempo</label>
            <select name="bloquetiempo">
                <option value="1"  <?php if ($bloquetiempo === 1)  echo "selected"; ?>>1 minuto</option>
                <option value="5"  <?php if ($bloquetiempo === 5)  echo "selected"; ?>>5 minutos</option>
                <option value="15" <?php if ($bloquetiempo === 15) echo "selected"; ?>>15 minutos</option>
                <option value="30" <?php if ($bloquetiempo === 30) echo "selected"; ?>>30 minutos</option>
            </select>
            <img src="uploads/<?= $logo ?>" width="260" title="Logotipo" />
            <label for="logopic">Logo: max. 32 KB. jpg o png</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="35625" />
            <input type="file" name="logopic" accept=".png,.jpg" />
            <br />
            <button type="submit" name="new_conf">Guardar</button>
        </form>
<?php
include 'pie.php';