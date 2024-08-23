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

// Verificar si el correo ya existe
$sql = "SELECT COUNT(*) FROM usuarios WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    $message = "El correo electrónico ya está registrado.";
} else {
    // Preparar la consulta SQL para insertar
    $sql = "INSERT INTO usuarios (nombre, correo, usuario, contraseña) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $correo, $usuario, $contraseña);

    // Inicializar mensaje
    $message = "";

    // Intentar ejecutar la consulta
    try {
        if ($stmt->execute()) {
            $message = "Usuario Guardado!";
            header("Location: vista_usuarios.php");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        $message = "Error: " . $e->getMessage();
    }

    // Cerrar la consulta
    $stmt->close();
}

