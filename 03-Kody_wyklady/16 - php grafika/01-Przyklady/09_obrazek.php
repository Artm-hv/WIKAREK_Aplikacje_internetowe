<?php 
header("Content-type: image/jpg");
$obrazek=imagecreatefromjpeg("duze.jpg");
imagepng($obrazek);
//imagedestroy($obrazek);
?>