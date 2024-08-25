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

// Manejar la eliminación de un registro
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];

    $sql = "DELETE FROM usuarios WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Manejar la inserción o actualización de un registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];

    // Verificar si el registro ya existe
    if ($id) {
        // Actualizar el registro existente
        $sql = "UPDATE usuarios SET nombre=?, correo=?, usuario=?, contraseña=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $correo, $usuario, $contraseña, $id);
    } else {
        // Verificar si el correo ya está registrado
        $sql_check = "SELECT * FROM usuarios WHERE correo=?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $correo);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo "<script>
                    alert('El correo electrónico ya está registrado.');
                    window.location.href = 'registro_usuarios.html';
                  </script>";
            $stmt_check->close();
            $conn->close();
            exit();
        }
        $stmt_check->close();

        // Insertar un nuevo registro
        $sql = "INSERT INTO usuarios (nombre, correo, usuario, contraseña) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $correo, $usuario, $contraseña);
    }

    // Ejecutar la consulta y verificar el resultado
    if ($stmt->execute()) {
        // Redirigir a la vista de usuarios
        header("Location: vista_usuarios.php");
        exit();
    } else {
        echo "Error al insertar el registro: " . $stmt->error;
    }

    // Cerrar la consulta
    $stmt->close();
    $conn->close();
}
?>

