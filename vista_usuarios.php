<?php
// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Manejar la eliminación de un registro
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];

    // Preparar y ejecutar la consulta de eliminación
    $sql = "DELETE FROM usuarios WHERE id = ?";
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
    // Consulta sin filtros
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
            background-color: #ADD8E6;
            border-radius: 4px;
        }
        .actions a:hover {
            background-color: #0056b3;
        }
        .actions .delete-btn {
            background-color: #ADD8E6; /* Color rojo para el botón de eliminar */
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
            <h1>Vista de Usuarios</h1>
        </header>


        <!-- Formulario de búsqueda -->
        <div class="search-form">
            <form method="GET" action="vista_usuarios.php">
                <label for="search">Buscar por Nombre:</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="submit" value="Buscar">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

                <?php if ($search !== ""): ?>
                    <a href="vista_usuarios.php">Mostrar Todos</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="records">
           <!-- <h2>Registros de Usuarios</h2>-->
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
                                    <!-- Botón de editar con ícono de Font Awesome -->
                                    <a href="#" class="edit-btn" data-id="<?php echo urlencode($row['id']); ?>" data-nombre="<?php echo urlencode($row['nombre']); ?>" data-correo="<?php echo urlencode($row['correo']); ?>" data-usuario="<?php echo urlencode($row['usuario']); ?>" data-contraseña="<?php echo urlencode($row['contraseña']); ?>">
                                    <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Botón de eliminar con ícono de Font Awesome -->
                                    <a href="vista_usuarios.php?action=delete&id=<?php echo urlencode($row['id']); ?>" class="delete-btn" onclick="return confirmarEliminacion(event, this);">
                                        <i class="fas fa-trash-alt" style="color: #000000; font-size: 18px;"></i>
                                    </a>
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
        
        
        <div class="top-buttons">
            <a href="registro_usuarios.html" class="center-button">Nuevo</a>
            <a href="#" id="regresarBtn" class="center-button">Regresar</a>
        </div>
    </div>

    <!-- Modal de Edición -->
    <!-- Modal de Edición -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Usuario</h2>
        <form id="editForm">
            <input type="hidden" id="editId" name="id">
            <div class="form-group">
                <h3>Nombre:</h3>
                <label for="editNombre">Nombre:</label>
                <input type="text" id="editNombre" name="nombre" required>
            </div>
            <div class="form-group">
                <h3>Correo Electrónico:</h3>
                <label for="editCorreo">Correo Electrónico:</label>
                <input type="email" id="editCorreo" name="correo" required>
            </div>
            <div class="form-group">
                <h3>Usuario:</h3>
                <label for="editUsuario">Usuario:</label>
                <input type="text" id="editUsuario" name="usuario" required>
            </div>
            <div class="form-group">
                <h3>Contraseña:</h3>
                <label for="editContraseña">Contraseña:</label>
                <input type="password" id="editContraseña" name="contraseña" required>
            </div>
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
                var nombre = this.getAttribute("data-nombre");
                var correo = this.getAttribute("data-correo");
                var usuario = this.getAttribute("data-usuario");
                var contraseña = this.getAttribute("data-contraseña");

                document.getElementById("editId").value = id;
                document.getElementById("editNombre").value = nombre;
                document.getElementById("editCorreo").value = correo;
                document.getElementById("editUsuario").value = usuario;
                document.getElementById("editContraseña").value = contraseña;

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
            fetch("update_usuario.php", {
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

    <!-- Incluye SweetAlert2 desde un CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmarEliminacion(event, element) {
        // Prevenir la acción predeterminada del enlace
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

<script>
document.getElementById('regresarBtn').addEventListener('click', function(event) {
    event.preventDefault(); // Evitar la acción predeterminada del enlace
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: "desea regresar al menu principal.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, regresar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'main.html'; // Redireccionar si confirma
        }
    });
});
</script>

</body>
</html>


<?php
// Cerrar la conexión
$conn->close();
?>