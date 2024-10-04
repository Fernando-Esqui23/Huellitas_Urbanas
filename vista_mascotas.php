<?php
// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "usuarios";

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
    display: flex;
    flex-direction: column; /* Coloca los elementos en una columna */
    align-items: flex-start; /* Alinea los elementos a la izquierda */
}

.search-form input[type="text"] {
    padding: 6px;
    width: 200px;
    margin-bottom: 10px; /* Añade espacio entre la barra de búsqueda y el botón */
}

.search-form input[type="submit"] {
    padding: 6px 12px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    align-self: flex-start; /* Alinea el botón a la izquierda */
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
            background-color: #04B2D9;
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
            padding: 10px 20px;
            margin: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .center-button:hover {
            background-color: #0056b3;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
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
            text-align: left; /* Alineación de los botones */
            margin-bottom: 20px; /* Espacio entre los botones y el formulario de búsqueda */
        }

        .top-buttons a {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px; /* Espacio entre los botones */
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .top-buttons a:hover {
            background-color: #0056b3;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
        }

        
        /* Estilo para el botón de Actualizar Cambios */
#saveChanges {
    display: inline-flex; /* Usa flexbox para centrar el contenido */
    align-items: center; /* Centra verticalmente el texto dentro del botón */
    justify-content: center; /* Centra horizontalmente el texto dentro del botón */
    padding: 8px 16px; /* Espaciado interno ajustado */
    background-color: #28a745; /* Verde para el botón */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    text-align: center; /* Centra el texto dentro del botón */
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    margin-top: 20px; /* Espacio en la parte superior */
    white-space: nowrap; /* Evita que el texto se divida en varias líneas */
    width: 30%; /* Ajusta el ancho del botón al contenido */
    max-width: 100%; /* Asegura que el botón no se desborde */
    margin-left: 200px;
    
}


#saveChanges:hover {
    background-color: #218838; /* Verde más oscuro en hover */
    box-shadow: 0px 4px 8px rgba(0,0,0,0.2); /* Sombra en hover */
}

#saveChanges:focus {
    outline: none; /* Elimina el contorno de enfoque */
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.5); /* Contorno verde al enfocar */
}

/* Estilos para el modal */
.modal-content {
    padding: 20px;
    background-color: #fefefe;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    margin: auto;
}

/* Estilos para los títulos de los campos */
.form-group {
    margin-bottom: 15px;
}

.form-group h3 {
    margin: 0;
    font-size: 0.8em;;
    color: #333;
}

.form-group label {
    display: block;
    margin: 5px 0;
}

.form-group input {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
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
                                <!--
                                <a href="#" class="edit-btn" onclick="openModal(<?php echo $row['id']; ?>, '<?php echo $row['tipo_mascota']; ?>', '<?php echo $row['nombre']; ?>', '<?php echo $row['fecha_rescate']; ?>', '<?php echo $row['edad']; ?>', '<?php echo $row['discapacidad']; ?>', '<?php echo $row['detalles_discapacidad']; ?>')"><img src="images/iconoeditar.png" alt="Editar" style="width: 20px; height: 20px;"></a>
                                <a href="vista_mascotas.php?action=delete&id=<?php echo urlencode($row['id']); ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');"><img src="images/iconoeliminar.png" alt="Eliminar" style="width: 20px; height: 20px;"></a>
                                -->
                                <a href="#" class="edit-btn" onclick="openModal(<?php echo $row['id']; ?>, '<?php echo $row['tipo_mascota']; ?>', '<?php echo $row['nombre']; ?>', '<?php echo $row['fecha_rescate']; ?>', '<?php echo $row['edad']; ?>', '<?php echo $row['discapacidad']; ?>', '<?php echo $row['detalles_discapacidad']; ?>')">Editar</a>
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
<!-- Contenedor para los botones en la parte superior -->
<div class="top-buttons">
            <a href="registro_mascotas.html" class="center-button">Nuevo</a>
            <a href="main.html" class="center-button">Regresar</a>
        </div>
      <!-- Modal para editar una mascota -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Editar Mascota</h2>
        <form id="editForm">
            <input type="hidden" name="id" id="editId">

            <div class="form-group">
                <h3>Tipo de Mascota:</h3>
                <label for="editTipoMascota">Tipo de Mascota:</label>
                <input type="text" name="tipo_mascota" id="editTipoMascota" required>
            </div>

            <div class="form-group">
                <h3>Nombre:</h3>
                <label for="editNombre">Nombre:</label>
                <input type="text" name="nombre" id="editNombre" required>
            </div>

            <div class="form-group">
                <h3>Fecha de Rescate:</h3>
                <label for="editFechaRescate">Fecha de Rescate:</label>
                <input type="date" name="fecha_rescate" id="editFechaRescate" required>
            </div>

            <div class="form-group">
                <h3>Edad:</h3>
                <label for="editEdad">Edad:</label>
                <input type="number" name="edad" id="editEdad" required>
            </div>

            <div class="form-group">
                <h3>Discapacidad:</h3>
                <label for="editDiscapacidad">Discapacidad:</label>
                <input type="text" name="discapacidad" id="editDiscapacidad">
            </div>

            <div class="form-group">
                <h3>Detalles de Discapacidad:</h3>
                <label for="editDetallesDiscapacidad">Detalles de Discapacidad:</label>
                <textarea name="detalles_discapacidad" id="editDetallesDiscapacidad"></textarea>
            </div>

            <input type="button" id="saveChanges" value="Actualizar Cambios">
        </form>
    </div>
</div>


    <script>
        function openModal(id, tipo_mascota, nombre, fecha_rescate, edad, discapacidad, detalles_discapacidad) {
            document.getElementById("editId").value = id;
            document.getElementById("editTipoMascota").value = tipo_mascota;
            document.getElementById("editNombre").value = nombre;
            document.getElementById("editFechaRescate").value = fecha_rescate;
            document.getElementById("editEdad").value = edad;
            document.getElementById("editDiscapacidad").value = discapacidad;
            document.getElementById("editDetallesDiscapacidad").value = detalles_discapacidad;

            var modal = document.getElementById("editModal");
            modal.style.display = "block";
        }

        function closeModal() {
            var modal = document.getElementById("editModal");
            modal.style.display = "none";
        }

        document.getElementById("saveChanges").onclick = function() {
            var formData = new FormData(document.getElementById("editForm"));
            fetch("update_mascota.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert("Cambios actualizados.");
                closeModal();
                location.reload(); // Recargar la página para reflejar los cambios
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
