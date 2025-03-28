<?php
session_start();

// Si el usuario ya inició sesión, lo redirige a la página de votación
if (isset($_SESSION['usuario'])) {
    header("Location: ../votante/votar.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: transparent; /* Sin fondo */
        }

        /* Contenedor del login */
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Logo RTVS */
        .logo {
            font-size: 80px;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80px;
            background: linear-gradient(to right, #8a2be2, #ff69b4, #00bfff);
            -webkit-background-clip: text;
            color: transparent;
        }

        /* Estilos de los inputs */
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Estilos de los labels */
        label {
            color: #ccc;
            font-size: 14px;
            font-weight: bold;
            display: block;
            text-align: left;
            margin-top: 10px;
        }

        /* Botón de inicio de sesión */
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #8a2be2, #ff69b4, #00bfff);
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover {
            background: linear-gradient(to right, #6a1bb8, #d63a89, #009acd);
        }

        /* Texto de "No tienes cuenta?" */
        p {
            color: #ccc;
            font-size: 14px;
            margin-top: 15px;
        }

        /* Enlace de registro (Ahora sin fondo y con degradado en el texto) */
        a {
            font-weight: bold;
            text-decoration: none;
            background: linear-gradient(to right, #8a2be2, #ff69b4, #00bfff);
            -webkit-background-clip: text;
            color: transparent;
            transition: 0.3s;
        }

        a:hover {
            background: linear-gradient(to right, #6a1bb8, #d63a89, #009acd);
            -webkit-background-clip: text;
            color: transparent;
        }

        /* Responsividad */
        @media (max-width: 480px) {
            .login-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="logo">RTVS</div>
        <h2>Iniciar Sesión</h2>

        <form action="validar_login.php" method="POST">
            <label for="cedula">Cédula:</label>
            <input type="text" name="cedula" id="cedula" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Iniciar Sesión</button>
        </form>

        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>

</body>
</html>
