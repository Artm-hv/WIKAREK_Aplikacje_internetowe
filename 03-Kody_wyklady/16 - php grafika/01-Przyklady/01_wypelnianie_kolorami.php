<?php
header("Content-type: image/gif");
$obrazek = imagecreate(100,100);
imagecolorallocate($obrazek,0,0,0);
$kolor_bialy=imagecolorallocate($obrazek,255,255,255);
for($i=1;$i<10;$i++){
 imageline($obrazek, 10*$i, 0, 10*$i, 100, $kolor_bialy);
 imageline($obrazek, 0, 10*$i, 100, 10*$i, $kolor_bialy);	
}
for($x=0;$x<10;$x++)
  for($y=0;$y<10;$y++){
    $kolor_losowy=imagecolorallocate($obrazek, rand()%256, rand()%256, rand()%256);
    imagefill($obrazek, 5+$x*10, 5+$y*10, $kolor_losowy);
}	
imagegif($obrazek);
//imagedestroy($obrazek);
?>