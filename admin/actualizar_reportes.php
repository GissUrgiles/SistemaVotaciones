<?php
include '../includes/conexion.php';

function obtenerDatosReporte($conn, $columna) {
    $sql = "SELECT $columna, SUM(cantidad_votos) as total_votos FROM informe_votos GROUP BY $columna";
    $resultado = $conn->query($sql);
    $datos = [];

    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }

    return $datos;
}

$datosProvincia = obtenerDatosReporte($conn, 'provincia');
$datosCiudad = obtenerDatosReporte($conn, 'ciudad');
$datosGenero = obtenerDatosReporte($conn, 'genero');

$conn->close();

// Devuelve los datos en formato JSON
echo json_encode([
    'provincia' => $datosProvincia,
    'ciudad' => $datosCiudad,
    'genero' => $datosGenero
]);
?>
