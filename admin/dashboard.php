<?php
// Iniciar sesión y verificar si el usuario es administrador
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: ../loginSesiones/login.php");
    exit();
}

include '../includes/conexion.php';

// Contar votantes y listas registradas
$sql_votantes = "SELECT COUNT(*) as total FROM votantes";
$result_votantes = $conn->query($sql_votantes);
$row_votantes = $result_votantes->fetch_assoc();
$total_votantes = $row_votantes['total'];

$sql_listas = "SELECT COUNT(*) as total FROM listas";
$result_listas = $conn->query($sql_listas);
$row_listas = $result_listas->fetch_assoc();
$total_listas = $row_listas['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <style>
        /* Barra superior con degradado */
        .barra-superior {
            width: 100%;
            background: linear-gradient(to right, #9b59b6, #6dd5fa, #ff758c);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Estilo del texto en la barra superior */
        .barra-superior span {
            font-size: 40px;  /* Tamaño de fuente grande */
        }

        /* Botón de cerrar sesión */
        .cerrar-sesion {
            background: linear-gradient(to right, #9b59b6, #6dd5fa, #ff758c);
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .cerrar-sesion:hover {
            background: linear-gradient(to right, #8e44ad, #4a90e2, #ff5c72);
        }

        /* Contenedor principal */
        .contenido {
            margin-top: 80px;
            width: 90%;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Enlaces */
        a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            margin: 0 10px;
            padding: 12px 25px;
            border-radius: 6px;
            display: inline-block;
            transition: background 0.3s ease;
        }

        a:hover {
            color: #fff;
        }

        /* Estilo para los botones */
        .boton {
            background: linear-gradient(to right, #9b59b6, #6dd5fa, #ff758c);
        }

        .boton:hover {
            background: linear-gradient(to right, #8e44ad, #4a90e2, #ff5c72);
        }

        /* Estilo del mensaje de bienvenida */
        h1 {
            color: #333;
            font-size: 40px;
        }

        p {
            font-size: 24px;
            color: #555;
        }

        /* Estilo para los números */
        .total {
            font-size: 24px;
            color: #9b59b6;
        }
    </style>
</head>
<body>

    <!-- Barra superior -->
    <div class="barra-superior">
        <span>Bienvenido al Sistema RTVS</span>
        <a href="../loginSesiones/logout.php" class="cerrar-sesion">Cerrar sesión</a>
    </div>

    <!-- Contenido del panel -->
    <div class="contenido">
        <h1>Bienvenido al Sistema </h1>
        <p>Total de votantes registrados: <span class="total"><?php echo $total_votantes; ?></span></p>
        <p>Total de listas registradas: <span class="total"><?php echo $total_listas; ?></span></p>
        
        <div>
            <a href="/SistemaVotaciones/admin/listas.php" class="boton">Gestionar Listas</a>
            <a href="/SistemaVotaciones/admin/candidatos.php" class="boton">Gestionar Candidatos</a>
            <a href="/SistemaVotaciones/admin/reportes.php" class="boton">Ver Reportes</a>
        </div>
    </div>

</body>
</html>
