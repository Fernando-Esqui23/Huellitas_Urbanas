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
            margin-top: 20px;
        }
        .search-form input[type="text"] {
            padding: 6px;
            width: 200px;
        }
        .search-form input[type="submit"] {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .search-form input[type="submit"]:hover {
            background-color: #45a049;
        }
        .actions {
            display: flex;
            justify-content: flex-start; /* Alínea acciones a la izquierda */
        }
        .actions a {
            margin-right: 10px;
            padding: 6px 12px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
        }
        .actions a:hover {
            background-color: #0056b3;
        }
        .actions .delete-btn {
            background-color: #dc3545; /* Color rojo para el botón de eliminar */
        }
        .actions .delete-btn:hover {
            background-color: #c82333;
        }

        /* Estilos para el modal */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; 
            background-color: rgba(0,0,0,0.4); 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 600px;
        }

        .center-button {
            display: inline-block;
            margin: 20px auto;
            background-color: #033E8C;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
            margin-right: 10px;
            font-size: 13px; /* Reducir un poco el tamaño de la fuente */
            text-align: center;
            white-space: nowrap; /* Asegura que el texto no se divida */
        }

        .center-button:hover {
            background-color: #45a049;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Estilo para los botones en la parte superior */
        .top-buttons {
            margin-bottom: 20px; /* Ajusta el margen inferior si es necesario */
            padding: 10px;
            text-align: center; /* Alinea los botones al centro */
        }

        .top-buttons a {
            display: inline-block;
            padding: 10px 20px;
            margin-left: 10px; /* Espacio entre botones */
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .top-buttons a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Vista de Mascotas</h1>
        </header>

        <!-- Contenedor para los botones en la parte superior -->
        <div class="top-buttons">
            <a href="registro_mascotas.html" class="center-button">Nuevo</a>
            <a href="main.html" class="center-button">Regresar</a>
        </div>

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
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['tipo_mascota']; ?></td>
                                <td><?php echo $row['nombre']; ?></td>
                                <td><?php echo $row['fecha_rescate']; ?></td>
                                <td><?php echo $row['edad']; ?></td>
                                <td><?php echo $row['discapacidad']; ?></td>
                                <td><?php echo $row['detalles_discapacidad']; ?></td>
                                <td class="actions">
                                    <a href="editar_mascota.php?id=<?php echo $row['id']; ?>" class="edit-btn">Editar</a>
                                    <a href="vista_mascotas.php?action=delete&id=<?php echo $row['id']; ?>" class="delete-btn">Eliminar</a>
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

        <!-- Modal para Editar Mascota -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Editar Mascota</h2>
                <form id="editForm" method="POST" action="update_mascota.php">
                    <!-- Aquí debes incluir los campos del formulario que quieres editar -->
                    <input type="hidden" id="editId" name="id">
                    <label for="editNombre">Nombre:</label>
                    <input type="text" id="editNombre" name="nombre" required>
                    <!-- Agrega más campos según sea necesario -->
                    <button type="submit" class="center-button">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Mostrar el modal cuando se haga clic en el botón de editar
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const id = this.href.split('id=')[1];
                fetch(`get_mascota.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('editId').value = data.id;
                        document.getElementById('editNombre').value = data.nombre;
                        // Rellena los demás campos según sea necesario
                        document.getElementById('editModal').style.display = 'block';
                    });
            });
        });

        // Cerrar el modal cuando se haga clic en la cruz
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('editModal').style.display = 'none';
        });

        // Cerrar el modal si se hace clic fuera del contenido del modal
        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('editModal')) {
                document.getElementById('editModal').style.display = 'none';
            }
        });

        // Mostrar alerta al guardar cambios
        document.getElementById('editForm').addEventListener('submit', function(event) {
            alert('Cambios guardados correctamente.');
        });
    </script>
</body>
</html>
