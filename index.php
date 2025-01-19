<?php
include_once 'global.php';

if (!isset($_SESSION['user_id']) && !isset($_POST['login']) && !isset($_POST['crearconfig'])) {
    include "login.php";
    exit();
}

function login($username, $password) {
    global $conn;
    global $mensaje;

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
        $mensaje = "Error: " . $e->getMessage();
        $result->close();
        return false;
    }
}

function registerEntry($user_id) {
    global $conn;
    global $mensaje;

    if ($_SESSION['dentro']) {
        include "dashboard.php";
        exit();
    }
    $hora = new DateTime('now');
    $hora = $hora->format("Y-m-d H:i");
    try {
        $conn->begin_transaction();
        $query = "INSERT INTO registros (user_id, reg_time, entrada, creado) VALUES ($user_id, '$hora', TRUE, NOW())";
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
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }
}

function registerExit($user_id) {
    global $conn;
    global $mensaje;

    if (!$_SESSION['dentro']) {
        include "dashboard.php";
        exit();
    }
    $hora = new DateTime('now');
    $hora = $hora->format("Y-m-d H:i");
    try {
        $conn->begin_transaction();
        $query = "INSERT INTO registros (user_id, reg_time, entrada, creado) VALUES ($user_id, '$hora', FALSE, NOW())";
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
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }
}

function createEmployee($username, $nombre, $password, $nif, $email, $role) {
    global $conn;
    global $mensaje;

    $username = mysqli_real_escape_string($conn, $username);
    $nombre = mysqli_real_escape_string($conn, $nombre);
    $password = mysqli_real_escape_string($conn, $password);
    $nif = mysqli_real_escape_string($conn, $nif);
    $email = mysqli_real_escape_string($conn, $email);
    $role = mysqli_real_escape_string($conn, $role);

    $query = "SELECT * FROM empleados WHERE username = '$username' OR NIF = '$nif'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $result->close();
        $mensaje = "Usuario o NIF ya existen.";
        return false;
    }
    $result->close();
    
    if (strlen($password) < 8) {
        $mensaje = "La contraseña tiene menos de 8 caracteres.";
        return false;
    }

    try {
        $query = "INSERT INTO empleados (username, nombre, password, NIF, email, dentro, role) VALUES ('$username', '$nombre', PASSWORD('$password'), '$nif', '$email', 0, $role)";
        if ($conn->query($query) === TRUE) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        return false;
    }
}

function editEmployee($user_id, $username, $nombre, $nif, $email, $role) {
    global $conn;
    global $mensaje;

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
        $mensaje = "Error: " . $e->getMessage();
        return false;
    }
}

function editPassword($user_id, $password0, $password1, $password2) {
    global $conn;
    global $mensaje;

    $query = "SELECT * FROM empleados WHERE user_id = $user_id AND password = PASSWORD('$password0')";
    $result = $conn->query($query);
    if ($result->num_rows == 0) {
        $result->close();
        $mensaje = "Contraseña actual incorrecta.";
        return false;
    }
    $result->close();
    if (($password1 != $password2) || (strlen($password1) < 6)) {
        $mensaje = "Las contraseñas no coinciden o tienen menos de 6 caracteres.";
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
            $mensaje = "Error al actualizar la contraseña.";
            return false;
        }
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }
}

