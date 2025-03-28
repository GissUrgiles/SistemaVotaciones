<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: ../loginSesiones/login.php");
    exit();
}

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

// Obtener datos para los gráficos
$datosProvincia = obtenerDatosReporte($conn, 'provincia');
$datosCiudad = obtenerDatosReporte($conn, 'ciudad');
$datosGenero = obtenerDatosReporte($conn, 'genero');

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes de Votaciones</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f9;
            color: #333;
            padding: 20px;
        }

        /* Barra superior */
        .top-bar {
            width: 100%;
            background: linear-gradient(to right, #9933ff, #ff66b2, #6699ff);
            padding: 10px 20px; /* Reducido el tamaño de la barra */
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            box-shadow: 0 2px 2px rgba(0, 0, 0, 0.2);
        }

        .top-bar h1 {
            color: white;
            margin: 0;
            font-size: 24px; /* Reducido el tamaño del título */
        }

        .logout-button {
            background-color: rgba(255, 255, 255, 0.7);
            border: none;
            padding: 8px 15px; /* Reducido el tamaño del botón */
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }

        .logout-button:hover {
            background-color: rgba(255, 255, 255, 0.9);
        }

        h1 {
            text-align: center;
            color: #9933ff;
            margin-top: 80px; /* Ajuste para que no se superponga con la barra superior */
        }

        h2 {
            text-align: center;
            color: #6699ff;
            margin-bottom: 30px;
        }

        .grafico-container {
            margin-bottom: 50px;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        canvas {
            width: 100% !important;
            max-width: 600px;
            margin: 0 auto;
            display: block;
        }

        a {
            display: inline-block;
            text-decoration: none;
            background-color: #9933ff;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px; /* Bajar más el botón Volver al Panel */
            text-align: center;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #ff66b2;
        }
    </style>
</head>
<body>

    <!-- Barra superior con título y botón de cerrar sesión -->
    <div class="top-bar">
        <h1>Reportes de Votaciones</h1>
        <a href="../loginSesiones/logout.php" class="logout-button">Cerrar sesión</a>
    </div>

    <!-- Botón Volver al Panel -->
    <a href="dashboard.php" style="display: inline-block; text-decoration: none; background-color: #9933ff; color: white; padding: 12px 20px; border-radius: 6px; font-size: 16px; font-weight: bold; margin-top: 60px; text-align: center; transition: background-color 0.3s ease;">Volver</a>


    <!-- Gráfico por Provincia -->
    <div class="grafico-container">
        <h2>Votos por Provincia</h2>
        <canvas id="graficoProvincia"></canvas>
    </div>

    <!-- Gráfico por Ciudad -->
    <div class="grafico-container">
        <h2>Votos por Ciudad</h2>
        <canvas id="graficoCiudad"></canvas>
    </div>

    <!-- Gráfico por Género -->
    <div class="grafico-container">
        <h2>Votos por Género</h2>
        <canvas id="graficoGenero"></canvas>
    </div>

    <script>
        // Datos de Provincia
        const datosProvincia = <?php echo json_encode($datosProvincia); ?>;
        const etiquetasProvincia = datosProvincia.map(d => d.provincia);
        const valoresProvincia = datosProvincia.map(d => d.total_votos);

        new Chart(document.getElementById('graficoProvincia'), {
            type: 'bar',
            data: {
                labels: etiquetasProvincia,
                datasets: [{
                    label: 'Votos por Provincia',
                    data: valoresProvincia,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

        // Datos de Ciudad
        const datosCiudad = <?php echo json_encode($datosCiudad); ?>;
        const etiquetasCiudad = datosCiudad.map(d => d.ciudad);
        const valoresCiudad = datosCiudad.map(d => d.total_votos);

        new Chart(document.getElementById('graficoCiudad'), {
            type: 'bar',
            data: {
                labels: etiquetasCiudad,
                datasets: [{
                    label: 'Votos por Ciudad',
                    data: valoresCiudad,
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

        // Datos de Género
        const datosGenero = <?php echo json_encode($datosGenero); ?>;
        const etiquetasGenero = datosGenero.map(d => d.genero);
        const valoresGenero = datosGenero.map(d => d.total_votos);

        new Chart(document.getElementById('graficoGenero'), {
            type: 'pie',
            data: {
                labels: etiquetasGenero,
                datasets: [{
                    label: 'Votos por Género',
                    data: valoresGenero,
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffcd56'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
