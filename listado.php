<?php
$titulo = "Listado de usuarios";
include 'cabecera.php';

if ($_POST['listar'] != "") $busca_user = mysqli_real_escape_string($conn,  $_POST['listar']); else $busca_user = $_SESSION['user_id'];
if ($_POST['inicio'] != "") $inicio = mysqli_real_escape_string($conn,$_POST['inicio']); else $inicio = date('Y-m-01');
if ($_POST['fin'] != "") $fin = mysqli_real_escape_string($conn,$_POST['fin']." 23:59:59"); else $fin = date('Y-m-t')." 23:59:59";

$query = "SELECT nombre FROM empleados WHERE user_id = $busca_user;";
try {
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
    $result->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $stmt->close();
    exit();
}

$stmt = $conn->stmt_init();
$stmt->prepare("SELECT reg_time, entrada FROM registros WHERE reg_time BETWEEN ? AND ? AND user_id = ? ORDER BY reg_id ASC;");
try {
    $stmt->bind_param("ssi", $inicio, $fin, $busca_user);
    $stmt->execute();
    $stmt->bind_result($reg_time, $entrada);
    $stmt->store_result();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $stmt->close();
    exit();
}
?>
        <form method="post" class="busca-form">
            <label for="inicio">Inicio</label>
            <input type="date" name="inicio" value="<?= $inicio ?>" required>
            <label for="fin">Fin</label>
            <input type="date" name="fin" value="<?= substr($fin, 0, 10) ?>" required>
            <button type="submit" name="listar" value="<?= $busca_user ?>">Filtrar</button>
        </form>
        <table class="tlistado">
            <caption>Registros para: <?= $nombre ?></caption>
            <tr><th>Fecha:</th><th>Entrada:</th><th>Salida:</th><th>Tiempo:</th></tr>
<?php
            if ($stmt->num_rows > 0) {
                while($row = $stmt->fetch()) {
                    if (!$entrada) {
                        $sal = new DateTime($reg_time);
                        $fecha = $sal->format('d/m/Y');
                        if ($ent) {
                            $lapso = $ent->diff($sal);
                            if ($lapso->days > 0) $tiempo = $lapso->format('%d d %H:%I'); else $tiempo = $lapso->format('%H:%I');
                            echo "<tr><td>" . $fecha . "</td><td>" . $ent->format('H:i') . "</td><td>" . $sal->format('H:i') . "</td><td>" . $tiempo . "</td></tr>\n";
                        } else
                            echo "<tr><td>" . $fecha . "</td><td>" . $ent->format('H:i') . "</td><td>---</td><td>---</td></tr>\n";                        
                    } else {
                        $ent = new DateTime($reg_time);
                    }
                }
            } else {
                echo "<tr><td colspan='4'>No hay registros</td></tr>";
            }
            $stmt->close();
            ?>
        </table>
    </body>
</html>