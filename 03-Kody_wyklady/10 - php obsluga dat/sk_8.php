<?php
$d1 = new DateTime('2024-10-15 14:30:00');
 echo "Data ze stringu  ".  $d1->format('Y-m-d H:i:s');
 echo "<BR>";
$d2 = new DateTime(); 
 echo "Dzisiejsza data ".  $d2->format('Y-m-d H:i:s');
 echo "<BR>";
$d3 = new DateTime('-16 years');
 echo "16 lat temu ".  $d3->format('Y-m-d H:i:s');
 echo "<BR>";
$d3->modify('+1 day');
 echo "Data po modyfikacjach +1 dzien ".  $d3->format('Y-m-d H:i:s');
 echo "<BR>";
$d3->modify('-2 weeks');
 echo "Data po modyfikacjach -2 tygodnie ".  $d3->format('Y-m-d H:i:s');
 echo "<BR>";
$d3->modify('next Monday');
 echo "Data po modyfikacjach nastepny poniedzialek ".  $d3->format('Y-m-d H:i:s');
 echo "<BR>";
$d3->add(new DateInterval('P1DT2H'));   
 echo "Data po modyfikacjach +1 dzien +2 godziny".  $d3->format('Y-m-d H:i:s');
 echo "<BR>";



?>