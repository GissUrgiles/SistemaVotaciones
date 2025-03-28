<?php
// Iniciar sesión y verificar si el usuario es administrador
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: ../loginSesiones/login.php");
    exit();
}

include '../includes/conexion.php';

// Obtener todos los candidatos con el nombre de la lista a la que pertenecen
$sql = "SELECT c.id, c.nombre, c.apellido, c.cargo, 
               COALESCE(l.nombre_lista, 'Sin Lista') AS nombre_lista, 
               c.foto
        FROM candidatos c
        LEFT JOIN listas l ON c.id_lista = l.id";

$resultado = $conn->query($sql);

// Verificar si la consulta tuvo éxito
if (!$resultado) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Administrar Candidatos</title>
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

            /* Estilo para el mensaje de bienvenida */
            .barra-superior span {
                font-size: 24px;
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

            /* Estilo del cuerpo de la página */
            body {
                font-family: Arial, sans-serif;
                background-color: #f7f7f7;
                margin: 0;
                padding: 0;
            }

            h1 {
                text-align: center;
                color: #6a1b9a;
                font-size: 40px;
                margin-top: 40px;
            }

            /* Estilo para los enlaces */
            a {
                text-decoration: none;
                color: #9b59b6;
                font-weight: bold;
                display: inline-block;
                margin-top: 20px;
                padding: 10px;
                border-radius: 5px;
                background-color: #fff;
                transition: background-color 0.3s;
            }

            a:hover {
                background-color: #9b59b6;
                color: white;
            }

            /* Estilo de los botones alineados */
            .botones-container {
                display: flex;
                justify-content: center;
                gap: 20px;
                margin-top: 20px;
            }

            .boton {
                background: linear-gradient(to right, #9b59b6, #6dd5fa, #ff758c);
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                font-weight: bold;
                transition: background 0.3s;
                text-align: center;
            }

            .boton:hover {
                background: linear-gradient(to right, #8e44ad, #4a90e2, #ff5c72);
            }

            /* Estilo de la tabla */
            table {
                width: 80%;
                margin: 20px auto;
                border-collapse: collapse;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            table th, table td {
                padding: 12px 20px;
                border: 1px solid #ddd;
                text-align: center;
                font-size: 16px;
            }

            table th {
                background-color: #6a1b9a;
                color: white;
            }

            table tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            table tr:hover {
                background-color: #e1bee7;
            }

            /* Botón de acción en la tabla */
            .acciones a {
                background: linear-gradient(to right, #9b59b6, #6dd5fa, #ff758c);
                color: white;
                padding: 8px 15px;
                border-radius: 5px;
                font-weight: bold;
                text-decoration: none;
                margin: 5px;
                transition: background 0.3s;
            }

            .acciones a:hover {
                background: linear-gradient(to right, #8e44ad, #4a90e2, #ff5c72);
            }
        </style>
    </head>
    <body>

        <!-- Barra superior -->
        <div class="barra-superior">
            <span>Bienvenido al Sistema RTVS</span>
            <a href="../loginSesiones/logout.php" class="cerrar-sesion">Cerrar sesión</a>
        </div>

        <!-- Título de la página -->
        <h1>Administración de Candidatos</h1>

        <!-- Botones de navegación al Dashboard y Agregar Nuevo Candidato -->
        <div class="botones-container">
            <a href="../admin/dashboard.php" class="boton">Ir al Dashboard</a>
            <a href="crear_candidato.php" class="boton">Agregar Nuevo Candidato</a>
        </div>

        <!-- Tabla de Candidatos -->
        <table>
            <tr>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Cargo</th>
                <th>Lista</th>
                <th>Acciones</th>
            </tr>

            <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <!-- Mostrar foto del candidato -->
                        <img src="http://localhost/SistemaVotaciones/uploads/<?php echo htmlspecialchars($fila['foto']); ?>" 
                             alt="Foto <?php echo htmlspecialchars($fila['cargo']); ?> <?php echo htmlspecialchars($fila['nombre']) . ' ' . htmlspecialchars($fila['apellido']); ?>" 
                             width="100" height="100">
                    </td>

                    <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($fila['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($fila['cargo']); ?></td>
                    <td>
                        <?php
                        // Si el candidato tiene una lista asignada, mostrarla, de lo contrario mostrar 'Sin Lista'
                        echo $fila['nombre_lista'] ? htmlspecialchars($fila['nombre_lista']) : 'Sin Lista';
                        ?>
                    </td>
                    <td class="acciones">
                        <a href="editar_candidato.php?id=<?php echo urlencode($fila['id']); ?>">Editar</a>
                        <a href="eliminar_candidato.php?id=<?php echo urlencode($fila['id']); ?>" 
                           onclick="return confirm('¿Estás seguro de eliminar este candidato?')">Eliminar</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </body>
</html>
