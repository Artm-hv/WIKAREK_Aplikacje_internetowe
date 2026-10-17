<?php
require_once __DIR__ . '/konfig.php';
$conn = get_db_connection();

$total_res = $conn->query("SELECT lacznie_odwiedzin FROM " . TBL_PREFIX . "licznik_stat WHERE id = 1");
$total = $total_res ? $total_res->fetch_assoc()['lacznie_odwiedzin'] : 0;

$online_res = $conn->query("SELECT COUNT(DISTINCT ip_address) as online FROM " . TBL_PREFIX . "wizyty_ip WHERE ostatnia_aktywnosc > NOW() - INTERVAL " . ONLINE_WINDOW_MIN . " MINUTE");
$online = $online_res ? $online_res->fetch_assoc()['online'] : 1;

$width = 300;
$height = 70;
$img = imagecreatetruecolor($width, $height);

$bg = imagecolorallocate($img, 34, 49, 63);
$border = imagecolorallocate($img, 52, 152, 219);
$text_color = imagecolorallocate($img, 236, 240, 241);
$highlight = imagecolorallocate($img, 46, 204, 113);
$accent = imagecolorallocate($img, 231, 76, 60);

imagefill($img, 0, 0, $bg);
imagerectangle($img, 0, 0, $width - 1, $height - 1, $border);
imagerectangle($img, 1, 1, $width - 2, $height - 2, $border);

$label_total = "LACZNIE ODWIEDZIN: " . str_pad($total, 6, '0', STR_PAD_LEFT);
$label_online = "AKTUALNIE ONLINE: " . $online;

imagestring($img, 5, 20, 16, $label_total, $highlight);
imagestring($img, 4, 20, 40, $label_online, $text_color);

header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');
imagepng($img);
imagedestroy($img);
