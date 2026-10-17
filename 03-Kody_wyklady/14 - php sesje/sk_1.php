<?php        
session_name('TESTOWA');
session_start();
echo 'Witaj na stronie #1';
$_SESSION['kolor'] = 'zielony';
$_SESSION['zwierze'] = 'kot';
$_SESSION['czas'] = time();
echo '<br /><a href="sk_2.php">strona 2 - bezposrednio</a>';
?>
