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

    $sql = "DELETE FROM usuarios WHERE id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error al preparar la consulta de eliminación: " . $conn->error;
    }
}

// Manejar la actualización de un registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];

    $sql = "UPDATE usuarios SET nombre=?, correo=?, usuario=?, contraseña=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $correo, $usuario, $contraseña, $id);

    if ($stmt->execute()) {
        echo "Usuario actualizado correctamente.";
    } else {
        echo "Error al actualizar usuario: " . $stmt->error;
    }

    $stmt->close();
}

// Manejar la búsqueda por nombre
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Obtener los registros para mostrar, con opción de búsqueda
if ($search !== "") {
    $sql = "SELECT * FROM usuarios WHERE nombre LIKE ?";
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
    $sql = "SELECT * FROM usuarios";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Usuarios</title>
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
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .search-form input[type="submit"]:hover {
            background-color: #45a049;
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
            background-color: #dc3545;
        }
        .actions .delete-btn:hover {
            background-color: #c82333;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
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
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Vista de Usuarios</h1>
        </header>

        <!-- Formulario de búsqueda -->
        <div class="search-form">
            <form method="GET" action="vista_usuarios.php">
                <label for="search">Buscar por Nombre:</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="submit" value="Buscar">
                <?php if ($search !== ""): ?>
                    <a href="vista_usuarios.php">Mostrar Todos</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="records">
            <h2>Registros de Usuarios</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo Electrónico</th>
                        <th>Usuario</th>
                        <th>Contraseña</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                                <td><?php echo htmlspecialchars($row['contraseña']); ?></td>
                                <td class="actions">
                                    <a href="javascript:void(0);" class="edit-btn" 
                                        data-id="<?php echo htmlspecialchars($row['id']); ?>" 
                                        data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>" 
                                        data-correo="<?php echo htmlspecialchars($row['correo']); ?>" 
                                        data-usuario="<?php echo htmlspecialchars($row['usuario']); ?>" 
                                        data-contraseña="<?php echo htmlspecialchars($row['contraseña']); ?>">
                                        Editar
                                    </a>
                                    <a href="vista_usuarios.php?action=delete&id=<?php echo urlencode($row['id']); ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No se encontraron registros.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Editar Usuario</h2>
                <form id="editForm">
                    <input type="hidden" id="editId">
                    <label for="editNombre">Nombre:</label>
                    <input type="text" id="editNombre" required>
                    
                    <label for="editCorreo">Correo Electrónico:</label>
                    <input type="email" id="editCorreo" required>
                    
                    <label for="editUsuario">Usuario:</label>
                    <input type="text" id="editUsuario" required>
                    
                    <label for="editContraseña">Contraseña:</label>
                    <input type="password" id="editContraseña" required>
                    
                    <button type="submit">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                // Obtener datos del usuario
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                const correo = this.getAttribute('data-correo');
                const usuario = this.getAttribute('data-usuario');
                const contraseña = this.getAttribute('data-contraseña');
                
                // Asignar los valores al formulario modal
                document.getElementById('editId').value = id;
                document.getElementById('editNombre').value = nombre;
                document.getElementById('editCorreo').value = correo;
                document.getElementById('editUsuario').value = usuario;
                document.getElementById('editContraseña').value = contraseña;

                // Mostrar el modal
                document.getElementById('editModal').style.display = "block";
            });
        });

        // Cerrar el modal al hacer clic en la "X"
        document.querySelector('.close').onclick = function() {
            document.getElementById('editModal').style.display = "none";
        };

        // Cerrar el modal al hacer clic fuera de la ventana modal
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                document.getElementById('editModal').style.display = "none";
            }
        };

        // Manejar el envío del formulario de edición
        document.getElementById('editForm').onsubmit = function(event) {
            event.preventDefault();

            // Enviar los datos mediante AJAX
            const formData = new FormData();
            formData.append('id', document.getElementById('editId').value);
            formData.append('nombre', document.getElementById('editNombre').value);
            formData.append('correo', document.getElementById('editCorreo').value);
            formData.append('usuario', document.getElementById('editUsuario').value);
            formData.append('contraseña', document.getElementById('editContraseña').value);

            fetch('vista_usuarios.php', {
                method: 'POST',
                body: formData
            }).then(response => response.text())
              .then(data => {
                  alert(data); // Mostrar mensaje de éxito/error
                  location.reload(); // Recargar la página para reflejar los cambios
              }).catch(error => {
                  console.error('Error:', error);
              });
        };
    </script>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
