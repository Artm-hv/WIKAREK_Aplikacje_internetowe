<?php
 define("plik","a1.txt");
 $dane = fread(fopen(plik,"r"), filesize(plik));
 echo $dane;
?> 
