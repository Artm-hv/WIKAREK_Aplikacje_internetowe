<?php
 require 'vendor/autoload.php';
 use PhpOffice\PhpSpreadsheet\Spreadsheet;
 use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

 $spreadsheet = new Spreadsheet();
 $sheet = $spreadsheet->getActiveSheet();
 $sheet->setCellValue('A1', 'Witaj świecie!');

 $writer = new Xlsx($spreadsheet);
 $writer->save('p1.xlsx');
?>