<?php
 class koszyk
 {
  var $towary;
  function __construct() 
   { echo "START ZAKUPY <BR>"; }

  function __destruct() 
   { echo "KONIEC ZAKUPY <BR>";  }

  public function napis($tekst)
   { echo $tekst; }	

  function kup ($towar, $ilosc)
   { if (isset($this->towary[$towar]))
      $this->towary[$towar] += $ilosc;
     else
      $this->towary[$towar] = $ilosc; }

  function usun_produkt ($towar, $ilosc)
   { if ($this->towary[$towar] >= $ilosc) 
     {$this->towary[$towar] -= $ilosc;}
    else 
     {throw new Exception('Zla ilosc towaru');} }

  function lista()
   { foreach ($this->towary as $klucz => $wartosc)
      echo "Towaru $klucz masz w koszyku = $wartosc <BR>";}

  function __clone()
  {echo "Klonowanie obiektu <BR>";}

  function __call($nazwa, $parametry)
   { echo "wywołałeś nieistniejącą metodę: $nazwa <BR>";
     echo "podajac parametry: <BR>";
     var_dump($parametry);
     echo "<BR>";}

  function __set($nazwa, $wartosc)
   {echo "BRAK ZMIENNEJ ODCZYT:$nazwa <BR>";}
	
  function __get($nazwa)
   {echo "BRAK ZMIENNEJ ZAPIS: $nazwa <BR>"; }

  };

?>

