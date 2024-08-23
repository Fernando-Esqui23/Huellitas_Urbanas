<?php
// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "usuarios";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Recoger los datos del formulario
$id = $_POST['id'];
$tipo_mascota = $_POST['tipo_mascota'];
$nombre = $_POST['nombre'];
$fecha_rescate = $_POST['fecha_rescate'];
$edad = $_POST['edad'];
$discapacidad = $_POST['discapacidad'];
$detalles_discapacidad = $_POST['detalles_discapacidad'];

// Preparar y ejecutar la consulta de actualización
$sql = "UPDATE mascotas SET tipo_mascota=?, nombre=?, fecha_rescate=?, edad=?, discapacidad=?, detalles_discapacidad=? WHERE id=?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ssssssi", $tipo_mascota, $nombre, $fecha_rescate, $edad, $discapacidad, $detalles_discapacidad, $id);
    $stmt->execute();
    $stmt->close();
} else {
    echo "Error al preparar la consulta de actualización: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
