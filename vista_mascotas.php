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
                                    <a href="#" class="edit-btn" data-id="<?php echo htmlspecialchars($row['id']); ?>" data-tipo="<?php echo htmlspecialchars($row['tipo_mascota']); ?>" data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>" data-fecha="<?php echo htmlspecialchars($row['fecha_rescate']); ?>" data-edad="<?php echo htmlspecialchars($row['edad']); ?>" data-discapacidad="<?php echo htmlspecialchars($row['discapacidad']); ?>" data-detalles="<?php echo htmlspecialchars($row['detalles_discapacidad']); ?>">Editar</a>
                                    <a href="vista_mascotas.php?action=delete&id=<?php echo urlencode($row['id']); ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');">Eliminar</a>
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

    <!-- Modal -->
   <!-- Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Mascota</h2>
        <form id="editForm">
            <input type="hidden" id="editId" name="id">
            <label for="editTipo">Tipo de Mascota:</label>
            <input type="text" id="editTipo" name="tipo_mascota" required>
            <br>
            <label for="editNombre">Nombre:</label>
            <input type="text" id="editNombre" name="nombre" required>
            <br>
            <label for="editFecha">Fecha de Rescate:</label>
            <input type="date" id="editFecha" name="fecha_rescate" required>
            <br>
            <label for="editEdad">Edad:</label>
            <input type="number" id="editEdad" name="edad" required>
            <br>
            <label for="editDiscapacidad">Discapacidad:</label>
            <input type="text" id="editDiscapacidad" name="discapacidad">
            <br>
            <label for="editDetalles">Detalles de Discapacidad:</label>
            <input type="text" id="editDetalles" name="detalles_discapacidad">
            <br>
            <button type="button" id="saveChanges">Actualizar Cambios</button>
        </form>
    </div>
</div>

<script>
    // Obtener el modal
    var modal = document.getElementById("editModal");
    var span = document.getElementsByClassName("close")[0];

    // Abrir el modal al hacer clic en el botón de editar
    document.querySelectorAll(".edit-btn").forEach(function(button) {
        button.addEventListener("click", function() {
            var id = this.getAttribute("data-id");
            var tipo = this.getAttribute("data-tipo");
            var nombre = this.getAttribute("data-nombre");
            var fecha = this.getAttribute("data-fecha");
            var edad = this.getAttribute("data-edad");
            var discapacidad = this.getAttribute("data-discapacidad");
            var detalles = this.getAttribute("data-detalles");

            document.getElementById("editId").value = id;
            document.getElementById("editTipo").value = tipo;
            document.getElementById("editNombre").value = nombre;
            document.getElementById("editFecha").value = fecha;
            document.getElementById("editEdad").value = edad;
            document.getElementById("editDiscapacidad").value = discapacidad;
            document.getElementById("editDetalles").value = detalles;

            modal.style.display = "block";
        });
    });

    // Cerrar el modal al hacer clic en el botón (x)
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Cerrar el modal al hacer clic fuera del contenido del modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Guardar cambios y enviar al servidor
    document.getElementById("saveChanges").onclick = function() {
        var formData = new FormData(document.getElementById("editForm"));
        fetch("update_mascota.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert("Cambios actualizados.");
            modal.style.display = "none";
            location.reload(); // Recargar la página para reflejar los cambios
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>

</body>
</html>

