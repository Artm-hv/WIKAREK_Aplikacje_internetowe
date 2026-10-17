<?php
// Dane do wykresu: nazwa => wartość
$dane = [
    'Jabłka' => 40,
    'Banany' => 25,
    'Pomarańcze' => 20,
    'Gruszki' => 15
];

// Ustawienia obrazu
$szer_obrazu = 500;
$wys_obrazu = 300;
$srodek_x = 150;
$srodek_y = 150;
$sr_kola = 200; // średnica koła

$obraz = imagecreatetruecolor($szer_obrazu, $wys_obrazu);

// Definiowanie kolorów
$tlo = imagecolorallocate($obraz, 255, 255, 255);
$kolor_ramki = imagecolorallocate($obraz, 200, 200, 200);
imagefilledrectangle($obraz, 0, 0, $szer_obrazu, $wys_obrazu, $tlo);

// Paleta kolorów dla wycinków
$paleta = [
    imagecolorallocate($obraz, 52, 152, 219),  // Niebieski
    imagecolorallocate($obraz, 46, 204, 113),  // Zielony
    imagecolorallocate($obraz, 241, 196, 15),  // Żółty
    imagecolorallocate($obraz, 231, 76, 60),   // Czerwony
    imagecolorallocate($obraz, 155, 89, 182)   // Fioletowy
];

$suma = array_sum($dane);
$kat_poczatkowy = 0;
$i = 0;
$kolor_tekstu = imagecolorallocate($obraz, 50, 50, 50);

// Rysowanie wykresu kołowego
foreach ($dane as $etykieta => $wartosc) {
    if ($suma <= 0) break;
    $kat_koncowy = $kat_poczatkowy + ($wartosc / $suma) * 360;
    $akt_kolor = $paleta[$i % count($paleta)];
    
    // Wypełnienie wycinka koła
    imagefilledarc(
        $obraz, 
        $srodek_x, $srodek_y, 
        $sr_kola, $sr_kola, 
        $kat_poczatkowy, $kat_koncowy, 
        $akt_kolor, 
        IMG_ARC_PIE
    );
    
    // Rysowanie legendy
    $legenda_x = 320;
    $legenda_y = 50 + ($i * 35);
    
    // Próbka koloru w legendzie (mały kwadracik)
    imagefilledrectangle($obraz, $legenda_x, $legenda_y, $legenda_x + 15, $legenda_y + 15, $akt_kolor);
    imagerectangle($obraz, $legenda_x, $legenda_y, $legenda_x + 15, $legenda_y + 15, $kolor_ramki);
    
    // Tekst legendy (wartość i procent)
    $procent = round(($wartosc / $suma) * 100, 1);
    $tekst = "$etykieta: $wartosc ($procent%)";
    imagestring($obraz, 3, $legenda_x + 25, $legenda_y, $tekst, $kolor_tekstu);
    
    $kat_poczatkowy = $kat_koncowy;
    $i++;
}

// Wyjście nagłówka i obrazu
header('Content-Type: image/png');
imagepng($obraz);
imagedestroy($obraz);
?>