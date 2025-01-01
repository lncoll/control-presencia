<?php
$title = "Solicitudes de cambio";
include 'cabecera.php';

$stmt = $conn->stmt_init();
$stmt->prepare("SELECT * FROM cambios WHERE estado = 0;");
$stmt->execute();
$stmt->bind_result($cambio_id, $reg_id, $user_id, $estado, $anterior, $posterior, $comentario);
$stmt->store_result();
?>
        <h2>Solicitudes de cambio</h2>
        <table class="tlistado">
            <tr>
                <th>Usuario</th>
                <th>Original</th>
                <th>Propuesto</th>
                <th>Comentario</th>
                <th>Acción</th>
            </tr>
<?php   
while ($stmt->fetch()) {
    $query = "SELECT username FROM empleados WHERE user_id = ".$user_id.";";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $nombre = $row['username'];
    $result->close();
    echo "            <tr>\n";
    echo "                <td>".$nombre."</td>\n";
    echo "                <td>".$anterior."</td>\n";
    echo "                <td>".$posterior."</td>\n";
    echo "                <td>".$comentario."</td>\n";
    echo "                <td>\n";
    echo "                    <form method='post'><button class='btn' name='aceptar'  value='".$cambio_id."' title='Aceptar' ><img src=img/acepta.png  alt='Aceptar'  width='24'></button></form>\n";
    echo "                    <form method='post'><button class='btn' name='rechazar' value='".$cambio_id."' title='Rechazar'><img src=img/rechaza.png alt='Rechazar' width='24'></button></form>\n";
    echo "                </td>\n";
    echo "            </tr>\n";
}
$stmt->close();
?>
        </table>
    </body>
</html>
