<?php
$titulo = "Edición de registro";
include 'cabecera.php';

$stmt = $conn->stmt_init();
$stmt->prepare("SELECT user_id, reg_time, entrada FROM registros WHERE reg_id = ? ;");
try {
    $stmt->bind_param("i", $_POST['editareg']);
    $stmt->execute();
    $stmt->bind_result($user_id, $reg_time, $entrada);
    $stmt->store_result();
    $stmt->fetch();
    $stmt->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $stmt->close();
    exit();
}

if ($_SESSION['user_id'] != $user_id) {
    $message = "No puedes editar registros de otros usuarios";
    include 'dashboard.php';
    exit();
}

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
