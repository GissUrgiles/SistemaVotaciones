<?php
session_start();
require_once "../includes/conexion.php";

// Si la sesión ya está activa, evitar redirecciones infinitas
if (isset($_SESSION["usuario"]) && isset($_SESSION["tipo_usuario"])) {
    echo "⚠️ Sesión activa detectada: " . $_SESSION["usuario"] . " | Tipo: " . $_SESSION["tipo_usuario"] . "<br>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos del formulario y limpiar espacios en blanco
    $cedula_o_usuario = trim($_POST["cedula"]);
    $clave = trim($_POST["password"]);

    // Validar que no estén vacíos
    if (empty($cedula_o_usuario) || empty($clave)) {
        die("Todos los campos son obligatorios. <a href='login.php'>Intentar de nuevo</a>");
    }

    // 🔹 Verificar si es un votante
    $sql = "SELECT id, clave FROM votantes WHERE cedula = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cedula_o_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        // 🔒 Verificar contraseña encriptada con Bcrypt
        if (password_verify($clave, $usuario["clave"])) {
            $_SESSION["usuario"] = $cedula_o_usuario;
            $_SESSION["tipo_usuario"] = "votante";
            header("Location: ../votante/votar.php");
            exit();
        } else {
            die("Contraseña incorrecta. <a href='login.php'>Intentar de nuevo</a>");
        }
    }

    // 🔹 Verificar si es administrador
    $sql = "SELECT id, clave FROM administradores WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cedula_o_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $admin = $resultado->fetch_assoc();
        
        // 🔒 Verificar contraseña encriptada con Bcrypt
        if (password_verify($clave, $admin["clave"])) {
            $_SESSION["usuario"] = $cedula_o_usuario;
            $_SESSION["tipo_usuario"] = "admin";
            header("Location: ../admin/dashboard.php");
            exit();
        } else {
            die("Contraseña incorrecta. <a href='login.php'>Intentar de nuevo</a>");
        }
    }

    // ❌ Si no se encuentra en votantes ni administradores
    die("Usuario o cédula no registrados. <a href='login.php'>Intentar de nuevo</a>");
}

$conn->close();
?>
