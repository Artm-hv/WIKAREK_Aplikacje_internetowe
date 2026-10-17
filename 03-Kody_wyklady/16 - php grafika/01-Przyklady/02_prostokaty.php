<?php
header("Content-type: image/gif");
$obrazek = imagecreate(100,100);
imagecolorallocate($obrazek,230,230,230);
$kolor_zielony=imagecolorallocate($obrazek,0,255,0);
$kolor_zolty=imagecolorallocate($obrazek,255,255,0);
for($i=0;$i<5;$i++){
 imagerectangle($obrazek,rand()%100, rand()%100, rand()%100, rand()%100,$kolor_zielony); 
 imagefilledrectangle($obrazek,rand()%100, rand()%100, rand()%100, rand()%100,$kolor_zolty);	
}
imagegif($obrazek);
//imagedestroy($obrazek);
?>