function editReg($reg_id, $fecha, $hora, $razon) {
    global $conn;
    global $mensaje;
    
    $newtime = new DateTime($fecha . " " . $hora);
    $nuevotiempo = $newtime->format("Y-m-d H:i");
    $reg_id = mysqli_real_escape_string($conn, $reg_id);
    $razon = mysqli_real_escape_string($conn, $razon);

    try {
        $conn->begin_transaction();
        $query = "SELECT user_id, reg_time FROM registros WHERE reg_id = $reg_id";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        if ($_SESSION['user_id'] != $row['user_id']) {
            $mensaje = "No puedes modificar registros de otros usuarios.";
            $result->close();
            $conn->rollback();
            return false;
        }
        $tiempoviejo = $row['reg_time'];
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }

// comprobar que la nueva fecha no esté en el futuro
    $new = strtotime($nuevotiempo  );
    if ($new > time()) {
        $mensaje = "No puedes seleccionar un momento futuro.";
        $conn->rollback();
        return false;
    }

// comprobar si la nueva hora solapa con periodo anterior o posterior
    $old = strtotime($tiempoviejo);
    if ($old < $new) {
        $query = "SELECT reg_id, reg_time FROM registros WHERE user_id = " . $_SESSION['user_id'] . " AND reg_time BETWEEN '$tiempoviejo' AND '$nuevotiempo' ORDER BY reg_time ASC";
        $periodo = "posterior";
    } else {
        $query = "SELECT reg_id, reg_time FROM registros WHERE user_id = " . $_SESSION['user_id'] . " AND reg_time BETWEEN '$nuevotiempo' AND '$tiempoviejo' ORDER BY reg_time ASC";
        $periodo = "anterior";
    }
    try {
        $result = $conn->query($query);
        if ($result->num_rows > 1) { // el registro viejo siempre está
            $row = $result->fetch_assoc();
            $mensaje = "La nueva hora solapa con un periodo " . $periodo;
            $result->close();
            $conn->rollback();
            return false;
        }
        $anterior = $row['reg_time'];
        $result->close();
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }

    try {
        $query = "INSERT INTO cambios (reg_id, user_id, estado, anterior, posterior, comentario) VALUES ($reg_id, " . $_SESSION['user_id'] . ", 0, '$tiempoviejo', '$nuevotiempo', '$razon')";
        if ($conn->query($query) === TRUE) {
            $query = "UPDATE registros SET reg_time = '$nuevotiempo', modificado = NOW() WHERE reg_id = $reg_id";
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
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }
}

function aceptarCambio($cambio_id) {
    global $conn;
    global $mensaje;

    $conn->begin_transaction();
    $query = "SELECT user_id, reg_id, posterior FROM cambios WHERE id = $cambio_id;";
    try {
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $reg_id = $row['reg_id'];
        $posterior = $row['posterior'];
        $result->close();
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }

    $query = "INSERT INTO mensajes (de, para, texto) VALUES (".$_SESSION['user_id'].", $user_id, 'Tu solicitud de cambio de registro a las $posterior ha sido aceptado.');";
    try {
        if ($conn->query($query) === FALSE) {
            $mensaje = "Ha fallado la aceptación del cambio.";
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }

    $query ="UPDATE cambios SET estado = 1 WHERE id = ".$cambio_id."; ";
    try {
        if ($conn->query($query) === TRUE) {
            $mensaje = "Cambio aceptado.";
            $conn->commit();
            return true;
        } else {
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }
}

function rechazarCambio($cambio_id) {
    global $conn;
    global $mensaje;

    $conn->begin_transaction();
    $query = "SELECT user_id, reg_id, anterior, posterior FROM cambios WHERE id = $cambio_id;";
    try {
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $reg_id = $row['reg_id'];
        $posterior = $row['posterior'];
        $anterior = $row["anterior"];
        $result->close();
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }

    $query = "INSERT INTO mensajes (de, para, texto) VALUES (".$_SESSION['user_id'].", $user_id, 'Tu solicitud de cambio de registro a las $posterior ha sido rechazado.');";
    try {
        if ($conn->query($query) === FALSE) {
            $mensaje = "Ha fallado el rechazo del cambio.";
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }

    $query = "UPDATE registros SET reg_time = '$anterior', modificado = NOW() WHERE reg_id = $reg_id;";
    try {
        if ($conn->query($query) === FALSE) {
            $mensaje = "Ha fallado el rechazo del cambio.";
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
    }

    $query ="UPDATE cambios SET estado = 2 WHERE id = ".$cambio_id."; ";
    try {
        if ($conn->query($query) === TRUE) {
            $conn->commit();   
            $mensaje = "Cambio rechazado.";
            return true;
        } else {
            $mensaje = "Ha fallado el rechazo del cambio.";
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $conn->rollback();
        return false;
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
    $nifempresa = $_POST['nifempresa'];

    if (strlen($_POST['password']) < 8 || $_POST['password'] != $_POST['password2']) {
        $mensaje = "Las contraseñas no coinciden o tienen menos de 8 caracteres.";
        include "creaconfig.php";
        exit();
    }
    
    try {
        $conn = new mysqli($dbserver, $dbuser, $dbpass, $dbname);
    } catch (Exception $e) {
        $mensaje = "No se pudo conectar a la base de datos, revise los parametros. " . $e->getMessage();
        include "creaconfig.php";
        exit();
    }
    $mensaje = "Conexión establecida, ";

    try {
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
        fwrite($fichero, "\$nifempresa = '$nifempresa';\n");
        fclose($fichero);
        $mensaje = " Configuración guardada, ";
    } catch (Exception $e) {
        $mensaje = "No se pudo crear el fichero de configuración, consulte al administrador web. " . $e->getMessage();
        include "creaconfig.php";
        exit();
    }

    try {
        $conn->begin_transaction();
        $query = "CREATE TABLE IF NOT EXISTS `empleados` (`user_id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(32) NOT NULL, `nombre` varchar(64) NOT NULL, `password` varchar(64) NOT NULL, `NIF` varchar(16) NOT NULL, `email` varchar(64) NOT NULL, `dentro` tinyint(1) NOT NULL, `role` int(11) NOT NULL, PRIMARY KEY (`user_id`), UNIQUE KEY `username` (`username`), UNIQUE KEY `NIF` (`NIF`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        if ($conn->query($query) != TRUE) {
            $conn->rollback();
            $mensaje = "Error al crear la tabla empleados";
            include "creaconfig.php";
            exit();
        }
        $query = "CREATE TABLE IF NOT EXISTS `registros` (`reg_id` int(11) NOT NULL AUTO_INCREMENT, `user_id` int(11) NOT NULL, `reg_time` datetime DEFAULT NULL, `entrada` tinyint(1) NOT NULL, `creado` datetime NOT NULL, `modificado` datetime DEFAULT NULL, `spare` int(11) DEFAULT NULL, PRIMARY KEY (`reg_id`), KEY `user_id` (`user_id`) USING BTREE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        if ($conn->query($query) != TRUE) {
            $conn->rollback();
            $mensaje = "Error al crear la tabla registros";
            include "creaconfig.php";
            exit();
        }
        $query = "CREATE TABLE IF NOT EXISTS `cambios` ( `id` int(11) NOT NULL AUTO_INCREMENT, `reg_id` int(11) NOT NULL, `user_id` int(11) NOT NULL, `estado` int(11) NOT NULL, `anterior` datetime NOT NULL, `posterior` datetime NOT NULL, `comentario` text NOT NULL, PRIMARY KEY (`id`), KEY `estado` (`estado`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        if ($conn->query($query) != TRUE) {
            $conn->rollback();
            $mensaje = "Error al crear la tabla cambios";
            include "creaconfig.php";
            exit();
        }
        $query = "CREATE TABLE IF NOT EXISTS `mensajes` ( `msg_id` int(11) NOT NULL AUTO_INCREMENT, `estado` int(11) NOT NULL, `de` int(11) NOT NULL, `para` int(11) NOT NULL, `texto` text NOT NULL, PRIMARY KEY (`msg_id`), KEY `para_estado` (`para`,`estado`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
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
        $mensaje = "No se pudo crear las tablas de datos, consulte al administrador web. " . $e->getMessage();
        include "creaconfig.php";
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
            $mensaje .= " Falló el registro.";
        }
        include "dashboard.php";
    } elseif (isset($_POST['register_exit'])) {
        if (registerExit($_SESSION['user_id'])) {
            $mensaje = "Salida registrada.";
        } else {
            $mensaje .= " Falló el registro.";
        }
        include "dashboard.php";
    }elseif (isset($_POST['modifica'])) {
        if (editReg($_POST['modifica'], $_POST['fecha'], $_POST['hora'], $_POST['razon'])) {
            $mensaje = "Registro modificado, a espera de validación.";
        } /* else {
            $mensaje = "Fallo al modificar el registro.";
        } */
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
    } elseif (isset($_POST['pdf'])) {
        include "listadopdf.php";
    }elseif (isset($_POST['editareg'])) {
        include "editreg.php";
    }elseif (isset($_POST['cambios'])) {
        include "cambios.php";
    } elseif (isset($_POST['create_employee']) && $_SESSION['role'] == 10) {
        if (createEmployee($_POST['username'], $_POST['nombre'], $_POST['password'], $_POST['NIF'], $_POST['email'], $_POST['role'])) {
            $mensaje = "Usuario creado.";
        } else {
            include "crear.php";
            exit();
        }
        include "dashboard.php";
    } elseif (isset($_POST['edit_employee'])) {
        if (editEmployee($_POST['user_id'], $_POST['username'], $_POST['nombre'], $_POST['NIF'], $_POST['email'], $_POST['role'])) {
            $mensaje = "Usuario actualizado.";
        } else {
            include "edit.php";
            exit();
        }
        include "dashboard.php";
    } elseif (isset($_POST['edit_password'])) {
        if (editPassword($_POST['user_id'], $_POST['password0'], $_POST['password1'], $_POST['password2'])) {
            $mensaje = "Password actualizado.";
        } else {
            include "edit.php";
            exit();
        }
        include "dashboard.php";
    } elseif (isset($_POST['aceptar_cambio'])) {
        aceptarCambio($_POST['aceptar_cambio']);
        include "cambios.php";
    } elseif (isset($_POST['rechazar_cambio'])) {
        rechazarCambio($_POST['rechazar_cambio']);
        include "cambios.php";
    } elseif (isset($_POST['logout'])) {
        session_destroy();
        include "login.php";
    } elseif (isset($_POST['crearconfig'])) {
        crearconfiguracion();
        include "login.php";
    } else {
        echo "Solicitud no válida.<br />\n";
        print_r($_POST);
    }
} else {
    include "dashboard.php";
}
