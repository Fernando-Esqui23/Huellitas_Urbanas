<?php
// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Recoger los datos del formulario
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$genero = $_POST['genero'];
$edad = $_POST['edad'];
$dui = $_POST['dui'];
$telefono = $_POST['telefono'];
$correo = $_POST['correo'];
$direccion = $_POST['direccion'];
$ocupacion = $_POST['ocupacion'];

// Preparar y ejecutar la consulta de actualización
$sql = "UPDATE adoptantes SET nombre=?, genero=?, edad=?, dui=?, telefono=?, correo=?, direccion=?, ocupacion=? WHERE id=?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ssissssss", $nombre, $genero, $edad, $dui, $telefono, $correo, $direccion, $ocupacion, $id);
    $stmt->execute();
    $stmt->close();
    // Redirigir a una página de confirmación o vista de adoptantes
    header("Location: vista_adoptantes.php");
    exit();
} else {
    echo "Error al preparar la consulta de actualización: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
