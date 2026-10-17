<?php
header("Content-type: image/png");
$obrazek = imagecreate(100,100);
imagecolorallocate($obrazek,230,230,230);
$kolor_czarny=imagecolorallocate($obrazek,0,0,0);
imagestring($obrazek,1+rand()%5,rand()%60, rand()%90,'Poziomo',$kolor_czarny); 
imagestringup($obrazek,1+rand()%5,rand()%90, rand()%60+30,'Pionowo',$kolor_czarny); 
imagepng($obrazek);
//imagedestroy($obrazek);
?>