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
 
// Manejar la eliminación de un registro
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
 
    $sql = "DELETE FROM mascotas WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
 
// Manejar la inserción o actualización de un registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $tipo_mascota = $_POST['tipo-mascota'];
    $nombre = $_POST['nombre'];
    $fecha_rescate = $_POST['fecha-rescate'];
    $edad = $_POST['edad'];
    $discapacidad = isset($_POST['discapacidad']) ? $_POST['discapacidad'] : NULL;
    $detalles_discapacidad = isset($_POST['detalles-discapacidad']) ? $_POST['detalles-discapacidad'] : NULL;
 
    // Verificar si el registro ya existe
    if ($id) {
        // Actualizar el registro existente
        $sql = "UPDATE mascotas SET tipo_mascota=?, nombre=?, fecha_rescate=?, edad=?, discapacidad=?, detalles_discapacidad=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $tipo_mascota, $nombre, $fecha_rescate, $edad, $discapacidad, $detalles_discapacidad, $id);
    } else {
        // Verificar si el registro ya existe en la base de datos
        $sql_check = "SELECT * FROM mascotas WHERE nombre=? AND fecha_rescate=?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $nombre, $fecha_rescate);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
 
        if ($result_check->num_rows > 0) {
            echo "<script>
                    alert('La mascota ya esta registrada.');
                    window.location.href = 'registro_mascotas.html';
                  </script>";
            $stmt_check->close();
            $conn->close();
            exit();
        }
        $stmt_check->close();
 
        // Insertar un nuevo registro
        $sql = "INSERT INTO mascotas (tipo_mascota, nombre, fecha_rescate, edad, discapacidad, detalles_discapacidad) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $tipo_mascota, $nombre, $fecha_rescate, $edad, $discapacidad, $detalles_discapacidad);
    }
 
    // Ejecutar la consulta y verificar el resultado
    if ($stmt->execute()) {
        // Si el registro se inserta correctamente, mostrar la alerta de éxito en el frontend
        echo "<script>
                window.location.href = 'registro_mascotas.html?success=true';
              </script>";
        exit();
    } else {
        echo "Error al insertar el registro: " . $stmt->error;
    }

    // Cerrar la declaración
    $stmt->close();
    $conn->close();
}
?>
}

