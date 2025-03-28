<?php 
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: ../loginSesiones/login.php");
    exit();
}

include '../includes/conexion.php';

// Obtener solo candidatos disponibles para cada cargo (sin necesidad de verificar id_lista)
$sql_presidentes = "SELECT id, nombre, apellido FROM candidatos WHERE cargo = 'Presidente'";
$sql_vicepresidentes = "SELECT id, nombre, apellido FROM candidatos WHERE cargo = 'Vicepresidente'";

$resultado_presidentes = $conn->query($sql_presidentes);
$resultado_vicepresidentes = $conn->query($sql_vicepresidentes);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_lista = $_POST["nombre_lista"];
    $numero_lista = $_POST["numero_lista"];
    $presidente_id = $_POST["presidente"];
    $vicepresidente_id = $_POST["vicepresidente"];
    $foto_lista = "";

    if ($presidente_id == $vicepresidente_id) {
        echo "<script>alert('El Presidente y Vicepresidente deben ser diferentes.'); window.history.back();</script>";
        exit();
    }

    // Subir la foto de la lista si se proporciona
    if (!empty($_FILES["foto_lista"]["name"])) {
        $directorio = "../uploads/";
        $foto_nombre = basename($_FILES["foto_lista"]["name"]);
        $ruta_archivo = $directorio . $foto_nombre;
        $ruta_foto_bd = "uploads/" . $foto_nombre;

        if (move_uploaded_file($_FILES["foto_lista"]["tmp_name"], $ruta_archivo)) {
            $foto_lista = $ruta_foto_bd;
        } else {
            echo "<script>alert('Error al subir la imagen.');</script>";
        }
    }

    // Insertar la nueva lista
    $sql_lista = "INSERT INTO listas (nombre_lista, numero_lista, foto_lista, id_presidente, id_vicepresidente) VALUES (?, ?, ?, ?, ?)";
    $stmt_lista = $conn->prepare($sql_lista);
    $stmt_lista->bind_param("sssii", $nombre_lista, $numero_lista, $foto_lista, $presidente_id, $vicepresidente_id);

    if ($stmt_lista->execute()) {
        // Obtener el ID de la última lista creada
        $ultimo_id_lista = $conn->insert_id; 

        // Actualizar los candidatos para asignarles la lista
        $sql_update_presidente = "UPDATE candidatos SET id_lista = ? WHERE id = ?";
        $stmt_update_presidente = $conn->prepare($sql_update_presidente);
        $stmt_update_presidente->bind_param("ii", $ultimo_id_lista, $presidente_id);
        $stmt_update_presidente->execute();

        $sql_update_vicepresidente = "UPDATE candidatos SET id_lista = ? WHERE id = ?";
        $stmt_update_vicepresidente = $conn->prepare($sql_update_vicepresidente);
        $stmt_update_vicepresidente->bind_param("ii", $ultimo_id_lista, $vicepresidente_id);
        $stmt_update_vicepresidente->execute();

        echo "<script>alert('Lista creada exitosamente.'); window.location='listas.php';</script>";
    } else {
        echo "<script>alert('Error al crear la lista. Intente nuevamente.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Lista</title>
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

        button {
            background: linear-gradient(to right, #9b59b6, #6dd5fa, #ff758c);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 18px;
            transition: background 0.3s, transform 0.3s;
        }

        button:hover {
            background: linear-gradient(to right, #8e44ad, #4a90e2, #ff5c72);
            transform: scale(1.05);
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
            margin-left: 20px;
        }

        .boton-cancelar:hover {
            background: linear-gradient(to right, #ff5c72, #4a90e2, #8e44ad);
            transform: scale(1.05);
        }

        select {
            background-color: #f9f9f9;
        }

        input[type="file"] {
            padding: 5px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

    <!-- Barra superior -->
    <div class="barra-superior">
        <span>Bienvenido al Sistema RTVS</span>
        <a href="../loginSesiones/logout.php" class="cerrar-sesion">Cerrar sesión</a>
    </div>

    <h1>Crear Nueva Lista</h1>
    <form action="crear_lista.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario()">
        <label>Nombre de la Lista:</label>
        <input type="text" name="nombre_lista" required id="nombre_lista" oninput="validarSoloLetras(this)">

        <label>Número de Lista:</label>
        <input type="number" name="numero_lista" required id="numero_lista" oninput="validarSoloNumeros(this)">

        <label>Foto Representativa:</label>
        <input type="file" name="foto_lista" accept="image/*">

        <label>Seleccionar Presidente:</label>
        <select name="presidente" required>
            <option value="">Seleccione un presidente</option>
            <?php while ($candidato = $resultado_presidentes->fetch_assoc()) { ?>
                <option value="<?php echo $candidato['id']; ?>"><?php echo $candidato['nombre'] . " " . $candidato['apellido']; ?></option>
            <?php } ?>
        </select>

        <label>Seleccionar Vicepresidente:</label>
        <select name="vicepresidente" required>
            <option value="">Seleccione un vicepresidente</option>
            <?php while ($candidato = $resultado_vicepresidentes->fetch_assoc()) { ?>
                <option value="<?php echo $candidato['id']; ?>"><?php echo $candidato['nombre'] . " " . $candidato['apellido']; ?></option>
            <?php } ?>
        </select>

        <button type="submit">Crear Lista</button>
        <a href="listas.php" class="boton-cancelar">Cancelar</a>
    </form>

    <script>
        function validarFormulario() {
            // Validar que todos los campos requeridos estén llenos
            let nombreLista = document.getElementById("nombre_lista").value;
            let numeroLista = document.getElementById("numero_lista").value;
            if (!nombreLista || !numeroLista) {
                alert("Todos los campos son requeridos.");
                return false;
            }
            return true;
        }

        function validarSoloLetras(input) {
            // Solo permitir letras (sin números ni caracteres especiales)
            input.value = input.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, "");
        }

        function validarSoloNumeros(input) {
            // Solo permitir números
            input.value = input.value.replace(/[^0-9]/g, "");
        }
    </script>

</body>
</html>
