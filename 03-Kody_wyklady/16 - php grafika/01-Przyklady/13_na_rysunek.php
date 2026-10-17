<?php
 header("Content-type: image/png");
 $liczba=$_GET['wizyty'];
 $rozmiar=strlen((string)$liczba);
 $obrazek = imagecreate(25*$rozmiar,50);
 imagecolorallocate($obrazek,230,230,230);
 $liczba=$_GET['wizyty'];
 for ($i=0;$i< $rozmiar;$i++){
  $cyfra=substr($liczba,$i,1);
  $kolor=imagecolorallocate($obrazek,rand()%255, rand()%255, rand()%255);
  $czcionka=rand()%4;
  if ($czcionka==0)$czcionka=__DIR__."/fonts/times.ttf";
  if ($czcionka==1)$czcionka=__DIR__."/fonts/courbi.ttf";
  if ($czcionka==2)$czcionka=__DIR__."/fonts/vivaldi.ttf";
  if ($czcionka==3)$czcionka=__DIR__."/fonts/pola.ttf";
  imagettftext($obrazek, 20+rand()%20,20+rand()%40,5+rand()%10+25*$i,
   30+rand()%15,$kolor,$czcionka,$cyfra);
 } 
 imagepng($obrazek);
 imagepng($obrazek,"wizyta.png");
?>