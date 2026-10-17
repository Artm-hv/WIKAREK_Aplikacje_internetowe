<?php 
 header("Content-type: image/gif");
 $obrazek = imagecreate(100,100);
 imagecolorallocate($obrazek,100,150,200); 
 imagegif($obrazek);
 //imagedestroy($obrazek); 
?> 
