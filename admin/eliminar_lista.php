<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: ../loginSesiones/login.php");
    exit();
}

include '../includes/conexion.php';

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    // Verificar si la lista tiene candidatos antes de eliminarla
    $sql_verificar = "SELECT COUNT(*) as total FROM candidatos WHERE id_lista = ?";
    $stmt = $conn->prepare($sql_verificar);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();

    if ($fila['total'] > 0) {
        echo "No se puede eliminar la lista porque tiene candidatos asignados.";
        exit();
    }

    // Si no tiene candidatos, eliminar la lista
    $sql = "DELETE FROM listas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: listas.php");
        exit();
    } else {
        echo "Error al eliminar la lista.";
    }
}
?>
