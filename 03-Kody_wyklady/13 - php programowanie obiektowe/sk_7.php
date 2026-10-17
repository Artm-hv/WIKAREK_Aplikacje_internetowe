<?php
 class koszyk
 {
  function __call($nazwa, $parametry)
   {
    echo "wywołałeś nieistniejącą metodę: $nazwa <BR>";
    echo "podajac parametry: <BR>";
    var_dump($parametry);
    echo "<BR>";;
  }
  function __set($nazwa, $wartosc)
  {echo "BRAK ZMIENNEJ ODCZYT:$nazwa <BR>";}
	
  function __get($nazwa)
   {echo "BRAK ZMIENNEJ ZAPIS: $nazwa <BR>"; }
 };
 
 $c=1;
 $zakupy = new koszyk;
 $zakupy->metoda_a("1",$c);
 $zakupy->t1="tekst";
 $w=$zakupy->t2; 
?>

