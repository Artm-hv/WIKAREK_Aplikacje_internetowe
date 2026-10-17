<?php
  $tablica["imie"] = "Jan";
  $tablica["nazwisko"] = "Kowalski";
  $tablica["adres"] = "Polna 1"; 
  foreach($tablica as $klucz=>$wartosc )
  {echo "$klucz => $wartosc <BR>";}
  echo "asort <BR>";
  asort($tablica);
  foreach($tablica as $klucz=>$wartosc )
  {echo "$klucz => $wartosc <BR>";}
  echo "arsort <BR>";
  arsort($tablica);
  foreach($tablica as $klucz=>$wartosc )
  {echo "$klucz => $wartosc <BR>";}
  echo "ksort <BR>";
  ksort($tablica);
  foreach($tablica as $klucz=>$wartosc )
  {echo "$klucz => $wartosc <BR>";}
?>
