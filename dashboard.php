<?php
include_once 'global.php';

if ($_SESSION['dentro']) { $cuantos = 31; } else { $cuantos = 30; };

$stmt = $conn->stmt_init();
$stmt->prepare("SELECT * FROM registros WHERE user_id = ? ORDER BY reg_id DESC LIMIT ?;");
try {
    $stmt->bind_param("ii", $_SESSION['user_id'], $cuantos);
    $stmt->execute();
    $stmt->store_result();
} catch (Exception $e) {
    $mensaje = "Error: " . $e->getMessage();
    $stmt->close();
}

$titulo = "Dashboard";
include 'cabecera.php';
$reg_sal = 0;

echo "        <h1>Bienvenid@ " . $_SESSION['username'] . " </h1>\n";
echo "        <form method='post' class='reg-form'>\n";
if ($_SESSION['dentro']) {
    echo "            <button type = submit name = register_exit>Registrar salida</button>\n";
} else {
    echo "            <button type = submit name = register_entry>Registrar entrada</button>\n";
}
echo "        </form>\n";
?>
        <table class="tlistado">
            <caption>Ultimos registros</caption>
            <tr><th>Fecha:</th><th colspan=2>Entrada:</th><th colspan="2">Salida:</th><th>Tiempo:</th></tr>
<?php
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($reg_id, $user_id, $reg_time, $entrada, $creado, $modificado);
                while($row = $stmt->fetch()) {
                    if ($entrada) {
                        $ent = new DateTime($reg_time);
                        $fecha = $ent->format('d/m/Y');
                        if ($sal) {
                            $tiempo = tiempostr($ent, $sal);
                            echo "            <tr><td>" . $fecha . "</td><td>" . $ent->format('H:i') . "</td>";
                            echo "<td><form method='post'><button class='btn' name='editareg' value='".$reg_id."' title='Editar'><img src=img/edit.png alt='Editar' width='24'></button></form></td>";
                            echo "<td>" . $sal->format('H:i') . "</td>";
                            echo "<td><form method='post'><button class='btn' name='editareg' value='".$reg_sal."' title='Editar'><img src=img/edit.png alt='Editar' width='24'></button></form></td>";
                            echo "<td>" . $tiempo . "</td></tr>\n";
                        } else {
                            echo "<tr><td>" . $fecha . "</td><td>" . $ent->format('H:i') . "</td>";
                            echo "<td><form method='post'><button class='btn' name='editareg' value='".$reg_id."' title='Editar'><img src=img/edit.png alt='Editar' width='24'></button></form></td>";
                            echo "<td>---</td><td></td><td>---</td>\n";
                        }
                    } else {
                        $sal = new DateTime($reg_time);
                        $reg_sal = $reg_id;
                    }
                }
            } else {
                echo "<tr><td colspan='6'>No hay registros</td></tr>\n";
            }
            $stmt->close();
            ?>
        </table>
    </body>
</html>