<?php
 class koszyk
 {
	 
  static public $telefon = "4323425";
  static public function wyswietl_t($napis)
  { echo "<BR>Numer telefonu:".$napis."<BR><BR>";  }


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
 function __clone()
  { 
    echo "Klonnowanie obiektu <BR>";
  }
 };
 koszyk::wyswietl_t(koszyk::$telefon);

?>

