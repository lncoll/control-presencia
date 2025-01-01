<?php
// Purpose: List all users in the database
$titulo = "Listado de usuarios";
include 'cabecera.php';

if ($_POST['users'] == "") $busca_nombre = "%"; else $busca_nombre = "%".$_POST['users']."%";

try {
    $stmt = $conn->stmt_init();
    if ($_POST['role'] == "") {
        $stmt->prepare("SELECT user_id, username, nombre FROM empleados WHERE nombre LIKE ? ORDER BY user_id ASC;");
        $stmt->bind_param("s", $busca_nombre);
    } else {
        $stmt->prepare("SELECT user_id, username, nombre FROM empleados WHERE nombre LIKE ? AND role = ? ORDER BY user_id ASC;");
        $stmt->bind_param("si", $busca_nombre, $_POST['role']);
    }
    $stmt->execute();
    $stmt->bind_result($user_id, $username, $nombre);
    $stmt->store_result();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $stmt->close();
    exit();
}
?>
        <form method="post" class="busca-form">
            Filtrar busqueda por: 
            <input type="text" name="users" placeholder="Nombre a buscar">
            <select name="role">
                <option value="" >Todos</option>
                <option value="0" >Desabilitado</option>
                <option value="1" >Usuario</option>
                <option value="10" >Administrador</option>
            </select>

            <button type="submit">Filtrar</button>
        </form>
        <h1>Listado de usuarios</h1>
<?php
if ($stmt->num_rows > 0) {
    echo "<table class='tlistado'>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>";
    while($row = $stmt->fetch()) {
        echo "<tr>
                <td>" . $user_id. "</td>
                <td>" . $username. "</td>
                <td>" . $nombre. "</td>
                <td>
                    <form method='post'>
                        <button class='btn' name='editame' value='".$user_id."' title='Editar'><img src=img/edit.png alt='Editar' width='24'></button>
                        <button class='btn' name='listar' value='".$user_id."' title='Listar'><img src=img/lista.png alt='Listar' width='24'></button>
                    </form>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
$stmt->close();

?>
    </body>
</html>