<?php
include '../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si las claves existen en $_POST antes de acceder a ellas
    $cedula = isset($_POST['cedula']) ? trim($_POST['cedula']) : '';
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
    $genero = isset($_POST['genero']) ? trim($_POST['genero']) : '';
    $fecha_nacimiento = isset($_POST['fecha_nacimiento']) ? trim($_POST['fecha_nacimiento']) : '';
    $provincia = isset($_POST['provincia']) ? trim($_POST['provincia']) : '';
    $ciudad = isset($_POST['ciudad']) ? trim($_POST['ciudad']) : '';
    $clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';

    // Verificar que todos los campos estén llenos
    if (empty($cedula) || empty($nombre) || empty($apellido) || empty($genero) || empty($fecha_nacimiento) || empty($provincia) || empty($ciudad) || empty($clave)) {
        die("<p style='color:red;'>❌ Todos los campos son obligatorios. <a href='registro.php'>Intentar de nuevo</a></p>");
    }

    // Validar cédula ecuatoriana
    function validarCedulaEcuatoriana($cedula) {
        if (strlen($cedula) != 10 || !ctype_digit($cedula)) {
            return false;
        }

        $codigoProvincia = intval(substr($cedula, 0, 2));
        $provinciasValidas = range(1, 24);
        if (!in_array($codigoProvincia, $provinciasValidas)) {
            return false;
        }

        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $suma = 0;

        for ($i = 0; $i < 9; $i++) {
            $valor = intval($cedula[$i]) * $coeficientes[$i];
            $suma += ($valor >= 10) ? $valor - 9 : $valor;
        }

        $digitoVerificador = ($suma % 10 === 0) ? 0 : (10 - ($suma % 10));

        return $digitoVerificador == intval($cedula[9]);
    }

    if (!validarCedulaEcuatoriana($cedula)) {
        die("<p style='color:red;'>La cédula ingresada no es válida. <a href='registro.php'>Intentar de nuevo</a></p>");
    }

    // Validar que nombre y apellido solo contengan letras
    if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/", $nombre) || !preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/", $apellido)) {
        die("<p style='color:red;'>El nombre y el apellido solo pueden contener letras. <a href='registro.php'>Intentar de nuevo</a></p>");
    }

    // Validar provincia y ciudad (solo letras)
    if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/", $provincia) || !preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/", $ciudad)) {
        die("<p style='color:red;'>La provincia y ciudad solo pueden contener letras. <a href='registro.php'>Intentar de nuevo</a></p>");
    }

    // Validar fecha de nacimiento (mayor de 16 años)
    $fecha_actual = new DateTime();
    $fecha_nacimiento_dt = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
    if (!$fecha_nacimiento_dt || $fecha_nacimiento_dt > $fecha_actual->modify('-16 years')) {
        die("<p style='color:red;'>Debes tener al menos 16 años para registrarte. <a href='registro.php'>Intentar de nuevo</a></p>");
    }

    // Validar contraseña (mínimo 6 caracteres)
    if (strlen($clave) < 6) {
        die("<p style='color:red;'>La contraseña debe tener al menos 6 caracteres. <a href='registro.php'>Intentar de nuevo</a></p>");
    }

    // Encriptar la contraseña con Bcrypt
    $clave_hash = password_hash($clave, PASSWORD_BCRYPT);

    // Verificar si la cédula ya está registrada
    $stmt = $conn->prepare("SELECT * FROM votantes WHERE cedula = ?");
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("<p style='color:red;'>Esta cédula ya está registrada. <a href='login.php'>Iniciar sesión</a></p>");
    }

    // Insertar en la base de datos
    $stmt = $conn->prepare("INSERT INTO votantes (cedula, nombre, apellido, genero, fecha_nacimiento, provincia, ciudad, clave) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $cedula, $nombre, $apellido, $genero, $fecha_nacimiento, $provincia, $ciudad, $clave_hash);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Registro exitoso. <a href='login.php'>Iniciar sesión</a></p>";
    } else {
        echo "<p style='color:red;'>Error al registrar el usuario: " . $conn->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
