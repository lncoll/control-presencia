<?php
$titulo = "Solicitudes de cambio";
include 'cabecera.php';
?>
        <form method="post" class="busca-form">
            Filtrar busqueda por: 
            <input type="text" name="user" placeholder="Usuario a buscar">
            <select name="estado">
                <option value=""   <?php if ($_POST['estado'] == "") echo "selected"; ?>>Todos</option>
                <option value="0"  <?php if (!isset($_POST['estado']) || $_POST['estado'] == 0) echo "selected"; ?>>Pendiente</option>
                <option value="1"  <?php if ($_POST['estado'] == 1) echo "selected"; ?>>Aceptado</option>
                <option value="10" <?php if ($_POST['estado'] == 10) echo "selected"; ?>>Rechazado</option>
            </select>
            <button type="submit" name="cambios">Filtrar</button>
        </form>
<?php
$stmt = $conn->stmt_init();
if ($_POST['estado'] == "") {
    $stmt->prepare("SELECT * FROM cambios;");
} else {
    $stmt->prepare("SELECT * FROM cambios WHERE estado = ?;");
    $stmt->bind_param("i", $_POST['estado']);
}
$stmt->execute();
$stmt->bind_result($cambio_id, $reg_id, $user_id, $estado, $anterior, $posterior, $comentario);
$stmt->store_result();
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
if ($stmt->num_rows > 0) {
    while ($stmt->fetch()) {
        $query = "SELECT username FROM empleados WHERE user_id = ".$user_id.";";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $nombre = $row['username'];
        $result->close();
        echo "            <tr>\n";
        echo "                <td>".$nombre."</td>\n";
        echo "                <td>".$anterior."</td>\n";
        echo "                <td>".$posterior."</td>\n";
        echo "                <td>".$comentario."</td>\n";
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
$stmt->close();
?>
        </table>
    </body>
</html>
