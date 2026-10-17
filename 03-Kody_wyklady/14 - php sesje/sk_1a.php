<?php        
session_name('TESTOWA');
session_start();
echo 'Witaj na stronie #1';
$_SESSION['kolor'] = 'zielony';
$_SESSION['zwierze'] = 'kot';
$_SESSION['czas'] = time();
echo '<br /><a href="sk_2.php?'.SID.'">strona 2  - SID </a>';
echo '<br /><a href="sk_2.php?'.urlencode(session_name()).'='.
  urlencode(session_id()).'">strona 2 - SID NOWY</a> ';
?>
