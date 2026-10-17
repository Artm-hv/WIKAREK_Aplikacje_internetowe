<?php
  $tekst = 'Żółty';
  echo $tekst.'<BR>';
  echo 'Funkcja strlen    = '.strlen($tekst).'<BR>'; 
  echo 'Funkcja mb_strlen ='. mb_strlen($tekst, 'UTF-8'); 
?>

