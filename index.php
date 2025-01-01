<?php
include_once 'global.php';

if (!isset($_SESSION['user_id']) && !isset($_POST['login'])) {
    include "login.php";
    exit();
}

function login($username, $password) {
    global $conn;
    try {
        $stmt = $conn->stmt_init();
        $stmt->prepare("SELECT * FROM empleados WHERE username = ? AND password = PASSWORD(?) AND role > 0");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $username, $nombre, $password, $NIF, $email, $dentro, $role);
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $role;
            $_SESSION['NIF'] = $NIF;
            $_SESSION['email'] = $email;
            $_SESSION['username'] = $username;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['dentro'] = $dentro;
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $stmt->close();
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
        $stmt = $conn->stmt_init();
        $stmt->prepare("INSERT INTO registros (user_id, reg_time, entrada, creado) VALUES (?, NOW(), TRUE, NOW())");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $stmt->prepare("UPDATE empleados SET dentro = 1 WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $_SESSION['dentro'] = true;
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $stmt->close();
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
        $stmt = $conn->stmt_init();
        $stmt->prepare("INSERT INTO registros (user_id, reg_time, entrada, creado) VALUES (?, NOW(), FALSE, NOW())");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $stmt->prepare("UPDATE empleados SET dentro = 0 WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $_SESSION['dentro'] = false;
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $stmt->close();
        exit();
    }
}

function createEmployee($username, $nombre, $password, $nif, $email, $role) {
    global $conn;
    try {
        $stmt = $conn->stmt_init();
        $stmt->prepare("INSERT INTO empleados (username, nombre, password, NIF, email, dentro, role) VALUES (?, ?, PASSWORD(?), ?, ?, 0, ?)");
        $stmt->bind_param("sssssi", $username, $nombre, $password, $nif, $email, $role);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $stmt->close();
        print_r($_POST);
        exit();
    }
}

function editEmployee($user_id, $username, $nombre, $nif, $email, $role) {
    global $conn;
    try {
        $stmt = $conn->stmt_init();
        $stmt->prepare("UPDATE empleados SET username = ?, nombre = ?, NIF = ?, email = ?, role = ? WHERE user_id = ?");
        $stmt->bind_param("sssssi", $username, $nombre, $nif, $email, $role, $user_id);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $stmt->close();
        exit();
    }
}

function editPassword($user_id, $password1, $password2) {
    global $conn;
    if (($password1 != $password2) || (strlen($password1) < 6)) {
        return false;
    }
    try {
        $stmt = $conn->stmt_init();
        $stmt->prepare("UPDATE empleados SET password = PASSWORD(?) WHERE user_id = ?");
        $stmt->bind_param("si", $password1,  $user_id);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $stmt->close();
        exit();
    }
}

function editReg($reg_id, $fecha, $hora, $razon) {
    global $conn;
    $newtime = new DateTime($fecha . " " . $hora);
    $posterior = $newtime->format("Y-m-d H:i");
    $conn->begin_transaction();

    try {
        $stmt = $conn->stmt_init();
        $stmt->prepare("SELECT user_id, reg_time FROM registros WHERE reg_id = ?");
        $stmt->bind_param("i", $reg_id);
        $stmt->execute();
        $stmt->bind_result($user_id, $anterior);
        $stmt->store_result();
        $stmt->fetch();
        if ($_SESSION['user_id'] != $user_id) {
            $stmt->close();
            return false;
        }
    } catch (Exception $e) {
        $stmt->close();
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        exit();
    }
    $stmt->close();

    try {
        $stmt = $conn->stmt_init();
        $stmt->prepare("INSERT INTO cambios (reg_id, user_id, estado, anterior, posterior, comentario) VALUES (?, ?, 0, ?, ?, ?)");
        $stmt->bind_param("iisss", $reg_id, $_SESSION['user_id'], $anterior, $posterior, $razon);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $stmt->close();
            $stmt = $conn->stmt_init();
            $stmt->prepare("UPDATE registros SET reg_time = ?, modificado = NOW() WHERE reg_id = ?");
            $stmt->bind_param("si", $posterior,$reg_id);
            $stmt->execute();
            $stmt->close();
            $conn->commit();
            return true;
        } else {
            $stmt->close();
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $stmt->close();
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
    } elseif (isset($_POST['logout'])) {
        session_destroy();
        include "login.php";
//        header("Location: index.php");
    } else {
        echo "Invalid request.<br />\n";
        print_r($_POST);
    }
} else {
    include "dashboard.php";
}
