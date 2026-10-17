<?php
 echo "Ustawiam ciasteczka";
 setcookie ("cookie[three]", "ADAM",time()+3600,'','localhost');
 setcookie ("cookie[two]", "KOT",time()+3600,'','localhost');
 setcookie ("cookie[one]", "PIES",time()+3600,'','localhost');
 echo '<br /><a href="sk_4a.php">Kolejna strona</a>';
?>
