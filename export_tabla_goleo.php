<?php
require('fpdf/fpdf.php');

// Conectar a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fut6_liga', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Consultar datos de la tabla de goleo
$consulta = $pdo->query('SELECT jugadores.nombre, equipos.nombre as equipo_nombre, jugadores.goles FROM jugadores JOIN equipos ON jugadores.equipo_id = equipos.id ORDER BY goles DESC');
$tabla_goleo = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();

// Logotipo
$pdf->Image('images/tu_logotipo.png', 10, 6, 30);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Tabla de Goleo - Liga de Fut 6 SSA', 0, 1, 'C');
$pdf->Ln(20);

// Encabezados
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Jugador', 1);
$pdf->Cell(60, 10, 'Equipo', 1);
$pdf->Cell(30, 10, 'Goles', 1);
$pdf->Ln();

// Datos
$pdf->SetFont('Arial', '', 12);
foreach ($tabla_goleo as $fila) {
    $pdf->Cell(60, 10, utf8_decode($fila['nombre']), 1);
    $pdf->Cell(60, 10, utf8_decode($fila['equipo_nombre']), 1);
    $pdf->Cell(30, 10, $fila['goles'], 1);
    $pdf->Ln();
}

// Output del PDF
$pdf->Output('D', 'Tabla_Goleo_Liga_Fut6_SSA.pdf');
?>