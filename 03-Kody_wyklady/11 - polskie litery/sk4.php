<?php
  $tekst = 'Ala ma Żółtego Kota ';
  echo $tekst.'<BR>';
  $tekst1 = mb_strtoupper($tekst,'UTF-8');
  $tekst2 = mb_strtolower($tekst,'UTF-8');
  echo "Duże litery     = ".$tekst1.'<BR>';
  echo "Małe litery     = ".$tekst2.'<BR>';
?>


