<?php
// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root"; // Reemplaza con tu usuario de MySQL
$password = ""; // Reemplaza con tu contraseña de MySQL
$database = "usuarios"; // La base de datos que ya tienes configurada

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$usuario = $_POST['usuario'];
$contraseña = $_POST['contraseña'];

// Preparar la consulta SQL
$sql = "INSERT INTO usuarios (nombre, correo, usuario, contraseña) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nombre, $correo, $usuario, $contraseña);

// Iniciar buffer de salida
ob_start();

try {
    if ($stmt->execute()) {
        $message = "Usuario Guardado!";
    } else {
        $message = "Error al guardar el usuario.";
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) { // Código de error para entrada duplicada
        $message = "El correo electrónico ya está registrado.";
    } else {
        $message = "Error: " . $e->getMessage();
    }
}

// Cerrar la consulta y la conexión
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Registro</title>
    <style>
        /* Estilo para el fondo oscuro del modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        /* Estilo para la ventana del modal */
        .modal {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
            text-align: center;
            max-width: 300px;
            width: 80%;
        }

        /* Estilo del botón de cerrar */
        .modal button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .modal button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body onload="showModal()">
    <!-- Modal personalizado -->
    <div class="modal-overlay" id="welcomeModal">
        <div class="modal">
            <h2><?php echo $message; ?></h2>
            <button onclick="closeModal()">Cerrar</button>
        </div>
    </div>

    <script>
        function showModal() {
            document.getElementById('welcomeModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('welcomeModal').style.display = 'none';
            window.location.href = "formulario.html"; // Redirigir a otra página si es necesario
        }
    </script>
</body>
</html>

<?php
// Enviar el contenido del buffer de salida
ob_end_flush();
?>
