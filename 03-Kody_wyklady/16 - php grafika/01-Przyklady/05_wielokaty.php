<?php
header("Content-type: image/gif");
$obrazek = imagecreate(100,100);
imagecolorallocate($obrazek,230,230,230);
$kolor_granat=imagecolorallocate($obrazek,200,100,200);
$kolor_oliwka=imagecolorallocate($obrazek,200,200,100);
$wartosci1=[ 
   rand()%100,rand()%100, 
   rand()%100,rand()%100, 
   rand()%100,rand()%100, 
   rand()%100,rand()%100 
];
$wartosci2=array( 
   rand()%100,rand()%100, 
   rand()%100,rand()%100, 
   rand()%100,rand()%100, 
   rand()%100,rand()%100 
);
imagepolygon($obrazek,$wartosci1,$kolor_granat);
imagefilledpolygon($obrazek,$wartosci2,$kolor_oliwka);  
imagegif($obrazek);
//imagedestroy($obrazek);
?>