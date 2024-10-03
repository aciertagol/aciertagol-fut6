<?php
require('fpdf/fpdf.php');

// Conectar a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fut6_liga', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Consultar datos de la tabla general
$consulta = $pdo->query('SELECT equipos.nombre, tabla_general.* FROM tabla_general JOIN equipos ON tabla_general.equipo_id = equipos.id');
$tabla_general = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();

// Logotipo
$pdf->Image('images/tu_logotipo.png', 10, 6, 30);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Tabla General - Liga de Fut 6 SSA', 0, 1, 'C');
$pdf->Ln(20);

// Encabezados
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Equipo', 1);
$pdf->Cell(25, 10, 'Ganados', 1);
$pdf->Cell(25, 10, 'Empatados', 1);
$pdf->Cell(25, 10, 'Perdidos', 1);
$pdf->Cell(25, 10, 'Goles Favor', 1);
$pdf->Cell(25, 10, 'Goles Contra', 1);
$pdf->Cell(25, 10, 'Puntos', 1);
$pdf->Ln();

// Datos
$pdf->SetFont('Arial', '', 12);
foreach ($tabla_general as $fila) {
    $pdf->Cell(40, 10, utf8_decode($fila['nombre']), 1);
    $pdf->Cell(25, 10, $fila['partidos_ganados'], 1);
    $pdf->Cell(25, 10, $fila['partidos_empatados'], 1);
    $pdf->Cell(25, 10, $fila['partidos_perdidos'], 1);
    $pdf->Cell(25, 10, $fila['goles_favor'], 1);
    $pdf->Cell(25, 10, $fila['goles_contra'], 1);
    $pdf->Cell(25, 10, $fila['puntos'], 1);
    $pdf->Ln();
}

// Output del PDF
$pdf->Output('D', 'Tabla_General_Liga_Fut6_SSA.pdf');
?>