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
    $sql = "DELETE FROM adoptantes WHERE id = ?";
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
    $sql = "SELECT * FROM adoptantes WHERE nombre LIKE ?";
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
    $sql = "SELECT * FROM adoptantes";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Adoptantes</title>
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
            margin-top: -20px;
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
            <h1>Vista de Adoptantes</h1>
        </header>
  
        <!-- Formulario de búsqueda -->
        <div class="search-form">
            <form method="GET" action="vista_adoptantes.php">
                <label for="search">Buscar por Nombre:</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="submit" value="Buscar">
                <?php if ($search !== ""): ?>
                    <a href="vista_adoptantes.php">Mostrar Todos</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="records">
            <!--<h2>Registros de Adoptantes</h2>-->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Genero</th>
                        <th>Edad</th>
                        <th>DUI</th>
                        <th>Telefono</th>
                        <th>Correo Electronico</th>
                        <th>Direccion</th>
                        <th>Ocupacion</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['id']}</td>";
                                echo "<td>{$row['nombre']}</td>";
                                echo "<td>{$row['genero']}</td>";
                                echo "<td>{$row['edad']}</td>";
                                echo "<td>{$row['dui']}</td>";
                                echo "<td>{$row['telefono']}</td>";
                                echo "<td>{$row['correo']}</td>";
                                echo "<td>{$row['direccion']}</td>";
                                echo "<td>{$row['ocupacion']}</td>";
                                echo "<td class='actions'>";
                                echo "<a href='generar_pdf.php?id={$row['id']}'>Generar PDF</a>";

                                echo "<a href='#' class='edit-btn' onclick=\"openModal({$row['id']}, '{$row['nombre']}', '{$row['genero']}', '{$row['edad']}', '{$row['dui']}', '{$row['telefono']}', '{$row['correo']}', '{$row['direccion']}', '{$row['ocupacion']}');\">
                                Editar
                                </a>";
                                
                                echo "<a href='vista_adoptantes.php?action=delete&id=" . urlencode($row['id']) . "' class='delete-btn' onclick=\"return confirmarEliminacion(event, this);\" style='color: white; background-color: #dc3545; padding: 5px 10px; border-radius: 5px; text-decoration: none;'>Eliminar</a>";
                            
                            
                                echo "</td>";
                                echo "</tr>";
                                
                            }
                        } else {
                            echo "<tr><td colspan='3'>No se encontraron registros</td></tr>";
                        }
                    ?>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nombre']; ?></td>
                                <td><?php echo $row['genero']; ?></td>
                                <td><?php echo $row['edad']; ?></td>
                                <td><?php echo $row['dui']; ?></td>
                                <td><?php echo $row['telefono']; ?></td>
                                <td><?php echo $row['correo']; ?></td>
                                <td><?php echo $row['direccion']; ?></td>
                                <td><?php echo $row['ocupacion']; ?></td>
                                <td class="actions">
                                    <a href="#" class="edit-btn" onclick="openModal(<?php echo $row['id']; ?>, '<?php echo $row['nombre']; ?>', '<?php echo $row['genero']; ?>', '<?php echo $row['edad']; ?>', '<?php echo $row['dui']; ?>', '<?php echo $row['telefono']; ?>', '<?php echo $row['correo']; ?>', '<?php echo $row['direccion']; ?>', '<?php echo $row['ocupacion']; ?>')">Editar</a>
                                    <a href="vista_adoptantes.php?action=delete&id=<?php echo urlencode($row['id']); ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">No se encontraron registros.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Contenedor para los botones en la parte superior -->
        <div class="top-buttons">
            <a href="registro_adoptantes.html" class="center-button">Nuevo</a>
            <a href="main.html" class="center-button">Regresar</a>
        </div>

        <!-- Modal para editar un adoptante -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Editar Adoptante</h2>
                <form id="editForm">
                    <input type="hidden" name="id" id="editId">

                    <div class="form-group">
                        <h3>Nombre:</h3>
                        <label for="editNombre">Nombre:</label>
                        <input type="text" id="editNombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <h3>Genero:</h3>
                        <label for="editGenero">Genero:</label>
                        <input type="text" name="genero" id="editGenero" required>
                    </div>

                    <div class="form-group">
                        <h3>Edad:</h3>
                        <label for="editEdad">Edad:</label>
                        <input type="number" name="edad" id="editEdad" required>
                    </div>

                    <div class="form-group">
                        <h3>DUI:</h3>
                        <label for="editDUI">DUI:</label>
                        <input type="number" name="dui" id="editDUI" required>
                    </div>

                    <div class="form-group">
                        <h3>Telefono:</h3>
                        <label for="editTelefono">Telefono:</label>
                        <input type="number" name="telefono" id="editTelefono" required>
                    </div>

                    <div class="form-group">
                        <h3>Correo:</h3>
                        <label for="editCorreo">Correo:</label>
                        <input type="email" name="correo" id="editCorreo" required>
                    </div>

                    <div class="form-group">
                        <h3>Direccion:</h3>
                        <label for="editDireccion">Direccion:</label>
                        <input type="text" name="direccion" id="editDireccion" required>
                    </div>

                    <div class="form-group">
                        <h3>Ocupacion:</h3>
                        <label for="editOcupacion">Ocupacion:</label>
                        <input type="text" name="ocupacion" id="editOcupacion" required>
                    </div>
                    <input type="button" id="saveChanges" value="Actualizar Cambios">
                </form>
            </div>
        </div>

        <!-- Script para manejar el modal -->
        <script>
    function openModal(id, nombre, genero, edad, dui, telefono, correo, direccion, ocupacion) {
        document.getElementById('editId').value = id;
        document.getElementById('editNombre').value = nombre;
        document.getElementById('editGenero').value = genero;
        document.getElementById('editEdad').value = edad;
        document.getElementById('editDUI').value = dui;
        document.getElementById('editTelefono').value = telefono;
        document.getElementById('editCorreo').value = correo;
        document.getElementById('editDireccion').value = direccion;
        document.getElementById('editOcupacion').value = ocupacion;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function saveChanges() {
        var form = document.getElementById('editForm');
        var formData = new FormData(form);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_adoptante.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert('Cambios guardados con éxito');
                closeModal();
                location.reload();
            } else {
                alert('Error al guardar los cambios');
            }
        };
        xhr.send(formData);
    }

    // Añadir el manejador de eventos para el botón "Actualizar Cambios"
    document.getElementById('saveChanges').addEventListener('click', saveChanges);
</script>

<!-- Incluye SweetAlert2 desde un CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Función para confirmar eliminación con SweetAlert2
    function confirmarEliminacion(event, element) {
        // Prevenir la acción predeterminada del enlace (evitar que se haga clic inmediatamente)
        event.preventDefault();

        // Usamos SweetAlert2 para mostrar una confirmación personalizada
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Este registro será eliminado permanentemente!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true // Coloca los botones en orden inverso
        }).then((result) => {
            // Si el usuario confirma, redirigimos a la URL de eliminación
            if (result.isConfirmed) {
                window.location.href = element.href; // Redirige a la URL de eliminación
            }
        });
    }
</script>


    </div>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>

