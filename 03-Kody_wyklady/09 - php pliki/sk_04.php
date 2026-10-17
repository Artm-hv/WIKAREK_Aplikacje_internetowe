<?php
 define("plik","a1.txt");
 $fp = fopen(plik,"r");
 $stareDane=fread($fp,filesize(plik)); 
 fclose($fp);
 $noweDane="To jest dodatkowa pierwsza linia\n"; 
 $noweDane.=$stareDane;
 $fp = fopen(plik, "w"); 
 fputs($fp,$noweDane); 
 fclose($fp);
?>
