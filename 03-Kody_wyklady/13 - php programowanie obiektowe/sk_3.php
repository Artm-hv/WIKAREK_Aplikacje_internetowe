<?php
 class koszyk
 {
  function __construct() 
   { echo "START ZAKUPY <BR>"; }
  function __destruct() 
   { echo "KONIEC ZAKUPY <BR>";  }
  public function napis($tekst)
   { echo $tekst; }	
 };
 $zakupy = new koszyk;
 $zakupy->napis('JESTEŚ NA ZAKUPACH <BR>');
?>

