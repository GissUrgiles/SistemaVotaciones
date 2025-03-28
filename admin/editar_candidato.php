<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: ../loginSesiones/login.php");
    exit();
}

include '../includes/conexion.php';

// Validar que el ID está presente en la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID de candidato no válido.'); window.location='candidatos.php';</script>";
    exit();
}

$id = $_GET['id'];

// Obtener los datos del candidato
$sql = "SELECT * FROM candidatos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$candidato = $resultado->fetch_assoc();

// Si el candidato no existe, redirigir
if (!$candidato) {
    echo "<script>alert('Candidato no encontrado.'); window.location='candidatos.php';</script>";
    exit();
}

// Si el formulario se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $cargo = $_POST["cargo"];
    $foto_candidato = $candidato['foto']; // Mantener la foto actual si no se sube una nueva

    // Validar que los campos no estén vacíos
    if (empty($nombre) || empty($apellido) || empty($cargo)) {
        echo "<script>alert('Todos los campos son obligatorios.');</script>";
    } else {
        // Subir la nueva foto si se proporciona
        if (!empty($_FILES["foto_candidato"]["name"])) {
            $directorio = "../uploads/";
            $foto_nombre = basename($_FILES["foto_candidato"]["name"]);
            $ruta_archivo = $directorio . $foto_nombre;
            $ruta_foto_bd = "uploads/" . $foto_nombre;

            if (move_uploaded_file($_FILES["foto_candidato"]["tmp_name"], $ruta_archivo)) {
                $foto_candidato = $ruta_foto_bd; // Actualizar la foto en la base de datos
            } else {
                echo "<script>alert('Error al subir la imagen.');</script>";
            }
        }

        // Actualizar los datos
        $sql_update = "UPDATE candidatos SET nombre=?, apellido=?, cargo=?, foto=? WHERE id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssi", $nombre, $apellido, $cargo, $foto_candidato, $id);

        if ($stmt_update->execute()) {
            echo "<script>alert('Candidato actualizado exitosamente.'); window.location='candidatos.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar candidato. Intente nuevamente.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Candidato</title>
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

        /* Estilo para el formulario */
        form {
            width: 50%;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        form label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            color: #6a1b9a;
        }

        form input, form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        /* Contenedor de botones */
        .botones {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        form button {
            background: linear-gradient(to right, #9b59b6, #6dd5fa, #ff758c);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
            width: 48%;
            cursor: pointer;
        }

        form button:hover {
            background: linear-gradient(to right, #8e44ad, #4a90e2, #ff5c72);
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

        .boton-cancelar {
            background: linear-gradient(to right, #ff758c, #6dd5fa, #9b59b6);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 18px;
            transition: background 0.3s, transform 0.3s;
            text-align: center;
        }

        .boton-cancelar:hover {
            background: linear-gradient(to right, #ff5c72, #4a90e2, #8e44ad);
            transform: scale(1.05);
            text-align: center;
        }

        /* Estilos para la imagen */
        .imagen-candidato {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
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
    <h1>Editar Candidato</h1>

    <!-- Formulario para editar el candidato -->
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($candidato['nombre']); ?>" required>

        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($candidato['apellido']); ?>" required>

        <label>Cargo:</label>
        <select name="cargo">
            <option value="Presidente" <?php echo ($candidato['cargo'] == 'Presidente') ? 'selected' : ''; ?>>Presidente</option>
            <option value="Vicepresidente" <?php echo ($candidato['cargo'] == 'Vicepresidente') ? 'selected' : ''; ?>>Vicepresidente</option>
        </select>

        <label>Foto del Candidato:</label>
        <img src="<?php echo $candidato['foto']; ?>" alt="Foto Candidato" class="imagen-candidato">
        <input type="file" name="foto_candidato" accept="image/*">

        <!-- Botones de acción -->
        <div class="botones">
            <button type="submit">Actualizar</button>
            <a href="listas.php" class="boton-cancelar">Cancelar</a>
        </div>
    </form>

</body>
</html>
