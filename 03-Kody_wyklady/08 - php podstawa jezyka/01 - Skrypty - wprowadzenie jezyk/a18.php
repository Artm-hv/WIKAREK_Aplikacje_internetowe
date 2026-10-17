<HTML>
 <BODY>
  <?php
   $napis="ala ma kota a kot ma ala ona go kocha a on ja w cale";
   $tablica=explode(" ",$napis);
   for($i=0; $i<count($tablica); $i++) 
    echo "Element tablicy o indeksie $i ma
     wartosc $tablica[$i]<BR>";
  ?>
 </BODY>
</HTML>
