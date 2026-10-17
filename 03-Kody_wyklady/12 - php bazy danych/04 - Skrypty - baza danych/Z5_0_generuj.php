<?php
$y = ' ;
';
$myfile = fopen("z5_0_konfig.php", "w");
$x = "<?php".$y;
fwrite($myfile, $x);
$x = '$user ="'.$_POST["userName"].'"'.$y;
fwrite($myfile, $x);
$x = '$haslo ="'.$_POST["password"].'"'.$y;
fwrite($myfile, $x);
$x = '$host ="'.$_POST["serverName"].'"'.$y;
fwrite($myfile, $x);
$x = '$baza ="'.$_POST["databaseName"].'"'.$y;
fwrite($myfile, $x);
$x = '?>';
fwrite($myfile, $x);
fclose($myfile);
?>