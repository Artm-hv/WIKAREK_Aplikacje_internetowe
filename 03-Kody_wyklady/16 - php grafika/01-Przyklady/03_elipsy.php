<?php
header("Content-type: image/gif");
$obrazek = imagecreate(100,100);
imagecolorallocate($obrazek,230,230,230);
$kolor_blekit=imagecolorallocate($obrazek,0,255,255);
$kolor_fiolet=imagecolorallocate($obrazek,255,0,255);
for($i=0;$i<5;$i++){
 imageellipse($obrazek,rand()%100, rand()%100,rand()%50, rand()%50,$kolor_blekit); 
 imagefilledellipse($obrazek,rand()%100, rand()%100, rand()%50, rand()%50,$kolor_fiolet);	
}
//imagegif($obrazek);
?>