<?php
// Iniciar el buffer de salida
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

// Crear instancia de FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetMargins(10, 10, 10); // Márgenes: izquierda, arriba, derecha

// Agregar un título
$pdf->Cell(0, 10, 'Registro de Adoptante', 0, 1, 'C');
$pdf->Ln(10); // Espacio después del título

// Agregar encabezados de columnas con formato
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalles del Registro', 0, 1, 'L');
$pdf->Ln(5); // Espacio después del encabezado

// Consultar el registro específico
if ($id > 0) {
    $sql = "SELECT * FROM adoptantes WHERE id = $id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Imprimir cada campo del registro en el PDF
        $pdf->SetFont('Arial', 'B', 12);
        $campos = [
            'ID' => 'id',
            'Nombre' => 'nombre',
            'Genero' => 'genero',
            'Edad' => 'edad',
            'DUI' => 'dui',
            'Telefono' => 'telefono',
            'Correo' => 'correo',
            'Direccion' => 'direccion',
            'Ocupacion' => 'ocupacion'
        ];
        
        $pdf->SetFont('Arial', '', 12);
        foreach ($campos as $nombreCampo => $campoDB) {
            $pdf->MultiCell(0, 10, $nombreCampo . ': ' . $row[$campoDB]);
            $pdf->Ln(); // Espacio entre campos
        }
    } else {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Registro no encontrado.', 0, 1, 'C');
    }
} else {
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'No se ha proporcionado un ID válido.', 0, 1, 'C');
}

// Cerrar la conexión
$conn->close();

// Limpiar el buffer de salida antes de enviar el PDF
ob_end_clean();

// Enviar el PDF al navegador
$pdf->Output('I', 'registro_adoptante.pdf');
?>
