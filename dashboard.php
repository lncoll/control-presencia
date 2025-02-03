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
//include_once 'global.php';

if ($_SESSION['dentro']) { $cuantos = 31; } else { $cuantos = 30; };

$stmt_das = $conn->stmt_init();
$stmt_das->prepare("SELECT reg_id, reg_time, entrada FROM registros WHERE user_id = ? ORDER BY reg_id DESC LIMIT ?;");
try {
    $stmt_das->bind_param("ii", $_SESSION['user_id'], $cuantos);
    $stmt_das->execute();
    $stmt_das->store_result();
} catch (Exception $e) {
    $mensaje = "Error: " . $e->getMessage();
    $stmt_das->close();
}

$titulo = "Dashboard";
include 'cabecera.php';
$reg_sal = 0;

echo "        <h1>Bienvenid@ " . $_SESSION['username'] . " </h1>\n";
echo "        <form method='post' class='reg-form' id='pos'>\n";
echo "        <p>Habilita el permiso de ubicación para poder realizar el registro</p>\n";
//echo "        <p id='pos'></p>\n";
echo "        </form>\n";
?>
        <script>
var x = document.getElementById('pos');
var dentro = <?php echo $_SESSION['dentro']; ?>;

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(setPosition);
    } else {
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function setPosition(position) {
    if (dentro) {
        x.innerHTML =
"            <button type = submit name = register_exit>Registrar salida</button>\n" + 
"            <input type='hidden' name='latitud' value=" + position.coords.latitude + ">\n" +
"            <input type='hidden' name='longitud' value=" + position.coords.longitude + ">\n" ;
    } else {
        x.innerHTML =
"            <button type = submit name = register_entry>Registrar entrada</button>\n" + 
"            <input type='hidden' name='latitud' value=" + position.coords.latitude + ">\n" +
"            <input type='hidden' name='longitud' value=" + position.coords.longitude + ">\n" ;
    }
}

getLocation();
        </script>
        <table class="tlistado">
            <caption>Ultimos registros</caption>
            <tr><th>Fecha:</th><th colspan=2>Entrada:</th><th colspan="2">Salida:</th><th>Tiempo:</th></tr>
<?php
            if ($stmt_das->num_rows > 0) {
                $stmt_das->bind_result($reg_id, $reg_time, $entrada);
                while($row = $stmt_das->fetch()) {
                    if ($entrada) {
                        $ent = new DateTime($reg_time);
                        $fecha = $ent->format('d/m/Y');
                        if (isset($sal) && $sal) {
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
            $stmt_das->close();
            ?>
        </table>
    </body>
</html>