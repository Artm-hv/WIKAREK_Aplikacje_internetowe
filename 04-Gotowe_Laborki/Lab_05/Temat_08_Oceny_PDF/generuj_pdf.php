<?php
require_once __DIR__ . '/konfig.php';
require_once __DIR__ . '/fpdf/fpdf.php';

$conn = get_db_connection();
$oceny = $conn->query("SELECT * FROM " . TBL_PREFIX . "oceny ORDER BY kod_przedmiotu ASC, nr_albumu ASC");

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-2//TRANSLIT', 'Politechnika Swietokrzyska - Raport Ocen PTI/PAI'), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, iconv('UTF-8', 'ISO-8859-2//TRANSLIT', 'Grupa: 3ID11B | Zespol: Artem Hizhovskyi (s097882), Ihor Namachynskyi (s097889)'), 0, 1, 'C');
        $this->Cell(0, 6, 'Data wygenerowania: ' . date('Y-m-d H:i:s'), 0, 1, 'C');
        $this->Ln(5);
        
        // Nagłówki tabeli
        $this->SetFillColor(52, 152, 219);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(25, 8, 'Kod', 1, 0, 'C', true);
        $this->Cell(65, 8, 'Przedmiot', 1, 0, 'L', true);
        $this->Cell(25, 8, 'Data', 1, 0, 'C', true);
        $this->Cell(22, 8, 'Album', 1, 0, 'C', true);
        $this->Cell(18, 8, 'Ocena', 1, 0, 'C', true);
        $this->Cell(35, 8, 'Status Decyzji', 1, 1, 'C', true);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Strona ' . $this->PageNo() . ' / {nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(40, 40, 40);

while($r = $oceny->fetch_assoc()) {
    $pdf->Cell(25, 7, iconv('UTF-8', 'ISO-8859-2//TRANSLIT', $r['kod_przedmiotu']), 1, 0, 'C');
    $pdf->Cell(65, 7, iconv('UTF-8', 'ISO-8859-2//TRANSLIT', substr($r['nazwa_przedmiotu'], 0, 36)), 1, 0, 'L');
    $pdf->Cell(25, 7, $r['data_zaliczenia'], 1, 0, 'C');
    $pdf->Cell(22, 7, $r['nr_albumu'], 1, 0, 'C');
    $pdf->Cell(18, 7, number_format($r['ocena'], 1), 1, 0, 'C');
    
    $status_str = strtoupper($r['status']);
    $pdf->Cell(35, 7, iconv('UTF-8', 'ISO-8859-2//TRANSLIT', $status_str), 1, 1, 'C');
}

$pdf->Output('I', 'Raport_Ocen_3ID11B.pdf');
