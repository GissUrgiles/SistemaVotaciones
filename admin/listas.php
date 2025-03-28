<?php
// Iniciar sesión y verificar si el usuario es administrador
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: ../loginSesiones/login.php");
    exit();
}

include '../includes/conexion.php';

// Obtener todas las listas con sus respectivos presidentes y vicepresidentes
$sql = "SELECT 
            l.id, 
            l.nombre_lista, 
            l.numero_lista, 
            l.foto_lista,
            c1.nombre AS nombre_presidente, 
            c1.apellido AS apellido_presidente, 
            c2.nombre AS nombre_vicepresidente, 
            c2.apellido AS apellido_vicepresidente 
        FROM listas l
        LEFT JOIN candidatos c1 ON l.id_presidente = c1.id
        LEFT JOIN candidatos c2 ON l.id_vicepresidente = c2.id";

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Administrar Listas</title>
        <link rel="stylesheet" href="../css/estilos.css">
        <style>
            /* Estilos generales */
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                background-color: #f9f9f9;
                color: #333;
                padding: 0;
            }

            /* Barra superior */
            .top-bar {
                width: 100%;
                background: linear-gradient(to right, #9933ff, #ff66b2, #6699ff);
                padding: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            }

            .top-bar h1 {
                color: white;
                margin: 0;
            }

            .logout-button {
                background-color: rgba(255, 255, 255, 0.7);
                border: none;
                padding: 12px 20px;
                border-radius: 6px;
                cursor: pointer;
                font-weight: bold;
                font-size: 16px;
            }

            .logout-button:hover {
                background-color: rgba(255, 255, 255, 0.9);
            }

            /* Contenedor de las listas */
            .listas-container {
                margin-top: 120px;
                padding: 30px;
                width: 95%;
                max-width: 1200px;
                margin-left: auto;
                margin-right: auto;
                background-color: white;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                border-radius: 12px;
                font-size: 18px;
            }

            /* Estilo de las tablas */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 30px;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            table th, table td {
                padding: 15px;
                text-align: left;
                font-size: 16px;
            }

            table th {
                background-color: #9933ff;
                color: white;
            }

            table td {
                background-color: #f9f9f9;
            }

            table tr:nth-child(even) td {
                background-color: #f1f1f1;
            }

            /* Estilo de los enlaces de acción convertidos en botones */
            .action-buttons {
                margin-top: 20px;
                margin-bottom: 30px;
            }

            .action-buttons a {
                display: inline-block;
                padding: 12px 25px;
                margin-right: 15px;
                text-decoration: none;
                font-weight: bold;
                font-size: 16px;
                color: white;
                background: linear-gradient(to right, #9933ff, #ff66b2, #6699ff);
                border-radius: 6px;
                transition: background 0.3s ease;
            }

            .action-buttons a:hover {
                background: linear-gradient(to right, #ff66b2, #9933ff, #6699ff);
            }

            /* Estilo del enlace Editar convertido en un botón */
            .edit-button {
                padding: 10px 20px;
                background: linear-gradient(to right, #9933ff, #ff66b2, #6699ff);
                color: white;
                text-decoration: none;
                font-weight: bold;
                border-radius: 5px;
                transition: background 0.3s ease;
                display: inline-block;
            }

            .edit-button:hover {
                background: linear-gradient(to right, #ff66b2, #9933ff, #6699ff);
            }

            /* Botón para cerrar sesión */
            .logout-button-container {
                text-align: right;
                margin-top: 30px;
            }

            .action-links {
                font-size: 18px;
                margin-bottom: 20px;
            }

            .action-links a {
                margin-right: 20px;
            }
        </style>
    </head>
    <body>

        <!-- Barra superior con título y botón de cerrar sesión -->
        <div class="top-bar">
            <h1>Administrar Listas</h1>
            <a href="../loginSesiones/logout.php" class="logout-button">Cerrar sesión</a>
        </div>

        <!-- Contenedor de la tabla de listas -->
        <div class="listas-container">
            <div class="action-buttons">
                <a href="../admin/dashboard.php">Ir al Dashboard</a>
                <a href="crear_lista.php">Crear Nueva Lista</a>
            </div>
            <table>
                <thead>
                    <tr>
                        
                        <th>Nombre de Lista</th>
                        <th>Número de Lista</th>
                        <th>Foto</th>
                        <th>Presidente</th>
                        <th>Vicepresidente</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $resultado->fetch_assoc()) { ?>
                        <tr>
                            
                            <td><?php echo $fila['nombre_lista']; ?></td>
                            <td><?php echo $fila['numero_lista']; ?></td>
                            <td>
                                <?php if (!empty($fila['foto_lista'])) { ?>
                                    <img src="../<?php echo $fila['foto_lista']; ?>" width="70">
                                <?php } else { ?>
                                    Sin foto
                                <?php } ?>
                            </td>
                            <td><?php echo $fila['nombre_presidente'] . ' ' . $fila['apellido_presidente']; ?></td>
                            <td><?php echo $fila['nombre_vicepresidente'] . ' ' . $fila['apellido_vicepresidente']; ?></td>
                            <td>
                                <a href="editar_lista.php?id=<?php echo $fila['id']; ?>" class="edit-button">Editar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
