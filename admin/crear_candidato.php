<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: ../loginSesiones/login.php");
    exit();
}

include '../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $cargo = $_POST["cargo"];
    $foto = "";

    // Validaciones para nombre y apellido (solo letras y espacios)
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
        echo "<script>alert('El nombre solo puede contener letras y espacios.'); window.history.back();</script>";
        exit();
    }

    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $apellido)) {
        echo "<script>alert('El apellido solo puede contener letras y espacios.'); window.history.back();</script>";
        exit();
    }

    // Manejo de subida de imagen
    if (!empty($_FILES["foto"]["name"])) {
        $directorio = "../uploads/";
        $foto = basename($_FILES["foto"]["name"]);
        $ruta_archivo = $directorio . $foto;

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $ruta_archivo)) {
            echo "Imagen subida correctamente.";
        } else {
            echo "Error al subir la imagen.";
        }
    }

    // Insertar el candidato SIN id_lista (se asignará después en crear_lista.php)
    $sql = "INSERT INTO candidatos (nombre, apellido, cargo, foto) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $apellido, $cargo, $foto);

    if ($stmt->execute()) {
        echo "<script>alert('Candidato agregado exitosamente.'); window.location='candidatos.php';</script>";
    } else {
        echo "Error al agregar candidato.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Candidato</title>
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

        /* Estilo del formulario */
        form {
            width: 60%;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 16px;
            color: #6a1b9a;
            margin-top: 10px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            background: linear-gradient(to right, #9b59b6, #6dd5fa, #ff758c);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 48%;
            transition: background 0.3s;
        }

        button:hover {
            background: linear-gradient(to right, #8e44ad, #4a90e2, #ff5c72);
        }

        /* Contenedor para los botones (Guardar y Volver) */
        .botones-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
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
    <h1>Agregar Nuevo Candidato</h1>

    <!-- Formulario para agregar nuevo candidato -->
    <form action="crear_candidato.php" method="POST" enctype="multipart/form-data">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Apellido:</label>
        <input type="text" name="apellido" required>

        <label>Cargo:</label>
        <select name="cargo" required>
            <option value="Presidente">Presidente</option>
            <option value="Vicepresidente">Vicepresidente</option>
        </select>

        <label>Foto del Candidato:</label>
        <input type="file" name="foto" accept="image/*">

        <!-- Contenedor con los botones (Guardar y Volver) -->
        <div class="botones-container">
            <!-- Botón para guardar -->
            <button type="submit">Guardar Candidato</button>

            <!-- Botón para volver -->
            <button type="button" onclick="window.location.href='candidatos.php'">Cancelar</button>
        </div>
    </form>

</body>
</html>
