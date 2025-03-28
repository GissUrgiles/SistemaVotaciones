<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: ../loginSesiones/login.php");
    exit();
}

include '../includes/conexion.php';

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: listas.php");
    exit();
}

$id = $_GET["id"];

// Obtener datos de la lista
$sql = "SELECT * FROM listas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$lista = $resultado->fetch_assoc();

if (!$lista) {
    header("Location: listas.php");
    exit();
}

// Obtener candidatos para los select
$sql_presidentes = "SELECT id, nombre, apellido FROM candidatos WHERE cargo = 'Presidente'";
$sql_vicepresidentes = "SELECT id, nombre, apellido FROM candidatos WHERE cargo = 'Vicepresidente'";

$resultado_presidentes = $conn->query($sql_presidentes);
$resultado_vicepresidentes = $conn->query($sql_vicepresidentes);

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_lista = trim($_POST["nombre_lista"]);
    $numero_lista = trim($_POST["numero_lista"]);
    $presidente_id = $_POST["presidente"];
    $vicepresidente_id = $_POST["vicepresidente"];
    $foto_lista = $lista["foto_lista"]; // Mantener la foto actual si no se actualiza

    // Verificar que el presidente y vicepresidente sean diferentes
    if ($presidente_id == $vicepresidente_id) {
        echo "<script>alert('El Presidente y Vicepresidente deben ser diferentes.'); window.history.back();</script>";
        exit();
    }

    // Procesar la nueva foto si se sube una
    if (!empty($_FILES["foto_lista"]["name"])) {
        $directorio = "../uploads/";
        $foto_nombre = basename($_FILES["foto_lista"]["name"]);
        $ruta_archivo = $directorio . $foto_nombre;
        $ruta_foto_bd = "uploads/" . $foto_nombre;

        if (move_uploaded_file($_FILES["foto_lista"]["tmp_name"], $ruta_archivo)) {
            $foto_lista = $ruta_foto_bd; // Actualizar la ruta en la base de datos
        } else {
            echo "<script>alert('Error al subir la imagen.');</script>";
        }
    }

    // Actualizar lista en la base de datos
    $sql_update = "UPDATE listas SET nombre_lista = ?, numero_lista = ?, foto_lista = ?, id_presidente = ?, id_vicepresidente = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssiii", $nombre_lista, $numero_lista, $foto_lista, $presidente_id, $vicepresidente_id, $id);

    if ($stmt_update->execute()) {
        echo "<script>alert('Lista actualizada exitosamente.'); window.location='listas.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar la lista.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Lista</title>
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

        form {
            background-color: white;
            padding: 30px;
            margin: 20px auto;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }

        input[type="text"], input[type="number"], select, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            font-size: 16px;
        }

        /* Estilo para los botones */
        .botones-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .boton {
            background: linear-gradient(to right, #9b59b6, #6dd5fa, #ff758c);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 18px;
            transition: background 0.3s, transform 0.3s;
            width: 150px;
            text-align: center;
        }

        .boton:hover {
            background: linear-gradient(to right, #8e44ad, #4a90e2, #ff5c72);
            transform: scale(1.05);
        }

        select {
            background-color: #f9f9f9;
        }

        input[type="file"] {
            padding: 5px;
            background-color: #f9f9f9;
        }

        a {
            text-decoration: none;
            color: #9b59b6;
            font-weight: bold;
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            background-color: #fff;
            transition: background-color 0.3s;
        }

        a:hover {
            background-color: #9b59b6;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Barra superior -->
    <div class="barra-superior">
        <span>Bienvenido al Sistema RTVS</span>
        <a href="../loginSesiones/logout.php" class="cerrar-sesion">Cerrar sesión</a>
    </div>

    <h1>Editar Lista</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Nombre de la Lista:</label>
        <input type="text" name="nombre_lista" value="<?php echo $lista['nombre_lista']; ?>" required><br>

        <label>Número de la Lista:</label>
        <input type="number" name="numero_lista" value="<?php echo $lista['numero_lista']; ?>" required><br>

        <label>Foto Representativa:</label>
        <input type="file" name="foto_lista" accept="image/*"><br>
        <?php if (!empty($lista['foto_lista'])): ?>
            <p>Imagen actual:</p>
            <img src="../<?php echo $lista['foto_lista']; ?>" width="150"><br>
        <?php endif; ?>

        <label>Seleccionar Presidente:</label>
        <select name="presidente" required>
            <option value="">Seleccione un presidente</option>
            <?php while ($candidato = $resultado_presidentes->fetch_assoc()) { ?>
                <option value="<?php echo $candidato['id']; ?>" <?php echo ($lista['id_presidente'] == $candidato['id']) ? 'selected' : ''; ?>>
                    <?php echo $candidato['nombre'] . " " . $candidato['apellido']; ?>
                </option>
            <?php } ?>
        </select><br>

        <label>Seleccionar Vicepresidente:</label>
        <select name="vicepresidente" required>
            <option value="">Seleccione un vicepresidente</option>
            <?php while ($candidato = $resultado_vicepresidentes->fetch_assoc()) { ?>
                <option value="<?php echo $candidato['id']; ?>" <?php echo ($lista['id_vicepresidente'] == $candidato['id']) ? 'selected' : ''; ?>>
                    <?php echo $candidato['nombre'] . " " . $candidato['apellido']; ?>
                </option>
            <?php } ?>
        </select><br>

        <!-- Contenedor para los botones centrados -->
        <div class="botones-container">
            <button type="submit" class="boton">Actualizar</button>
            <a href="listas.php" class="boton">Cancelar</a>
        </div>
    </form>

</body>
</html>
