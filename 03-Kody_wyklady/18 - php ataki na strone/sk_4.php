<?php
  session_name('TESTOWA');
  session_start();
  echo 'Witaj na stronie #2<BR>';
  echo "<img src=".$_POST['obr'].">"
?>
		