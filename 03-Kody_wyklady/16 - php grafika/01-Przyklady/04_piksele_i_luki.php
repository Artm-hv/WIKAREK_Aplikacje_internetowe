<?php
header("Content-type: image/gif");
$obrazek = imagecreate(100,100);
imagecolorallocate($obrazek,230,230,230);
$kolor_niebieski=imagecolorallocate($obrazek,0,0,255);
$kolor_czerwony=imagecolorallocate($obrazek,255,0,0);
for($i=0;$i<20;$i++){
 imagesetpixel($obrazek, rand()%100, rand()%100, $kolor_niebieski);
 imagearc($obrazek, rand()%100, rand()%100, rand()%50, rand()%50, rand()%360, rand()%360, $kolor_czerwony);
}
imagegif($obrazek);
//imagedestroy($obrazek);
?>