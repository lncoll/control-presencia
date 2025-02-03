<?php
/* 
 * Gestión de las solicitudes de cambio.
 * Lista las solicitudes, pudiendo filtrar entre: todas, pendientes, aceptadas o rechazadas.
 * Permite aceptar o rechazar una solicitud.
 */

$titulo = "Solicitudes de cambio";
include 'cabecera.php';

// Validar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: '.dirname($_SERVER['PHP_SELF']));
    exit();
}

// Validar y sanitizar entrada
$estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;
?>
        <form method="post" class="busca-form">
            Filtrar búsqueda por estado: 
            <select name="estado">
                <option value=""  <?php if ($estado === "") echo "selected"; ?>>Todos</option>
                <option value="0" <?php if ($estado === 0) echo "selected"; ?>>Pendiente</option>
                <option value="1" <?php if ($estado === 1) echo "selected"; ?>>Aceptado</option>
                <option value="2" <?php if ($estado === 2) echo "selected"; ?>>Rechazado</option>
            </select>
            <button type="submit" name="cambios">Filtrar</button>
        </form>
<?php
// Preparar consulta segura
$stmt_cam = $conn->stmt_init();
if ($estado === "") {
    $stmt_cam->prepare("SELECT * FROM cambios ORDER BY id DESC;");
} else {
    $stmt_cam->prepare("SELECT * FROM cambios WHERE estado = ? ORDER BY id DESC;");
    $stmt_cam->bind_param("i", $estado);
}
$stmt_cam->execute();
$stmt_cam->bind_result($cambio_id, $reg_id, $user_id, $estado, $anterior, $posterior, $comentario);
$stmt_cam->store_result();
?>
        <h2>Solicitudes de cambio</h2>
        <table class="tlistado">
            <tr>
                <th>Usuario</th>
                <th>Original</th>
                <th>Propuesto</th>
                <th>Comentario</th>
                <th>Acción</th>
            </tr>
<?php   
if ($stmt_cam->num_rows > 0) {
    while ($stmt_cam->fetch()) {
        $query = "SELECT username FROM empleados WHERE user_id = {$user_id};";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $nombre = $row['username'];
        $result->close();
        echo "            <tr>\n";
        echo "                <td>{$nombre}</td>\n";
        echo "                <td>{$anterior}</td>\n";
        echo "                <td>{$posterior}</td>\n";
        echo "                <td>{$comentario}</td>\n";
        if ($estado == 0) {
            echo "                <td>\n";
            echo "                    <form method='post'><button class='btn' name='aceptar_cambio'  value='".$cambio_id."' title='Aceptar' ><img src=img/acepta.png  alt='Aceptar'  width='24'></button></form>\n";
            echo "                    <form method='post'><button class='btn' name='rechazar_cambio' value='".$cambio_id."' title='Rechazar'><img src=img/rechaza.png alt='Rechazar' width='24'></button></form>\n";
            echo "                </td>\n";
        } elseif ($estado == 1) {
            echo "                <td>Aceptado</td>\n";
        } elseif ($estado == 2) {
            echo "                <td>Rechazado</td>\n";
        }
        echo "            </tr>\n";
    }
} else {
    echo "            <tr><td colspan='5'>No hay registros</td></tr>\n";
}
$stmt_cam->close();
?>
        </table>
    </body>
</html>
