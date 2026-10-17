<?php
$d=trim($_POST['data']);
echo $d;
echo "<BR>";
var_dump($d);
echo "<BR>";
$r= (int) substr($d,0,4);
echo "Rok $r";
echo "<BR>";
$m= (int) substr($d,5,2);
echo "Miesiac $m";
echo "<BR>";
$dz= (int) substr($d,8,2);
echo "Dzien $dz";
echo "<BR>";
$da=mktime(0,0,0,$m,$dz,$r);
echo "Znacznik $da";
echo "<BR>";
?>
