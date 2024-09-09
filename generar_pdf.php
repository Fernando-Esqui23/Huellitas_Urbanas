<?php
// Iniciar el buffer de salida para evitar problemas con la salida previa
ob_start();

// Incluir la biblioteca FPDF
require('pdf/fpdf.php');

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

// Obtener el ID del registro desde la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Depuración: Mostrar el ID recibido
echo "ID recibido: " . $id . "<br>";

// Crear instancia de FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Agregar un título
$pdf->Cell(0, 10, 'Registro de Adoptante', 0, 1, 'C');

// Agregar encabezados de columnas
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 10, 'Campo', 1);
$pdf->Cell(150, 10, 'Valor', 1);
$pdf->Ln();

// Consultar el registro específico
if ($id > 0) {
    $sql = "SELECT * FROM adoptantes WHERE id = $id";
    $result = $conn->query($sql);

    // Manejar errores en la consulta
    if (!$result) {
        die("Error en la consulta: " . $conn->error);
    }

    // Agregar los datos del registro específico
    $pdf->SetFont('Arial', '', 10);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pdf->Cell(30, 10, 'ID', 1);
        $pdf->Cell(150, 10, $row['id'], 1);
        $pdf->Ln();
        $pdf->Cell(30, 10, 'Nombre', 1);
        $pdf->Cell(150, 10, $row['nombre'], 1);
        $pdf->Ln();
        $pdf->Cell(30, 10, 'Genero', 1);
        $pdf->Cell(150, 10, $row['genero'], 1);
        $pdf->Ln();
        $pdf->Cell(30, 10, 'Edad', 1);
        $pdf->Cell(150, 10, $row['edad'], 1);
        $pdf->Ln();
        $pdf->Cell(30, 10, 'DUI', 1);
        $pdf->Cell(150, 10, $row['dui'], 1);
        $pdf->Ln();
        $pdf->Cell(30, 10, 'Telefono', 1);
        $pdf->Cell(150, 10, $row['telefono'], 1);
        $pdf->Ln();
        $pdf->Cell(30, 10, 'Correo', 1);
        $pdf->Cell(150, 10, $row['correo'], 1);
        $pdf->Ln();
        $pdf->Cell(30, 10, 'Direccion', 1);
        $pdf->Cell(150, 10, $row['direccion'], 1);
        $pdf->Ln();
        $pdf->Cell(30, 10, 'Ocupacion', 1);
        $pdf->Cell(150, 10, $row['ocupacion'], 1);
    } else {
        $pdf->Cell(0, 10, 'Registro no encontrado.', 0, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'No se ha proporcionado un ID válido.', 0, 1, 'C');
}

// Cerrar la conexión
$conn->close();

// Limpiar el buffer de salida antes de enviar el PDF
ob_end_clean();

// Enviar el PDF al navegador
$pdf->Output('I', 'registro_adoptante.pdf');
?>
