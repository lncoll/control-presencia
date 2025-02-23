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

$titulo = "Mensajes";
include 'cabecera.php';

echo "        <h2>Mensajes</h2>\n";
try {
    $stmt_men = $conn->stmt_init();
    $stmt_men->prepare("SELECT `mensajes`.`msg_id`, `mensajes`.`estado`,`mensajes`.`hora`,`mensajes`.`texto`,`empleados`.`username`  FROM `mensajes` JOIN `empleados` ON `mensajes`.`de` = `empleados`.`user_id` WHERE `mensajes`.`para` = ?;");
    $stmt_men->bind_param("i", $_SESSION["user_id"]);
    $stmt_men->execute();
    $stmt_men->bind_result($msg_id, $estado, $hora, $texto, $remite);
    $stmt_men->store_result();
    if ($stmt_men->num_rows>0){
        while ($stmt_men->fetch()){
            echo "        <div class='mensajes'>\n";
            if ($estado)
                echo <<<HTML
            <h3>De: $remite - hora: $hora - Leído</h3>

            <hr>
            $texto
        </div>
HTML;
            else
                echo <<<HTML
            <h3>De: $remite - hora: $hora - Nuevo</h3>

            <hr>
            $texto
            <form method="post">
                <button type="submit" name="msg_leido" value="$msg_id"> Marcar como leido</button>
            </form>
        </div>
HTML;
        }
    }
} catch (Exception $e) {
    $mensaje = "Error cargando mensajes. " . $e->getMessage();
    exit();
}

include 'pie.php';
