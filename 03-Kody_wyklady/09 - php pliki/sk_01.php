<?php 
   $fp = fopen("a1.txt","r");
   echo  "fgets 10 znakow";
   echo "<BR>";
   $tekst1 = fgets($fp, 10);
   var_dump($tekst1);
   echo "<BR>"; echo "<BR>";
   rewind($fp);
   echo  "fgets 70 znakow";
   echo "<BR>";
   $tekst1 = fgets($fp, 70);
   var_dump($tekst1);
   echo "<BR>";echo "<BR>";
   rewind($fp);
   echo  "fread 70 znakow";
   echo "<BR>";
   $tekst1 = fread($fp, 70);
   var_dump($tekst1);
   echo "<BR>";echo "<BR>";
   rewind($fp);
   echo  "Funkcja file";
   echo "<BR>";
   $tekst1 = file("a1.txt");
   var_dump($tekst1);
   echo "<BR>";echo "<BR>";
?>
		