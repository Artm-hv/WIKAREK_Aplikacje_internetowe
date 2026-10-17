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
      $this->towary[$towar] = $ilosc;
   }
  function usun_produkt ($towar, $ilosc)
   {	 
    if ($this->towary[$towar] >= $ilosc)
     { $this->towary[$towar] -= $ilosc;
	return true;
     } 
    else { return false;}
   }
  function lista()
  { 
   foreach ($this->towary as $klucz => $wartosc)
    echo "Towaru $klucz masz w koszyku = $wartosc <BR>";
  }
 };
 $zakupy = new koszyk;
 $zakupy->napis('JESTES NA ZAKUPACH<BR>');
 $zakupy->kup('WODA',10);
 $zakupy->kup('WODA',20);
 $zakupy->kup('MLEKO',10);
 $zakupy->usun_produkt('WODA',10);
 $zakupy->lista();


?>

