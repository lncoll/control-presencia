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

// Purpose: List all users in the database

if (!isset($_POST['role'])) $role = ""; else $role = $_POST['role'];
if ($_POST['users'] == "") $busca_nombre = "%"; else $busca_nombre = "%".$_POST['users']."%";

try {
    $stmt_usr = $conn->stmt_init();
    if ($role == "") { 
        $stmt_usr->prepare("SELECT user_id, username, nombre FROM empleados WHERE nombre LIKE ? ORDER BY user_id ASC;");
        $stmt_usr->bind_param("s", $busca_nombre);
    } else {
        $stmt_usr->prepare("SELECT user_id, username, nombre FROM empleados WHERE nombre LIKE ? AND role = ? ORDER BY user_id ASC;");
        $stmt_usr->bind_param("si", $busca_nombre, $role);
    }
    $stmt_usr->execute();
    $stmt_usr->bind_result($user_id, $username, $nombre);
    $stmt_usr->store_result();
} catch (Exception $e) {
    $mensaje = "Error: " . $e->getMessage();
    $stmt_usr->close();
}

$titulo = "Listado de usuarios";
include 'cabecera.php';
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
if ($stmt_usr->num_rows > 0) {
    echo "<table class='tlistado'>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>";
    while($row = $stmt_usr->fetch()) {
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
    echo "<h2>0 resultados</h2>";
}
$stmt_usr->close();
include 'pie.php';