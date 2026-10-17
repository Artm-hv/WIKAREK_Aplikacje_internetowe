<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$n1 = rand(2, 9);
$n2 = rand(1, 9);
$code = (string)($n1 + $n2);
$_SESSION['captcha_code'] = $code;

$img = imagecreatetruecolor(120, 38);
$bg = imagecolorallocate($img, 240, 243, 246);
$text_color = imagecolorallocate($img, 44, 62, 80);
$line_color = imagecolorallocate($img, 189, 195, 199);

imagefill($img, 0, 0, $bg);
for($i=0; $i<4; $i++) {
    imageline($img, rand(0,120), rand(0,38), rand(0,120), rand(0,38), $line_color);
}
$text = "$n1 + $n2 = ?";
imagestring($img, 5, 22, 11, $text, $text_color);

header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');
imagepng($img);
imagedestroy($img);
