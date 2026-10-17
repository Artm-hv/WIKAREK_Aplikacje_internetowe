<?php
 class koszyk
 { 
  function __construct() 
   { echo "START ZAKUPY <BR>"; }
  function __destruct() 
   { echo "KONIEC ZAKUPY <BR>";  }
 };
 $zakupy = new koszyk;
?>

