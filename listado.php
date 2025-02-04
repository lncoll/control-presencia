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
    $stmt_lis->close();
}

$stmt_lis = $conn->stmt_init();
$stmt_lis->prepare("SELECT reg_id, reg_time, entrada FROM registros WHERE reg_time BETWEEN ? AND ? AND user_id = ? ORDER BY reg_id ASC;");
try {
    $stmt_lis->bind_param("ssi", $inicio, $fin, $busca_user);
    $stmt_lis->execute();
    $stmt_lis->bind_result($reg_id, $reg_time, $entrada);
    $stmt_lis->store_result();
} catch (Exception $e) {
    $mensaje .= "Error: " . $e->getMessage();
    $stmt_lis->close();
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
            <tr><th>Fecha:</th><th colspan="2">Entrada:</th><th colspan="2">Salida:</th><th>Tiempo:</th></tr>
<?php
            if ($stmt_lis->num_rows > 0) {
                while($row = $stmt_lis->fetch()) {
                    if (!$entrada) {
                        $sal = new DateTime($reg_time);
                        $fecha = $sal->format('d/m/Y');
                        if ($ent) {
                            $tiempo = tiempostr($ent, $sal);
                            echo "<tr><td>" . $fecha . "</td><td>" . $ent->format('H:i') . "</td><td><form method='post'><button class='btn' name='ver_reg'  value='".$ent_id."' title='Mapa' ><img src=img/mapa.png  alt='Mapa'  width='24'></button></form></td><td>" . $sal->format('H:i') . "</td><td><form method='post'><button class='btn' name='ver_reg'  value='".$reg_id."' title='Mapa' ><img src=img/mapa.png  alt='Mapa'  width='24'></button></form></td><td>" . $tiempo . "</td></tr>\n";
                        } else
                            echo "<tr><td>" . $fecha . "</td><td>" . $ent->format('H:i') . "</td><td>---</td><td>---</td></tr>\n";                        
                    } else {
                        $ent = new DateTime($reg_time);
                        $ent_id = $reg_id;
                    }
                }
            } else {
                echo "<tr><td colspan='6'>No hay registros</td></tr>";
            }
            $stmt_lis->close();
            ?>
        </table>
<?php
include 'pie.php';