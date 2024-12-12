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

// Manejar la eliminación de un registro
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];

    $sql = "DELETE FROM adoptantes WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Manejar la inserción o actualización de un registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $nombre = $_POST['nombre'];
    $genero = $_POST['genero'];
    $edad = $_POST['edad'];
    $dui = $_POST['dui'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    $ocupacion = $_POST['ocupacion'];

    // Verificar si el correo ya está registrado
    $sql_check = "SELECT * FROM adoptantes WHERE correo=?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $correo);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $stmt_check->close();
        $conn->close();
        // Mostrar alerta de SweetAlert2
        echo '<!DOCTYPE html>
              <html lang="es">
              <head>
                  <meta charset="UTF-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
                  <title>Error de Registro</title>
              </head>
              <body>
                  <script type="text/javascript">',
             'Swal.fire({',
             '  icon: "error",',
             '  title: "Error!",',
             '  text: "El correo electrónico ya está registrado.",',
             '}).then((result) => {',
             '  if (result.isConfirmed) {',
             '    window.location.href = "registro_adoptantes.html";',
             '  }',
             '});',
             '</script>
              </body>
              </html>';
        exit();
    }
    $stmt_check->close();

    // Insertar un nuevo registro
    $sql = "INSERT INTO adoptantes (nombre, genero, edad, dui, telefono, correo, direccion, ocupacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssss", $nombre, $genero, $edad, $dui, $telefono, $correo, $direccion, $ocupacion);

    // Ejecutar la consulta y verificar el resultado
    if ($stmt->execute()) {
        // Si el registro se inserta correctamente, mostrar la alerta de éxito en el frontend
        echo "<script>
                window.location.href = 'registro_adoptantes.html?success=true';
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