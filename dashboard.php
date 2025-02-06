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
echo "        <div id='map'></div>\n";
echo "        <form method='post' class='reg-form' id='pos'>\n";
echo "        <p>Esperando ubicación</p>\n";
echo "        </form>\n";
?>
        <script>
var x = document.getElementById('pos');
var map = L.map('map').setView([0, 0], 16);
var marker = L.marker([0, 0]).addTo(map);
var circle = L.circle([0, 0], {
    color: 'blue',
    fillColor: '#03f',
    fillOpacity: 0.3,
    radius: 10
}).addTo(map);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
}).addTo(map);

function geoError(error){
    switch(error.code) {
        case error.PERMISSION_DENIED:
            x.innerHTML = "<p>El usuario no permite la petición de geolocalización.<br>Permita la ubicación para poder realizar el registro.</p>";
            break;
        case error.POSITION_UNAVAILABLE:
            x.innerHTML = "<p>Información de geolocalización no disponible.<br>Pulse en 'Inicio' para volver a intentarlo.</p>";
            break;
        case error.TIMEOUT:
            x.innerHTML = "<p>La petición ha tardado demasiado tiempo en responder.<br>Pulse en 'Inicio' para volver a intentarlo.</p>";
            break;
        case error.UNKNOWN_ERROR:
            x.innerHTML = "Error desconocido";
            break;
    }
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(setPosition, geoError, {enableHighAccuracy: false, timeout: 5000, maximumAge: 0});
    }
}

function setPosition(position) {
    var dentro = <?php echo $_SESSION['dentro']; ?>;
    map.setView([position.coords.latitude, position.coords.longitude], 16);
    marker.setLatLng([position.coords.latitude, position.coords.longitude]);
    circle.setLatLng([position.coords.latitude, position.coords.longitude]);
    circle.setRadius(position.coords.accuracy / 2);

    if (dentro) {
        x.innerHTML =
"            <button type = submit name = register_exit>Registrar salida</button>\n" + 
"            <input type='hidden' name='latitud' value=" + position.coords.latitude + ">\n" +
"            <input type='hidden' name='longitud' value=" + position.coords.longitude + ">\n" +
"            <input type='hidden' name='timezone' value=" + Intl.DateTimeFormat().resolvedOptions().timeZone + ">\n";
    } else {
        x.innerHTML =
"            <button type = submit name = register_entry>Registrar entrada</button>\n" + 
"            <input type='hidden' name='latitud' value=" + position.coords.latitude + ">\n" +
"            <input type='hidden' name='longitud' value=" + position.coords.longitude + ">\n" +
"            <input type='hidden' name='timezone' value=" + Intl.DateTimeFormat().resolvedOptions().timeZone + ">\n";
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
        <p class="notapie">* Nota, el tiempo se calcula en bloques de <?= $bloquetiempo ?> minutos.</p>
<?php
include 'pie.php';