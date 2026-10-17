<?php
 define("plik","a1.txt");
 $fp = fopen(plik,"r+");
 $stareDane=fread($fp,filesize(plik)); 
 $noweDane="To jest dodatkowa pierwsza linia\n"; 
 $noweDane.=$stareDane;
 rewind($fp);
 fputs($fp,$noweDane); 
 fclose($fp);
?>
