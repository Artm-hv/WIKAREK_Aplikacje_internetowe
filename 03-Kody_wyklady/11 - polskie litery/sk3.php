<?php
  $tekst = 'Ala ma żółtego kota ';
  echo $tekst.'<BR>';
  $tekst1 = substr($tekst,10,5);
  $tekst2 = mb_substr($tekst,10,5,'UTF-8');
  echo "Od 10 znaku substr     = ".$tekst1.'<BR>';
  echo "Od 10 znaku mb_substr  = ".$tekst2.'<BR>';
 
?>

