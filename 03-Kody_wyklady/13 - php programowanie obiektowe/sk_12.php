<?php
 function dlu($napis)
 {
  if (strlen($napis)>20)
  { $wynik=substr($napis,0,20);} 
  else 
  { for ($x=strlen($napis);$x<=20;$x++)
    { $napis = $napis." ";}
    $wynik=$napis;
  };
  return $wynik;
 }

 require('fpdf/fpdf.php');
 $pdf=new FPDF();
 $pdf->AddPage();
 $pdf->SetFont('Arial','B',16) ;
 $pdf->SetAuthor('Wikarek Jarosław');
 $pdf->SetSubject('Wyniki zaliczenia'); 
 $pdf->SetDrawColor(123);
 $pdf->Cell(0,0,'WYNIKI ZALICZENIA STUDENTOW',0,1,'C');
 $pdf->SetFontSize(12);
 $pdf->SetDrawColor(123);
 $pdf->ln(20);
 $pdf->Cell(25,8,dlu('Nr_albumu'),1,0,'L'); 
 $pdf->SetX(35);
 $pdf->Cell(40,8,dlu('Nazwisko'),1,0,'L'); 
 $pdf->SetX(75);
 $pdf->Cell(40,8,dlu('Imie'),'1',0,'L') ;
 $pdf->SetX(115);
 $pdf->Cell(20,8,dlu('Grupa'),'1',0,'L'); 
 $pdf->SetX(135);
 $pdf->Cell(20,8,dlu('Ocena'),'1',1,'L');

 $link=mysqli_connect("localhost", "root","student") ; 
 mysqli_select_db ($link,"test");
 $sqll="select * from oceny"; 
 $zawl=mysqli_query($link,$sqll);
 while ($rowl = mysqli_fetch_row($zawl))
 {
   $pdf->Cell(25,8,dlu($rowl[0]),1,0,'L'); 
   $pdf->SetX(35);
   $pdf->Cell(40,8,dlu($rowl[1]),'1',0,'L');
   $pdf->SetX(75);
   $pdf->Cell(40,8,dlu($rowl[2]),'1',0,'L'); 
   $pdf->SetX(115);
   $pdf->Cell(20,8,dlu($rowl[3]),'1',0,'L');
   $pdf->SetX(135);
   $pdf->Cell(20,8,dlu($rowl[4]),'1',1,'L');
 };
# $pdf->Output(); 
 $pdf->Output("d",'dane.pdf',true); 
 mysqli_close($link);
?>