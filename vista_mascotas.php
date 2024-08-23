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

// Obtener todos los registros para mostrar
$sql = "SELECT * FROM mascotas";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Mascotas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Vista de Mascotas</h1>
        </header>
        <div class="records">
            <h2>Registros de Mascotas</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Tipo de Mascota</th>
                    <th>Nombre</th>
                    <th>Fecha de Rescate</th>
                    <th>Edad</th>
                    <th>Discapacidad</th>
                    <th>Detalles de Discapacidad</th>
                    <th>Acciones</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['tipo_mascota']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['fecha_rescate']; ?></td>
                    <td><?php echo $row['edad']; ?></td>
                    <td><?php echo $row['discapacidad']; ?></td>
                    <td><?php echo $row['detalles_discapacidad']; ?></td>
                    <td>
                        <a href="formulario_mascotas.php?id=<?php echo $row['id']; ?>">Editar</a>
                        <a href="vista_mascotas.php?action=delete&id=<?php echo $row['id']; ?>">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
