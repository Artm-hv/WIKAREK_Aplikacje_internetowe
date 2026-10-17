<?php
  $tekst = 'Żółty';
  if (preg_match('/^[a-zA-ZąęćłńóśźżĄĘĆŁŃÓŚŹŻ\s]+$/u', $tekst)) 
	  echo "NAPIS = '".$tekst."' zwiera tylko litery lub spacje";
  else echo "NAPIS = '".$tekst."' zwiera inne  znaki ";
 echo "<BR>"; 
 $tekst = 'Żółty  ala';
  if (preg_match('/^[a-zA-ZąęćłńóśźżĄĘĆŁŃÓŚŹŻ\s]+$/u', $tekst)) 
	  echo "NAPIS = '".$tekst."' zwiera tylko litery lub spacje";
  else echo "NAPIS = '".$tekst."' zwiera inne  znaki ";
 echo "<BR>"; 
 $tekst = 'Żółty  ala 123';
  if (preg_match('/^[a-zA-ZąęćłńóśźżĄĘĆŁŃÓŚŹŻ\s]+$/u', $tekst)) 
	  echo "NAPIS = '".$tekst." 'zwiera tylko litery lub spacje";
  else echo "NAPIS = '".$tekst." 'zwiera inne  znaki ";
 
?>

