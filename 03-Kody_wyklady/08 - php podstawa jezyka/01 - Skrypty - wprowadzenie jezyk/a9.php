<?php
 $a = 4; 
 function Test()
 { 
  global $a;
  echo "wartosc a = $a"; 
 } 
 Test();
?>