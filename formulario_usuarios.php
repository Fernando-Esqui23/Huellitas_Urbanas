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

// Ejecutar la consulta y verificar el resultado
try {
    if ($stmt->execute()) {
        echo "Registro guardado con éxito";
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) { // Código de error para entrada duplicada
        echo "El correo electrónico ya está registrado.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}

// Cerrar la consulta y la conexión
$stmt->close();
$conn->close();
?>

