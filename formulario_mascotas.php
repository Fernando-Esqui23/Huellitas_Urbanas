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
$tipo_mascota = $_POST['tipo-mascota'];
$nombre = $_POST['nombre'];
$fecha_rescate = $_POST['fecha-rescate'];
$edad = $_POST['edad'];
$discapacidad = isset($_POST['discapacidad']) ? $_POST['discapacidad'] : NULL;
$detalles_discapacidad = isset($_POST['detalles-discapacidad']) ? $_POST['detalles-discapacidad'] : NULL;

// Preparar la consulta SQL
$sql = "INSERT INTO mascotas (tipo_mascota, nombre, fecha_rescate, edad, discapacidad, detalles_discapacidad) 
VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $tipo_mascota, $nombre, $fecha_rescate, $edad, $discapacidad, $detalles_discapacidad);


 // Ejecutar la consulta y verificar el resultado
 if ($stmt->execute()) {
    // Redirigir a la vista de mascotas después de guardar
    header("Location: vista_mascotas.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
