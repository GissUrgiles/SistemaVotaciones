<?php
require_once "../includes/conexion.php";
session_start();

// Verificar si el votante ha iniciado sesión
if (!isset($_SESSION['cedula'])) {
    header("Location: ../loginSesiones/login.php");
    exit();
}

// Obtener la cédula del votante
$cedula = $_SESSION['cedula'];

// Verificar si el usuario ya ha votado
$verificar = $conn->prepare("SELECT * FROM votos WHERE cedula_votante = ?");
$verificar->bind_param("s", $cedula);
$verificar->execute();
$resultado = $verificar->get_result();

if ($resultado->num_rows > 0) {
    // Si ya votó, redirigir directamente a resultado.php
    header("Location: resultado.php");
    exit();
}

// Si envió el voto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_candidato'])) {
    $id_candidato = $_POST['id_candidato'];

    // Insertar el voto
    $stmt = $conn->prepare("INSERT INTO votos (cedula_votante, id_candidato) VALUES (?, ?)");
    $stmt->bind_param("si", $cedula, $id_candidato);

    if ($stmt->execute()) {
        // Redirigir a resultado.php después de votar
        header("Location: resultado.php");
        exit();
    } else {
        echo "<p style='color:red;'>❌ Error al registrar el voto: " . $conn->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Voto</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <h2>Mi Voto</h2>

    <!-- Botón para cerrar sesión -->
    <a href="../loginSesiones/logout.php" class="cerrar-sesion">Cerrar sesión</a>

    <?php if ($result->num_rows > 0): 
        $row = $result->fetch_assoc(); ?>
        <p>Has votado por: <strong><?= htmlspecialchars($row['candidato']) ?></strong></p>
        <p>Lista: <strong><?= htmlspecialchars($row['lista']) ?></strong></p>
    <?php else: ?>
        <p>❌ No has registrado tu voto aún.</p>
    <?php endif; ?>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
