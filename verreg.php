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

$reg_id = mysqli_real_escape_string($conn, $_POST['ver_mapa']);
$query = "SELECT empleados.nombre, registros.user_id, registros.reg_time, registros.entrada, registros.IP, registros.location, registros.creado, registros.modificado FROM registros INNER JOIN empleados ON empleados.user_id = registros.user_id WHERE registros.reg_id = ?;";
try {
    $stmt_ver = $conn->stmt_init();
    $stmt_ver->prepare($query);
    $stmt_ver->bind_param("i", $reg_id);
    $stmt_ver->execute();
    $stmt_ver->bind_result($username, $user_id, $reg_time, $entrada, $IP, $location, $creado, $modificado);
    $stmt_ver->store_result();
    $stmt_ver->fetch();
} catch (Exception $e) {
    $mensaje = "Error: " . $e->getMessage();
    $stmt_ver->close();
}
$location = explode("|", $location);
$mes = substr($reg_time, 0, 7);

$titulo = "Ver registro";
include 'cabecera.php';
?>

        <div id='vermap'></div>

<script>
var latitude = <?php echo $location[0]; ?>;
var longitude = <?php echo $location[1]; ?>;
var map = L.map('vermap').setView([latitude, longitude], 16);
var marker = L.marker([latitude, longitude]).addTo(map);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
}).addTo(map);
</script>
        <table class="tlistado">
            <caption>Registro de <?= $username ?></caption>
            <tr><th>Fecha:</th><td><?= $reg_time ?></td></tr>
            <tr><th>Entrada:</th><td><?= $entrada ? "Sí" : "No" ?></td></tr>
            <tr><th>IP:</th><td><?= $IP ?></td></tr>
            <tr><th>Creado:</th><td><?= $creado ?></td></tr>
            <tr><th>Modificado:</th><td><?= $modificado ?></td></tr>
        </table>
        <br />
        <form method="post" class="reg-form">
            <input type="hidden" name="mes" value="<?= $mes ?>">
            <button type="submit" name="listar" value="<?= $user_id ?>">Volver</button>
        </form>

<?php
include 'pie.php';