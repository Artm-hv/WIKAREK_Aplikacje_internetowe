<?php
	session_name('TESTOWA');
	session_start();
	echo 'Witaj na stronie #2<br/>';
	echo "Kolor ".$_SESSION['kolor']."<BR>"; 
	echo "Zwierze ". $_SESSION['zwierze']."<BR>";
	echo "Data :".date('Y m d H:i:s',$_SESSION['czas'])."<BR>";
?>
