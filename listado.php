<?php
if ($_POST['listar'] != "") $busca_user = mysqli_real_escape_string($conn,  $_POST['listar']); else $busca_user = $_SESSION['user_id'];
if ($_POST['mes'] != "") $mes = mysqli_real_escape_string($conn,$_POST['mes']); else $mes = date('Y-m');
$inicio = $mes."-01";
$fin = DateTime::createFromFormat('Y-m-d', $inicio);
$fin->modify('last day of this month');
$fin = $fin->format('Y-m-d') . " 23:59:59";
//if ($_POST['fin'] != "") $fin = mysqli_real_escape_string($conn,$_POST['fin']." 23:59:59"); else $fin = date('Y-m-t')." 23:59:59";

$titulo = "Listado de usuarios";
include 'cabecera.php';

$query = "SELECT nombre FROM empleados WHERE user_id = $busca_user;";
try {
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
    $result->close();
} catch (Exception $e) {
    $mensaje = "Error: " . $e->getMessage();
    $stmt->close();
}

$stmt = $conn->stmt_init();
$stmt->prepare("SELECT reg_time, entrada FROM registros WHERE reg_time BETWEEN ? AND ? AND user_id = ? ORDER BY reg_id ASC;");
try {
    $stmt->bind_param("ssi", $inicio, $fin, $busca_user);
    $stmt->execute();
    $stmt->bind_result($reg_time, $entrada);
    $stmt->store_result();
} catch (Exception $e) {
    $mensaje .= "Error: " . $e->getMessage();
    $stmt->close();
}
?>
        <form method="post" class="busca-form">
            <label for="mes">Mes</label>
            <input type="month" name="mes" value="<?= $mes ?>" required>
            <button type="submit" name="listar" value="<?= $busca_user ?>">Filtrar</button>
            <button type="submit" name="pdf" value="<?= $busca_user ?>">pdf</button>
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
                            $tiempo = tiempostr($ent, $sal);
/*                            $lapso = $ent->diff($sal);
                            $minutos = $lapso->days * 1440 + $lapso->h * 60 + $lapso->i;
                            $minutos -= $minutos % $bloquetiempo;
                            if ($lapso->days > 0) {
                                $tiempo = sprintf("%dd %02d:%02d", floor($minutos/1440), floor($minutos/60), $minutos % 60);
                            } else {
                                $tiempo = sprintf("%02d:%02d", floor($minutos/60), $minutos % 60);
                            }*/
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