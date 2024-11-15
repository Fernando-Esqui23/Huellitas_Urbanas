<?php
session_start();

// Habilitar la visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

// Crea la conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verifica que se haya enviado una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera las credenciales del formulario
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Usa declaraciones preparadas para evitar inyección SQL
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si el usuario existe, inicia sesión y redirige al menú
        $_SESSION['username'] = $user;
        header("Location: main.html");
        exit();
    } else {
        // Si las credenciales son incorrectas, muestra una alerta con SweetAlert2
        echo '<!DOCTYPE html>
              <html lang="es">
              <head>
                  <meta charset="UTF-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
                  <title>Error de Inicio de Sesión</title>
              </head>
              <body>
                  <script type="text/javascript">',
             'Swal.fire({',
             '  icon: "error",',
             '  title: "Error!",',
             '  text: "Usuario o contraseña incorrectos",',
             '}).then((result) => {',
             '  if (result.isConfirmed) {',
             '    window.location.href = "index.html";',
             '  }',
             '});',
             '</script>
              </body>
              </html>';
    }

    $stmt->close(); // Cierra la declaración
} else {
    // Si no es una solicitud POST
    echo "Método no permitido";
}

$conn->close();
?>