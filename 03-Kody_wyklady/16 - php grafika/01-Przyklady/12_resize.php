<?php
header("Content-type: image/gif");
$obrazek1=imagecreatefromjpeg("duze.jpg");
$szerokosc=imagesx($obrazek1);
$wysokosc=imagesy($obrazek1);
$obrazek2=imagecreate($szerokosc/4,$wysokosc/4);
imagecolorallocate($obrazek2,0,0,0);
imagecopyresized($obrazek2,$obrazek1,0,0,0,0,$szerokosc/4,$wysokosc/4,$szerokosc,$wysokosc);
imagegif($obrazek2);
?>