<?php
 $dane = [
    'Styczen' => 40,
    'Luty' => 25,
    'Marzec' => 20,
    'Kwiecien' => 15,
    'Maj' => 40,
    'Czerwiec' => 22,
    'Lipiec' => 15,
    'Sierpien' => 10,
    'Wrzesien' => 35,
    'Pazdziernik' => 25,
    'Listopad' => 40,
    'Grudzien' => 15
    ];
 $szer_obrazu = 500;
 $wys_obrazu = 500;
 $srodek_x = 150;
 $srodek_y = 250;
 $sr_kola = 300; 

 $obraz = imagecreatetruecolor($szer_obrazu, $wys_obrazu);
 $tlo = imagecolorallocate($obraz, 255, 255, 255);
 $kolor_ramki = imagecolorallocate($obraz, 200, 200, 200);
 $kolor_tekstu = imagecolorallocate($obraz, 50, 50, 50);
 imagefilledrectangle($obraz, 0, 0, $szer_obrazu, $wys_obrazu, $tlo);
 $paleta = [
    imagecolorallocate($obraz, 100, 100, 255),
    imagecolorallocate($obraz, 0, 0, 0),
    imagecolorallocate($obraz, 255, 0, 0),
    imagecolorallocate($obraz, 0, 255, 0),
	imagecolorallocate($obraz, 0, 0, 255),
	imagecolorallocate($obraz, 255, 255, 0),
	imagecolorallocate($obraz, 0, 255, 255),
	imagecolorallocate($obraz, 255, 0, 255),
	imagecolorallocate($obraz, 255, 165, 0),
	imagecolorallocate($obraz, 128, 0, 128),
	imagecolorallocate($obraz, 128, 128, 128),
	imagecolorallocate($obraz, 165, 42, 42)
    ];

 $suma = array_sum($dane);
 $kat_poczatkowy = 0;
 $i = 0;
 foreach ($dane as $etykieta => $wartosc) {
  $kat_koncowy = round($kat_poczatkowy + ($wartosc / $suma) * 360,0);
  $akt_kolor = $paleta[$i % count($paleta)];
  imagefilledarc($obraz,$srodek_x, $srodek_y,$sr_kola, $sr_kola, 
     $kat_poczatkowy, $kat_koncowy,$akt_kolor,IMG_ARC_PIE);
    
  $legenda_x = 320;
  $legenda_y = 50 + ($i * 35);
  imagefilledrectangle($obraz, $legenda_x, $legenda_y, $legenda_x + 15, $legenda_y + 15, $akt_kolor);
  imagerectangle($obraz, $legenda_x, $legenda_y, $legenda_x + 15, $legenda_y + 15, $kolor_ramki);
  $procent = round(($wartosc / $suma) * 100, 1);
  $tekst = "$etykieta: $wartosc ($procent%)";
  imagestring($obraz, 3, $legenda_x + 25, $legenda_y, $tekst, $kolor_tekstu);
  $kat_poczatkowy = $kat_koncowy;
  $i++;
 }
 header('Content-Type: image/jpg');
 imagejpeg($obraz);
?>