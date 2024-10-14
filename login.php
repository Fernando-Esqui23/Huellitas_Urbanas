<?php
session_start();

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

    // Consulta para verificar las credenciales
    $sql = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Si el usuario existe, inicia sesión y redirige al menú
        $_SESSION['username'] = $user;
        header("Location: main.html");
        exit();
    } else {
        // Si las credenciales son incorrectas, muestra una alerta con JavaScript
        echo '<script type="text/javascript">',
             'alert("Usuario o contraseña incorrectos");',
             'window.location.href = "index.html";', // Redirige a la página de inicio o formulario
             '</script>';
    }
} else {
    // Si no es una solicitud POST
    echo "Método no permitido";
}


$conn->close();
?>
