<?php
$titulo = "Mensajes";
include 'cabecera.php';

echo "        <h2>Mensajes</h2>\n";
try {
    $stmt = $conn->stmt_init();
    $stmt->prepare("SELECT `mensajes`.`estado`,`mensajes`.`hora`,`mensajes`.`texto`,`empleados`.`username`  FROM `mensajes` JOIN `empleados` ON `mensajes`.`de` = `empleados`.`user_id` WHERE `mensajes`.`para` = ?;");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $stmt->bind_result($estado, $hora, $texto, $remite);
    $stmt->store_result();
    if ($stmt->num_rows>0){
        while ($stmt->fetch()){
            echo "        <div class='mensajes'>\n";
            if ($estado)
                echo "            <h3>De: ".$remite." - hora: ".$hora." - Leido</h3>\n";
            else
                echo "            <h3>De: ".$remite." - hora: ".$hora." - Nuevo</h3>\n";
            echo "            <hr>\n";
            echo "            ".$texto."\n";
            echo "        </div>\n";
        }
    }
} catch (Exception $e) {
    $mensaje = "Error cargando mensajes. " . $e->getMessage();
    exit();
}

?>
    </body>
</html>
