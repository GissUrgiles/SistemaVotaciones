<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: ../loginSesiones/login.php");
    exit();
}

include '../includes/conexion.php';

// Verificar si el ID es válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID de candidato no válido.'); window.location='candidatos.php';</script>";
    exit();
}

$id = $_GET['id'];

// Verificar si el candidato existe
$sql_check = "SELECT * FROM candidatos WHERE id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $id);
$stmt_check->execute();
$resultado = $stmt_check->get_result();
$candidato = $resultado->fetch_assoc();

if (!$candidato) {
    echo "<script>alert('Candidato no encontrado.'); window.location='candidatos.php';</script>";
    exit();
}

// Verificar si el candidato está en una lista
$sql_lista = "SELECT id FROM listas WHERE id_presidente = ? OR id_vicepresidente = ?";
$stmt_lista = $conn->prepare($sql_lista);
$stmt_lista->bind_param("ii", $id, $id);
$stmt_lista->execute();
$resultado_lista = $stmt_lista->get_result();

if ($resultado_lista->num_rows > 0) {
    echo "<script>alert('No se puede eliminar este candidato porque está asignado a una lista.'); window.location='candidatos.php';</script>";
    exit();
}

// Proceder con la eliminación
$sql = "DELETE FROM candidatos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Candidato eliminado exitosamente.'); window.location='candidatos.php';</script>";
} else {
    echo "<script>alert('Error al eliminar candidato.'); window.location='candidatos.php';</script>";
}
?>
