<?php
require('fpdf/fpdf.php');
include "konfig.php";

$album = isset($_GET['album']) ? trim($_GET['album']) : '';
if (empty($album) || !$conn) die("Brak danych.");

$res = $conn->query("SELECT * FROM oceny WHERE nr_albumu = '" . $conn->real_escape_string($album) . "'");

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'POLITECHNIKA SWIETOKRZYSKA W KIELCACH', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 11);
        $this->Cell(0, 8, 'Wydzial Elektrotechniki, Automatyki i Informatyki', 0, 1, 'C');
        $this->Ln(5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Strona ' . $this->PageNo() . '/{nb} | Wygenerowano przez system PTI', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(0, 10, 'RAPORT AKCEPTACJI OCEN DLA NR ALBUMU: ' . $album, 0, 1, 'L');
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(25, 8, 'Kod', 1, 0, 'C', true);
$pdf->Cell(70, 8, 'Przedmiot', 1, 0, 'L', true);
$pdf->Cell(30, 8, 'Termin', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Ocena', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Decyzja studenta', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
if ($res) {
    while($row = $res->fetch_assoc()) {
        $pdf->Cell(25, 8, $row['kod_przedmiotu'], 1, 0, 'C');
        $pdf->Cell(70, 8, substr($row['nazwa_przedmiotu'], 0, 35), 1, 0, 'L');
        $pdf->Cell(30, 8, $row['termin'], 1, 0, 'C');
        $pdf->Cell(25, 8, $row['ocena'], 1, 0, 'C');
        $pdf->Cell(40, 8, $row['status'], 1, 1, 'C');
    }
}

$pdf->Ln(15);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 8, 'Podpis studenta: ...........................................', 0, 1, 'R');

$pdf->Output('I', 'Raport_Ocen_' . $album . '.pdf');
?>