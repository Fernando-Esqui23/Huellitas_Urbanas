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
    $sql = "DELETE FROM donaciones WHERE id = ?";
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
    $sql = "SELECT * FROM donaciones WHERE nombre LIKE ?";
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
    $sql = "SELECT * FROM donaciones";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Donaciones</title>
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
            <h1>Vista de Donaciones</h1>
        </header>

        <div class="search-form">
            <form method="GET" action="vista_donaciones.php">
                <label for="search">Buscar por Nombre:</label>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="submit" value="Buscar">
                <?php if ($search !== ""): ?>
                    <a href="vista_donaciones.php">Mostrar Todos</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="records">
        <!-- <h2>Registros de Donaciones</h2>-->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Direccion</th>
                        <th>Telefono</th>
                        <th>DUI</th>
                        <th>Correo Electrónico</th>
                        <th>Monto</th>
                        <th>Frecuencia de donacion</th>
                        <th>Metodo de pago</th>
                        <th>Fecha de donacion</th>
                        <th>Destino de la donacion</th>
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
            echo "<td>{$row['direccion']}</td>";
            echo "<td>{$row['telefono']}</td>";
            echo "<td>{$row['dui']}</td>";
            echo "<td>{$row['correo_electronico']}</td>";
            echo "<td>{$row['monto']}</td>";
            echo "<td>{$row['frecuencia_donacion']}</td>";
            echo "<td>{$row['metodo_pago']}</td>";
            echo "<td>{$row['fecha_donacion']}</td>";
            echo "<td>{$row['destino_donacion']}</td>";
            echo "<td class='actions'>";

            // Botón para generar PDF (puedes agregar un icono aquí si es necesario)
            echo "<a href='generar_pdf_donaciones.php?id={$row['id']}' title='Generar PDF'><i class='fas fa-file-pdf' style='color: #000000;'></i></a>";

            // Botón de editar con icono
            echo "<a href='#' class='edit-btn' 
                    data-id='{$row['id']}'data-nombre='{$row['nombre']}' data-direccion='{$row['direccion']}' data-telefono='{$row['telefono']}' data-dui='{$row['dui']}'data-correo_electronico='{$row['correo_electronico']}'data-monto='{$row['monto']}'data-frecuencia_donacion='{$row['frecuencia_donacion']}'data-metodo_pago='{$row['metodo_pago']}'data-fecha_donacion='{$row['fecha_donacion']}'data-destino_donacion='{$row['destino_donacion']}'title='Editar'> <i class='fas fa-edit' style='color: #000000;'></i>
                  </a>";

            // Botón de eliminar con icono
            echo "<a href='vista_donaciones.php?action=delete&id=" . urlencode($row['id']) . "' class='delete-btn' onclick=\"return confirmarEliminacion(event, this);\" title='Eliminar'>
                    <i class='fas fa-trash' style='color: #000000;'></i>
                  </a>";

            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No se encontraron registros</td></tr>";
    }
    ?>
</tbody>
            </table>
        </div>
        
        <div class="top-buttons">
            <a href="registro_adoptantes.html" class="center-button">Nuevo</a>
            <a href="#" id="regresarBtn" class="center-button">Regresar</a>
            </div>

    </div>

    <!-- Modal de Edición -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Editar Donación</h2>
            <form id="editForm" method="POST" action="editar_donacion.php">
                <input type="hidden" id="editId" name="id">
                <div class="form-group">
                    <h3>Nombre:</h3>
                    <label for="editNombre">Nombre:</label>
                    <input type="text" id="editNombre" name="nombre">
                </div>
                <div class="form-group">
                    <h3>Direccion:</h3>
                    <label for="editDireccion">Dirección:</label>
                    <input type="text" id="editDireccion" name="direccion">
                </div>
                <div class="form-group">
                    <h3>Telefono:</h3>
                    <label for="editTelefono">Teléfono:</label>
                    <input type="text" id="editTelefono" name="telefono">
                </div>
                <div class="form-group">
                    <h3>DUI:</h3>
                    <label for="editDui">DUI:</label>
                    <input type="text" id="editDui" name="dui">
                </div>
                <div class="form-group">
                    <h3>Correo:</h3>
                    <label for="editCorreoElectronico">Correo Electrónico:</label>
                    <input type="email" id="editCorreoElectronico" name="correo_electronico">
                </div>
                <div class="form-group">
                    <h3>Monto:</h3>
                    <label for="editMonto">Monto:</label>
                    <input type="text" id="editMonto" name="monto">
                </div>
                <div class="form-group">
                    <h3>Frecuencia de donacion:</h3>
                    <label for="editFrecuenciaDonacion">Frecuencia de donación:</label>
                    <input type="text" id="editFrecuenciaDonacion" name="frecuencia_donacion">
                </div>
                <div class="form-group">
                    <h3>Metodo de pago:</h3>
                    <label for="editMetodoPago">Método de pago:</label>
                    <input type="text" id="editMetodoPago" name="metodo_pago">
                </div>
                <div class="form-group">
                    <h3>Fecha de donacion:</h3>
                    <label for="editFechaDonacion">Fecha de donación:</label>
                    <input type="date" id="editFechaDonacion" name="fecha_donacion">
                </div>
                <div class="form-group">
                    <h3>Destino  de la donacion:</h3>
                    <label for="editDestinoDonacion">Destino de la donación:</label>
                    <input type="text" id="editDestinoDonacion" name="destino_donacion">
                </div>
                <input type="button" id="saveChanges" value="Actualizar Cambios">
            </form>
        </div>
    </div>

    <script>
     document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', (event) => {
        event.preventDefault();
        const modal = document.getElementById('editModal');
        document.getElementById('editId').value = button.getAttribute('data-id');
        document.getElementById('editNombre').value = button.getAttribute('data-nombre');
        document.getElementById('editDireccion').value = button.getAttribute('data-direccion');
        document.getElementById('editTelefono').value = button.getAttribute('data-telefono');
        document.getElementById('editDui').value = button.getAttribute('data-dui');
        document.getElementById('editCorreoElectronico').value = button.getAttribute('data-correo_electronico');
        document.getElementById('editMonto').value = button.getAttribute('data-monto');
        document.getElementById('editFrecuenciaDonacion').value = button.getAttribute('data-frecuencia_donacion');
        document.getElementById('editMetodoPago').value = button.getAttribute('data-metodo_pago');
        document.getElementById('editFechaDonacion').value = button.getAttribute('data-fecha_donacion');
        document.getElementById('editDestinoDonacion').value = button.getAttribute('data-destino_donacion');
        modal.style.display = 'block';
    });
});

        function closeModal() {
            var modal = document.getElementById("editModal");
            modal.style.display = "none";
        }

        document.getElementById("saveChanges").onclick = function() {
            var formData = new FormData(document.getElementById("editForm"));
            fetch("update_donacion.php", {
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

        <!-- Incluye SweetAlert2 desde un CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Función para confirmar eliminación con SweetAlert2
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
