<?php
// Datos de conexión a la base de datos
$host = "localhost"; // Servidor
$user = "root"; // Usuario de MySQL (por defecto en XAMPP)
$password = ""; // Contraseña (vacía por defecto en XAMPP)
$database = "votaciones"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer codificación de caracteres
$conn->set_charset("utf8");
?>

