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

// Manejar la inserción de un nuevo registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $dui = $_POST['dui'];
    $correo_electronico = $_POST['correo-electronico'];
    $monto = $_POST['monto'];
    $frecuencia_donacion = $_POST['frecuencia-donacion'];
    $metodo_pago = $_POST['metodo-pago'];
    $fecha_donacion = $_POST['fecha-donacion'];
    $destino_donacion = $_POST['destino-donacion'];

    // Verificar si el correo ya está registrado
    $sql_check = "SELECT * FROM donaciones WHERE correo_electronico=?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $correo_electronico);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "<script>
                alert('El correo electrónico ya está registrado.');
                window.location.href = 'registro _donaciones.html';
              </script>";
        $stmt_check->close();
        $conn->close();
        exit();
    }
    $stmt_check->close();

    // Insertar un nuevo registro
    $sql = "INSERT INTO donaciones (nombre, direccion, telefono, dui, correo_electronico, monto, frecuencia_donacion, metodo_pago, fecha_donacion, destino_donacion) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissssss", $nombre, $direccion, $telefono, $dui, $correo_electronico, $monto, $frecuencia_donacion, $metodo_pago, $fecha_donacion, $destino_donacion);

   // Ejecutar la consulta y verificar el resultado
   if ($stmt->execute()) {
    // Redirigir a la vista de usuarios
    header("Location: vista_donaciones.php");
    exit();
} else {
    echo "Error al insertar el registro: " . $stmt->error;
}

// Cerrar la consulta
$stmt->close();
$conn->close();
}
?>
