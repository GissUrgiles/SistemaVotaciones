<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Registro de Votantes</title>

        <style>
            /* Estilos generales */
            body {
                font-family: Arial, sans-serif;
                background-color: #f9f9f9;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }

            /* Contenedor del formulario */
            .form-container {
                background-color: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
                width: 400px; /* Aumentamos el ancho */
                max-width: 90%;
                text-align: center;
            }

            /* Título con degradado */
            h2 {
                font-size: 60px;
                background: linear-gradient(to right, #ff66b2, #6699ff, #9933ff);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                font-weight: bold;
                margin-bottom: 20px;
            }

            /* Campos del formulario */
            label {
                display: block;
                text-align: left;
                margin-top: 10px;
                font-weight: bold;
                color: #ccc; /* Color solicitado */
            }

            /* Hacer que los inputs y select tengan el mismo tamaño */
            input, select {
                width: 100%;
                padding: 10px; /* Ajustar el padding para que sean iguales */
                margin-top: 5px;
                border: 1px solid #ccc;
                border-radius: 5px; /* Redondear esquinas */
                font-size: 16px;
                appearance: none; /* Oculta el estilo predeterminado del select */
                -webkit-appearance: none;
                -moz-appearance: none;
            }

            /* Agregar una flecha personalizada al select */
            select {
                background-color: white;
                cursor: pointer;
                background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23000000'><path d='M7 10l5 5 5-5H7z'/></svg>");
                background-repeat: no-repeat;
                background-position: right 10px center;
                background-size: 20px;
            }


            /* Botón con degradado */
            button {
                width: 100%;
                background: linear-gradient(to right, #ff66b2, #6699ff, #9933ff);
                color: white;
                padding: 12px;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                font-weight: bold;
                margin-top: 20px;
                cursor: pointer;
                transition: 0.3s;
            }

            button:hover {
                opacity: 0.9;
            }

            /* Texto "¿Ya tienes una cuenta?" con color #ccc */
            p {
                margin-top: 15px;
                color: #ccc;
                font-size: 14px;
            }

            /* Enlace "Inicia sesión aquí" con degradado en letras */
            a {
                font-weight: bold;
                background: linear-gradient(to right, #ff66b2, #6699ff, #9933ff);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                text-decoration: none;
            }

            a:hover {
                text-decoration: underline;
            }

            /* Diseño responsive */
            @media (max-width: 500px) {
                .form-container {
                    width: 90%;
                    padding: 20px;
                }

                h2 {
                    font-size: 20px;
                }
            }
        </style>

        <script>
            function validarCedula(cedula) {
                if (!/^\d{10}$/.test(cedula)) {
                    alert("La cédula debe contener exactamente 10 dígitos numéricos.");
                    return false;
                }

                var codigoProvincia = parseInt(cedula.substring(0, 2));
                var provinciasValidas = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 30];

                if (!provinciasValidas.includes(codigoProvincia)) {
                    alert("La cédula ingresada no es ecuatoriana. Código de provincia inválido.");
                    return false;
                }

                var coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
                var suma = 0;

                for (var i = 0; i < 9; i++) {
                    var valor = parseInt(cedula[i]) * coeficientes[i];
                    if (valor >= 10) {
                        valor -= 9;
                    }
                    suma += valor;
                }

                var digitoVerificador = (10 - (suma % 10)) % 10;

                if (digitoVerificador !== parseInt(cedula[9])) {
                    alert("La cédula ingresada no es válida. Error en el dígito verificador.");
                    return false;
                }

                return true;
            }

            function validarFormulario() {
                var cedula = document.getElementById("cedula").value;
                var nombres = document.getElementById("nombres").value.trim();
                var apellidos = document.getElementById("apellidos").value.trim();
                var genero = document.getElementById("genero").value;
                var fechaNacimiento = document.getElementById("fecha_nacimiento").value;
                var provincia = document.getElementById("provincia").value.trim();
                var ciudad = document.getElementById("ciudad").value.trim();
                var clave = document.getElementById("password").value;

                var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/;
                if (!validarCedula(cedula))
                    return false;
                if (!soloLetras.test(nombres) || !soloLetras.test(apellidos)) {
                    alert("Los nombres y apellidos solo pueden contener letras.");
                    return false;
                }
                if (!soloLetras.test(provincia) || !soloLetras.test(ciudad)) {
                    alert("La provincia y la ciudad solo pueden contener letras.");
                    return false;
                }
                if (genero === "") {
                    alert("Por favor, seleccione su género.");
                    return false;
                }
                var fechaActual = new Date();
                var fechaMinima = new Date();
                fechaMinima.setFullYear(fechaActual.getFullYear() - 16);
                if (new Date(fechaNacimiento) > fechaMinima) {
                    alert("Debes tener al menos 16 años para registrarte.");
                    return false;
                }
                if (clave.length < 6) {
                    alert("La contraseña debe tener al menos 6 caracteres.");
                    return false;
                }

                return true;
            }
        </script>
    </head>
    <body>

        <div class="form-container">
            <h2>Registrate</h2>

            <form action="registrar_usuario.php" method="POST" onsubmit="return validarFormulario();">
                <label for="cedula">Cédula:</label>
                <input type="text" id="cedula" name="cedula" required>

                <label for="nombres">Nombre:</label>
                <input type="text" id="nombres" name="nombre" required>

                <label for="apellidos">Apellido:</label>
                <input type="text" id="apellidos" name="apellido" required>

                <label for="genero">Género:</label>
                <select id="genero" name="genero" required>
                    <option value="">Seleccionar</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>

                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

                <label for="provincia">Provincia:</label>
                <input type="text" id="provincia" name="provincia" required>

                <label for="ciudad">Ciudad:</label>
                <input type="text" id="ciudad" name="ciudad" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="clave" required>

                <button type="submit">Registrarse</button>
            </form>

            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>

    </body>
</html>
