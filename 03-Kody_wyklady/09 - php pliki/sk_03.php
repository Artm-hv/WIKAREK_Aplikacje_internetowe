<?php
 define("plik","a1.txt");
 $fp = fopen(plik, "r+");
 $linia="To jest dodatkowa pierwsza linia\n"; 
 fputs($fp,$linia); 
 fclose($fp);
?>
