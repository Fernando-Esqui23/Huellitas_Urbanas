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
        .actions {
            display: flex;
            justify-content: flex-start; /* Align actions to the start (left) */
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
            background-color: #dc3545; /* Red color for delete button */
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
            background-color: ##033E8C;
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
        .button-group {
            text-align: left; /* Alineación de los botones */
            margin-bottom: 10px; /* Espacio entre los botones y el formulario de búsqueda */
        }

        .button-group {
            text-align: left; /* Alineación de los botones */
            margin-bottom: 10px; /* Espacio entre los botones y el formulario de búsqueda */
        }

        .button-group a {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px; /* Espacio entre los botones */
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Vista de Usuarios</h1>
        </header>

        <!-- Contenedor para los botones "Nuevo" y "Guardar" -->
        <div class="button-group">
            <a href="registro_mascotas.html">Nuevo</a>
            <a href="main.html">rer</a>
        </div>

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
                                    <a href="#" class="edit-btn" data-id="<?php echo urlencode($row['id']); ?>" data-nombre="<?php echo urlencode($row['nombre']); ?>" data-correo="<?php echo urlencode($row['correo']); ?>" data-usuario="<?php echo urlencode($row['usuario']); ?>" data-contraseña="<?php echo urlencode($row['contraseña']); ?>">Editar</a>
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
    </div>

    <!-- Modal de Edición -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Editar Usuario</h2>
            <form id="editForm">
                <input type="hidden" id="editId" name="id">
                <label for="editNombre">Nombre:</label>
                <input type="text" id="editNombre" name="nombre"><br><br>
                <label for="editCorreo">Correo Electrónico:</label>
                <input type="email" id="editCorreo" name="correo"><br><br>
                <label for="editUsuario">Usuario:</label>
                <input type="text" id="editUsuario" name="usuario"><br><br>
                <label for="editContraseña">Contraseña:</label>
                <input type="password" id="editContraseña" name="contraseña"><br><br>
                <button type="button" id="saveChangesBtn" class="center-button">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script>
        // Modal JS
        var modal = document.getElementById("editModal");
        var span = document.getElementsByClassName("close")[0];
        var editButtons = document.querySelectorAll(".edit-btn");
        var saveChangesBtn = document.getElementById("saveChangesBtn");

        editButtons.forEach(function(btn) {
            btn.addEventListener("click", function(event) {
                var id = event.target.getAttribute("data-id");
                var nombre = decodeURIComponent(event.target.getAttribute("data-nombre"));
                var correo = decodeURIComponent(event.target.getAttribute("data-correo"));
                var usuario = decodeURIComponent(event.target.getAttribute("data-usuario"));
                

                var contraseña = decodeURIComponent(event.target.getAttribute("data-contraseña"));

                document.getElementById("editId").value = id;
                document.getElementById("editNombre").value = nombre;
                document.getElementById("editCorreo").value = correo;
                document.getElementById("editUsuario").value = usuario;
                document.getElementById("editContraseña").value = contraseña;

                modal.style.display = "block";
            });
        });

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        saveChangesBtn.addEventListener("click", function() {
            alert("Los cambios han sido guardados.");
            modal.style.display = "none";
        });
    </script>
</body>
</html>
<?php
// Cerrar conexión
$conn->close();
?>
