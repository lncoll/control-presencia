<?php
$query = "SELECT user_id, reg_time, entrada FROM registros WHERE reg_id = " . $_POST['editareg'] . " ;"; 
try {
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];
    $reg_time = $row['reg_time'];
    $entrada = $row['entrada'];
    $result->close();
} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
    $result->close();
}

if ($_SESSION['user_id'] != $user_id) {
    $message = "No puedes editar registros de otros usuarios";
    include 'dashboard.php';
    exit();
}

$titulo = "Edición de registro";
include 'cabecera.php';

$momento = new DateTime($reg_time);
$fecha = $momento->format('Y-m-d');
$hora = $momento->format('H:i');
$tittime = $momento->format('d/m/Y H:i');

if ($entrada) { 
    echo "        <h2>Modificar registro de entrada: ".$tittime."</h2>\n";
} else {
    echo "        <h2>Modificar registro de salida: ".$tittime."</h2>\n";
}
?>        
        <form method="post" class="ereg-form">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" value="<?= $fecha ?>" required>
            <label for="hora">Hora:</label>
            <input type="time" name="hora" value="<?= $hora ?>" required>
            <br />
            <label for="razon">Justificación:</label>
            <textarea name="razon" rows="4" cols="55" placeholder="Causa de la modificación" required></textarea>
            <br />
            <button type="submit" name="modifica" value="<?= $_POST['editareg'] ?>">Solicitar modificación</button>
        </form>
    </body>
</html>
