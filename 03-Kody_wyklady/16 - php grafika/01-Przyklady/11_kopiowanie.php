<?php 
header("Content-type: image/gif");
$obrazek1=imagecreatefromjpeg("duze.jpg");
$szerokosc=imagesx($obrazek1);
$wysokosc=imagesy($obrazek1);
$obrazek2=imagecreate($szerokosc/2,$wysokosc/2);
imagecolorallocate($obrazek2,0,0,0);
imagecopy($obrazek2,$obrazek1,0,0,0,0,$szerokosc/2,$wysokosc/2);
imagegif($obrazek2);
?>