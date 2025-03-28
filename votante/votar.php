<?php
session_start();
require_once "../includes/conexion.php";

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../loginSesiones/login.php");
    exit();
}

// Obtener la cédula del votante desde la sesión
$cedula = $_SESSION['usuario'];

// Obtener el ID del votante desde la base de datos
$sql = "SELECT id FROM votantes WHERE cedula = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cedula);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    session_destroy();
    header("Location: ../loginSesiones/login.php");
    exit();
}

$fila = $resultado->fetch_assoc();
$id_votante = $fila['id'];

// Verificar si el votante ya ha votado
$sql = "SELECT id FROM votos WHERE id_votante = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_votante);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    echo "<p style='text-align: center; font-size: 18px;'>✅ Usted ya ha votado. <a href='../loginSesiones/logout.php'>Cerrar sesión</a></p>";
    exit();
}

// Obtener las listas con los datos de presidente y vicepresidente
$sql = "SELECT 
            l.id, 
            l.nombre_lista, 
            l.foto_lista, 
            p.nombre AS nombre_presidente, 
            p.apellido AS apellido_presidente, 
            p.foto AS foto_presidente, 
            v.nombre AS nombre_vicepresidente, 
            v.apellido AS apellido_vicepresidente, 
            v.foto AS foto_vicepresidente
        FROM listas l
        JOIN candidatos p ON l.id_presidente = p.id
        JOIN candidatos v ON l.id_vicepresidente = v.id";

$listas = $conn->query($sql);

// Verificar si hay listas disponibles
if (!$listas || $listas->num_rows === 0) {
    echo "<p style='text-align: center; font-size: 18px;'>❌ No hay listas disponibles para votar.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .barra-superior {
            width: 100%;
            height: 50px;
            background: linear-gradient(to right, #9b59b6, #6dd5fa, #ff758c);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }

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

        .votacion-container {
            width: 95%;
            max-width: 900px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .listas-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        .lista-opcion {
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            width: 250px;
        }

        .foto-lista, .foto-candidato {
            width: 100%;
            max-width: 150px;
            height: auto;
            border-radius: 5px;
        }

        .btn-votar {
            background-color: #ff69b4;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
            display: block;
            width: 100%;
        }

        .btn-votar:hover {
            background-color: #d63a89;
        }
    </style>
</head>
<body>

    <div class="barra-superior">
        <span>Bienvenido al Sistema RTVS</span>
        <a href="../loginSesiones/logout.php" class="cerrar-sesion">Cerrar sesión</a>
    </div>

    <div class="votacion-container">
        <h2>Seleccione su opción de votación</h2>

        <form action="votar.php" method="POST">
            <div class="listas-container">
                <?php while ($row = $listas->fetch_assoc()): ?>
                    <label class="lista-opcion">
                        <input type="radio" name="id_lista" value="<?= $row['id'] ?>" required>
                        <div>
                            <img src="../uploads/<?= htmlspecialchars($row['foto_lista']) ?>" alt="Lista" class="foto-lista">
                            <h3><?= htmlspecialchars($row['nombre_lista']) ?></h3>
                        </div>
                        <div>
                            <h4>Presidente:</h4>
                            <img src="../uploads/<?= htmlspecialchars($row['foto_presidente']) ?>" alt="Presidente" class="foto-candidato">
                            <p><?= htmlspecialchars($row['nombre_presidente']) . " " . htmlspecialchars($row['apellido_presidente']) ?></p>
                        </div>
                        <div>
                            <h4>Vicepresidente:</h4>
                            <img src="../uploads/<?= htmlspecialchars($row['foto_vicepresidente']) ?>" alt="Vicepresidente" class="foto-candidato">
                            <p><?= htmlspecialchars($row['nombre_vicepresidente']) . " " . htmlspecialchars($row['apellido_vicepresidente']) ?></p>
                        </div>
                    </label>
                <?php endwhile; ?>
            </div>
            <button type="submit" class="btn-votar">Votar</button>
        </form>
    </div>

</body>
</html>
