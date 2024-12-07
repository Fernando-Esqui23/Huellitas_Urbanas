<?php
// Datos de conexión a la base de datos
$servername = "mysql.webcindario.com";
$username = "huellitasurb";
$password = "kurama111023";
$dbname = "huellitasurb";
// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Recoger los datos del formulario
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$usuario = $_POST['usuario'];
$contraseña = $_POST['contraseña'];

// Preparar y ejecutar la consulta de actualización
$sql = "UPDATE usuarios SET nombre=?, correo=?, usuario=?, contraseña=? WHERE id=?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ssssi", $nombre, $correo, $usuario, $contraseña, $id);
    $stmt->execute();
    $stmt->close();
} else {
    echo "Error al preparar la consulta de actualización: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
