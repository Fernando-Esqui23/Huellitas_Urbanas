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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        echo "<script>alert('El correo electrónico ya está registrado.');</script>";
    } else {
        // Preparar la consulta SQL para insertar
        $sql = "INSERT INTO usuarios (nombre, correo, usuario, contraseña) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $correo, $usuario, $contraseña);

        // Intentar ejecutar la consulta
        try {
            if ($stmt->execute()) {
                header("Location: vista_usuarios.php");
                exit(); // Asegura que el script se detenga después de la redirección
            }
        } catch (mysqli_sql_exception $e) {
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }

        // Cerrar la consulta
        $stmt->close();
    }
}

// Cerrar la conexión
$conn->close();
?>

