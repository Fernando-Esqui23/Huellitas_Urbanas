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

// Obtener los datos del formulario
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$direccion = $_POST['direccion'];
$telefono = $_POST['telefono'];
$dui = $_POST['dui'];
$correo_electronico = $_POST['correo_electronico'];
$monto = $_POST['monto'];
$frecuencia_donacion = $_POST['frecuencia_donacion'];
$metodo_pago = $_POST['metodo_pago'];
$fecha_donacion = $_POST['fecha_donacion'];
$destino_donacion = $_POST['destino_donacion'];

// Preparar y ejecutar la consulta de actualización
$sql = "UPDATE donaciones SET nombre = ?, direccion = ?, telefono = ?, dui = ?, correo_electronico = ?, monto = ?, frecuencia_donacion = ?, metodo_pago = ?, fecha_donacion = ?, destino_donacion = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ssssssssssi", $nombre, $direccion, $telefono, $dui, $correo_electronico, $monto, $frecuencia_donacion, $metodo_pago, $fecha_donacion, $destino_donacion, $id);
    $stmt->execute();
    $stmt->close();
} else {
    echo "Error al preparar la consulta de actualización: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>