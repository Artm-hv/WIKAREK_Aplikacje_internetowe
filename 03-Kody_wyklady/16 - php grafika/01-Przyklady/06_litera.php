<?php
header("Content-type: image/png");
$obrazek = imagecreate(100,100);
imagecolorallocate($obrazek,230,230,230);
$kolor_czarny=imagecolorallocate($obrazek,0,0,0);
imagechar($obrazek,1+rand()%5,rand()%90, rand()%90,'A',$kolor_czarny); 
imagecharup($obrazek,1+rand()%5,rand()%90, rand()%90,'B',$kolor_czarny); 
imagepng($obrazek);
//imagedestroy($obrazek);
?>