<?php
include_once 'global.php';

if (!isset($_SESSION['user_id']) && !isset($_POST['login']) && !isset($_POST['crearconfig'])) {
    include "login.php";
    exit();
}

function login($username, $password) {
    global $conn;
    $use = mysqli_real_escape_string($conn, $username);
    $pas = mysqli_real_escape_string($conn, $password);
    try {
        $query = "SELECT * FROM `empleados` WHERE `username` = '$use' AND `password` = PASSWORD('$pas') AND `role` > 0;";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['NIF'] = $row['NIF'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['dentro'] = $row['dentro'];
            $result->close();
            return true;
        } else {
            $result->close();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $result->close();
        exit();
    }
}

function registerEntry($user_id) {
    global $conn;
    if ($_SESSION['dentro']) {
        include "dashboard.php";
        exit();
    }
    try {
        $conn->begin_transaction();
        $query = "INSERT INTO registros (user_id, reg_time, entrada, creado) VALUES ($user_id, NOW(), TRUE, NOW())";
        if ($conn->query($query) === TRUE) {
            $query = "UPDATE empleados SET dentro = 1 WHERE user_id = $user_id";
            if ($conn->query($query) === TRUE) {
                $_SESSION['dentro'] = true;
                $conn->commit();
                return true;
            } else {
                $conn->rollback();
                return false;
            }
        } else {
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $conn->rollback();
        exit();
    }
}

function registerExit($user_id) {
    global $conn;
    if (!$_SESSION['dentro']) {
        include "dashboard.php";
        exit();
    }
    try {
        $conn->begin_transaction();
        $query = "INSERT INTO registros (user_id, reg_time, entrada, creado) VALUES ($user_id, NOW(), FALSE, NOW())";
        if ($conn->query($query) === TRUE) {
            $query = "UPDATE empleados SET dentro = 0 WHERE user_id = $user_id";
            if ($conn->query($query) === TRUE) {
                $_SESSION['dentro'] = false;
                $conn->commit();
                return true;
            } else {
                $conn->rollback();
                return false;
            }
        } else {
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $conn->rollback();
        exit();
    }
}

function createEmployee($username, $nombre, $password, $nif, $email, $role) {
    global $conn;

    $username = mysqli_real_escape_string($conn, $username);
    $nombre = mysqli_real_escape_string($conn, $nombre);
    $password = mysqli_real_escape_string($conn, $password);
    $nif = mysqli_real_escape_string($conn, $nif);
    $email = mysqli_real_escape_string($conn, $email);
    $role = mysqli_real_escape_string($conn, $role);

    try {
        $query = "INSERT INTO empleados (username, nombre, password, NIF, email, dentro, role) VALUES ('$username', '$nombre', PASSWORD('$password'), '$nif', '$email', 0, $role)";
        if ($conn->query($query) === TRUE) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        print_r($_POST);
        exit();
    }
}

function editEmployee($user_id, $username, $nombre, $nif, $email, $role) {
    global $conn;

    $user_id = mysqli_real_escape_string($conn, $user_id);
    $username = mysqli_real_escape_string($conn, $username);
    $nombre = mysqli_real_escape_string($conn, $nombre);
    $nif = mysqli_real_escape_string($conn, $nif);
    $email = mysqli_real_escape_string($conn, $email);
    $role = mysqli_real_escape_string($conn, $role);

    try {
        $query = "UPDATE empleados SET username = '$username', nombre = '$nombre', NIF = '$nif', email = '$email', role = '$role' WHERE user_id = $user_id";
        if ($conn->query($query) === TRUE) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

function editPassword($user_id, $password1, $password2) {
    global $conn;

    if (($password1 != $password2) || (strlen($password1) < 6)) {
        return false;
    }

    $user_id = mysqli_real_escape_string($conn, $user_id);
    $password1 = mysqli_real_escape_string($conn, $password1);
    $password2 = mysqli_real_escape_string($conn, $password2);

    try {
        $query = "UPDATE empleados SET password = PASSWORD('$password1') WHERE user_id = $user_id";
        if ($conn->query($query) === TRUE) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

function editReg($reg_id, $fecha, $hora, $razon) {
    global $conn;
    
    $newtime = new DateTime($fecha . " " . $hora);
    $posterior = $newtime->format("Y-m-d H:i");
    $reg_id = mysqli_real_escape_string($conn, $reg_id);
    $razon = mysqli_real_escape_string($conn, $razon);

    try {
        $conn->begin_transaction();
        $quey = "SELECT user_id, reg_time FROM registros WHERE reg_id = $reg_id";
        $result = $conn->query($quey);
        $row = $result->fetch_assoc();
        if ($_SESSION['user_id'] != $row['user_id']) {
            $result->close();
            return false;
        }
        $anterior = $row['reg_time'];
    } catch (Exception $e) {
        $result->close();
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        exit();
    }

    try {
        $query = "INSERT INTO cambios (reg_id, user_id, estado, anterior, posterior, comentario) VALUES ($reg_id, " . $_SESSION['user_id'] . ", 0, '$anterior', '$posterior', '$razon')";
        if ($conn->query($query) === TRUE) {
            $query = "UPDATE registros SET reg_time = '$posterior', modificado = NOW() WHERE reg_id = $reg_id";
            if ($conn->query($query) === TRUE) {
                $conn->commit();
                return true;
            } else {
                $conn->rollback();
                return false;
            }
        } else {
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

function aceptarCambio($cambio_id) {
    global $conn;
    $query ="UPDATE cambios SET estado = 1 WHERE id = ".$cambio_id."; ";
    try {
        if ($conn->query($query) === TRUE) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

function rechazarCambio($cambio_id) {
    global $conn;
    $conn->begin_transaction();
    $query = "SELECT reg_id, anterior FROM cambios WHERE id = ".$cambio_id.";";
    try {
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $reg_id = $row['reg_id'];
        $anterior = $row['anterior'];
        $result->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $conn->rollback();
        exit();
    }

    $query = "UPDATE registros SET reg_time = '$anterior', modificado = NOW() WHERE reg_id = $reg_id;";
    try {
        if ($conn->query($query) === FALSE) {
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $conn->rollback();
        exit();
    }

    $query ="UPDATE cambios SET estado = 2 WHERE id = ".$cambio_id."; ";
    try {
        if ($conn->query($query) === TRUE) {
            $conn->commit();   
            return true;
        } else {
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $conn->rollback();
        exit();
    }
}

function crearconfiguracion(){
    global $mensaje;
    global $nombreempresa;

    $dbserver = $_POST['dbserver'];
    $dbuser = $_POST['dbuser'];
    $dbpass = $_POST['dbpass'];
    $dbname = $_POST['dbname'];
    $nombreempresa = $_POST['nombreempresa'];

    if (strlen($_POST['password']) < 8 || $_POST['password'] != $_POST['password2']) {
        $mensaje = "Las contraseñas no coinciden o tienen menos de 8 caracteres.";
        include "creaconfig.php";
        exit();
    }
    
    try {
        $conn = new mysqli($dbserver, $dbuser, $dbpass, $dbname);

        if ($conn->connect_error) {
            $mensaje = "No se pudo conectar a la base de datos, revise los parametros";
            include "creaconfig.php";
            exit();
        }
        $mensaje = "Conexión establecida, ";

        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $password2 = mysqli_real_escape_string($conn, $_POST['password2']);
        $nif = mysqli_real_escape_string($conn, $_POST['NIF']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        $dentro = mysqli_real_escape_string($conn, $_POST['dentro']);

        $fichero = fopen("config.php", "w");
        fwrite($fichero, "<?php\n");
        fwrite($fichero, "\$dbserver = '$dbserver';\n");
        fwrite($fichero, "\$dbuser = '$dbuser';\n");
        fwrite($fichero, "\$dbpass = '$dbpass';\n");
        fwrite($fichero, "\$dbname = '$dbname';\n");
        fwrite($fichero, "\$nombreempresa = '$nombreempresa';\n");
        fclose($fichero);
        $mensaje = " Configuración guardada, ";

        $conn->begin_transaction();
        $query = "CREATE TABLE IF NOT EXISTS empleados (user_id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) NOT NULL, nombre VARCHAR(50) NOT NULL, password CHAR(41) NOT NULL, NIF CHAR(9) NOT NULL, email VARCHAR(50) NOT NULL, dentro BOOLEAN NOT NULL, role INT NOT NULL);";
        if ($conn->query($query) != TRUE) {
            $conn->rollback();
            $mensaje = "Error al crear la tabla empleados";
            include "creaconfig.php";
            exit();
        }
        $query = "CREATE TABLE IF NOT EXISTS registros (reg_id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, reg_time DATETIME NOT NULL, entrada BOOLEAN NOT NULL, creado DATETIME NOT NULL, modificado DATETIME);";
        if ($conn->query($query) != TRUE) {
            $conn->rollback();
            $mensaje = "Error al crear la tabla registros";
            include "creaconfig.php";
            exit();
        }
        $query = "CREATE TABLE IF NOT EXISTS cambios (id INT AUTO_INCREMENT PRIMARY KEY, reg_id INT NOT NULL, user_id INT NOT NULL, estado INT NOT NULL, anterior DATETIME NOT NULL, posterior DATETIME NOT NULL, comentario TEXT);";
        if ($conn->query($query) != TRUE) {
            $conn->rollback();
            $mensaje = "Error al crear la tabla cambios";
            include "creaconfig.php";
            exit();
        }
        $query = "CREATE TABLE IF NOT EXISTS `mensajes` (`msg_id` int(11) NOT NULL AUTO_INCREMENT, `estado` int(11) NOT NULL, `de` int(11) NOT NULL, `para` int(11) NOT NULL, `texto` text NOT NULL, PRIMARY KEY (`msg_id`), KEY `para_estado` (`para`,`estado`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        if ($conn->query($query) != TRUE) {
            $conn->rollback();
            $mensaje = "Error al crear la tabla mensajes";
            include "creaconfig.php";
            exit();
        }
        $mensaje .= "Tablas creadas, ";
        $query = "INSERT INTO empleados (username, nombre, password, NIF, email, dentro, role) VALUES ('$username', '$nombre', PASSWORD('$password'), '$nif', '$email', $dentro, $role)";
        if ($conn->query($query) != TRUE) {
            $conn->rollback();
            $mensaje = "Error al crear el usuario administrador";
            include "creaconfig.php";
            exit();
        }
        $mensaje .= "Usuario administrador creado, ";
        $conn->commit();
        return true;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $conn->rollback();
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['login'])) {
        if (login($_POST['username'], $_POST['password'])) {
            include "dashboard.php";
        } else {
            $mensaje = "Usuario o contraseña no válidos.";
            include "login.php";
        }
    } elseif (isset($_POST['register_entry'])) {
        if (registerEntry($_SESSION['user_id'])) {
            $mensaje = "Entrada registrada.";
        } else {
            $mensaje = "Falló el registro.";
        }
        include "dashboard.php";
    } elseif (isset($_POST['register_exit'])) {
        if (registerExit($_SESSION['user_id'])) {
            $mensaje = "Salida registrada.";
        } else {
            $mensaje = "Falló el registro.";
        }
        include "dashboard.php";
    }elseif (isset($_POST['modifica'])) {
        if (editReg($_POST['modifica'], $_POST['fecha'], $_POST['hora'], $_POST['razon'])) {
            $mensaje = "Registro modificado, a espera de validación.";
        } else {
            $mensaje = "Fallo al modificar el registro.";
        }
        include "dashboard.php";
    } elseif (isset($_POST['dashboard'])) {
        include "dashboard.php";
    } elseif (isset($_POST['editame'])) {
        include "edit.php";
    } elseif (isset($_POST['crear'])) {
        include "crear.php";
    } elseif (isset($_POST['users'])) {
        include "listusers.php";
    } elseif (isset($_POST['listar'])) {
        include "listado.php";
    }elseif (isset($_POST['editareg'])) {
        include "editreg.php";
    }elseif (isset($_POST['cambios'])) {
        include "cambios.php";
    } elseif (isset($_POST['create_employee']) && $_SESSION['role'] == 10) {
        if (createEmployee($_POST['username'], $_POST['nombre'], $_POST['password'], $_POST['NIF'], $_POST['email'], $_POST['role'])) {
            $mensaje = "Usuario creado.";
        } else {
            $mensaje = "Fallo al crear usuario.";
        }
        include "dashboard.php";
    } elseif (isset($_POST['edit_employee'])) {
        if (editEmployee($_POST['user_id'], $_POST['username'], $_POST['nombre'], $_POST['NIF'], $_POST['email'], $_POST['role'])) {
            $mensaje = "Usuario actualizado.";
        } else {
            $mensaje = "Fallo al actualizar.";
        }
        include "dashboard.php";
    } elseif (isset($_POST['edit_password'])) {
        if (editPassword($_POST['user_id'], $_POST['password1'], $_POST['password2'])) {
            $mensaje = "Password actualizado.";
        } else {
            $mensaje = "Las contraseñas no coinciden o tienen menos de 6 caracteres.";
        }
        include "dashboard.php";
    } elseif (isset($_POST['aceptar_cambio'])) {
        if (aceptarCambio($_POST['aceptar_cambio'])) {
            $mensaje = "Cambio aceptado.";
        } else {
            $mensaje = "Fallo al aceptar el cambio.";
        }
        include "cambios.php";
    } elseif (isset($_POST['rechazar_cambio'])) {
        if (rechazarCambio($_POST['rechazar_cambio'])) {
            $mensaje = "Cambio rechazado.";
        } else {
            $mensaje = "Fallo al rechazar el cambio.";
        }
        include "cambios.php";
    } elseif (isset($_POST['logout'])) {
        session_destroy();
        include "login.php";
    } elseif (isset($_POST['crearconfig'])) {
        crearconfiguracion();
        include "login.php";
    } else {
        echo "Invalid request.<br />\n";
        print_r($_POST);
    }
} else {
    include "dashboard.php";
}
