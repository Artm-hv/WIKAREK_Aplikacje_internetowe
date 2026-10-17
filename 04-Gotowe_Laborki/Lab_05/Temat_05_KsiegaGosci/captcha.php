<?php
session_start();
$code = (string)rand(1000, 9999);
$_SESSION['captcha_code'] = $code;

header("Content-Type: image/png");
$im = imagecreate(100, 36);
$bg = imagecolorallocate($im, 240, 240, 240);
$fg = imagecolorallocate($im, 20, 20, 20);
$noise = imagecolorallocate($im, 180, 180, 180);

for ($i = 0; $i < 5; $i++) {
    imageline($im, rand(0, 100), rand(0, 36), rand(0, 100), rand(0, 36), $noise);
}
imagestring($im, 5, 28, 10, $code, $fg);
imagepng($im);
imagedestroy($im);
?>