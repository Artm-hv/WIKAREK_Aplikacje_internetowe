<?php
 $tablica["imie"] = "Jan";
 $tablica["nazwisko"] = "Kowalski";
 $tablica["adres"] = "Polna 1";
  while(list($klucz,$wartosc)=each($tablica))
  echo "$klucz => $wartosc <BR>";
?> 