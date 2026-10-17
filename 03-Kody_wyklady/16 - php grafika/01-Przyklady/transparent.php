<?php 
header("Content-type: image/png");
$obrazek = imagecreate(100,100);
$kolor_czarny=imagecolorallocate($obrazek,0,0,0);
$kolor_czerwony=imagecolorallocate($obrazek,255,0,0);	
imagefilledellipse($obrazek,50,50, 90, 90,$kolor_czerwony);	
imagecolortransparent($obrazek, $kolor_czarny);
imagepng($obrazek);
//imagedestroy($obrazek);
?>