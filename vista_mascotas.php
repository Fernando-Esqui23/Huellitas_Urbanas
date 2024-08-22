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

    // Preparar y ejecutar la consulta de eliminación
    $sql = "DELETE FROM mascotas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error al preparar la consulta de eliminación: " . $conn->error;
    }
}

// Manejar la búsqueda por nombre
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Obtener los registros para mostrar, con opción de búsqueda
if ($search !== "") {
    // Preparar la consulta con búsqueda por nombre
    $sql = "SELECT * FROM mascotas WHERE nombre LIKE ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $like_search = "%" . $search . "%";
        $stmt->bind_param("s", $like_search);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        die("Error al preparar la consulta de búsqueda: " . $conn->error);
    }
} else {
    // Consulta sin filtros
    $sql = "SELECT * FROM mascotas";
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Mascotas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos básicos para mejorar la apariencia */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .records {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #dddddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .search-form {
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            padding: 6px;
            width: 200px;
        }
        .search-form input[type="submit"] {
            padding: 6px 12px;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: blue;
        }
        .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Vista de Mascotas</h1>
        </header>

        <!-- Formulario de búsqueda -->
        <div class="search-form">
            <form method="GET" action="vista_mascotas.php">
                <label for="search">Buscar por Nombre:</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="submit" value="Buscar">
                <?php if ($search !== ""): ?>
                    <a href="vista_mascotas.php">Mostrar Todos</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="records">
            <h2>Registros de Mascotas</h2>
            <table>
                <thead>
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
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['tipo_mascota']); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['fecha_rescate']); ?></td>
                                <td><?php echo htmlspecialchars($row['edad']); ?></td>
                                <td><?php echo htmlspecialchars($row['discapacidad']); ?></td>
                                <td><?php echo htmlspecialchars($row['detalles_discapacidad']); ?></td>
                                <td class="actions">
                                    <a href="formulario_mascotas.php?id=<?php echo urlencode($row['id']); ?>">Editar</a>
                                    <a href="vista_mascotas.php?action=delete&id=<?php echo urlencode($row['id']); ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No se encontraron registros.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php
// Cerrar la conexión
$conn->close();
?>
