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
     {$this->towary[$towar] -= $ilosc;}
    else 
     {throw new Exception('Zla ilosc towaru');}  
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
 $zakupy = new koszyk;
 try 
 {
  $zakupy->napis('JESTEŚ NA ZAKUPACH <BR>');
  $zakupy->kup('WODA',10);
  $zakupy->kup('WODA',20);
  $zakupy->kup('MLEKO',10);
  $zakupy->usun_produkt('WODA',40);
  $zakupy->lista();
  $zak->lista();
 }
catch (Exception $x)
 {  
  echo $x->getMessage();
  echo "<BR>";
  echo $x->getCode();
  echo "<BR>";
  echo $x->getfile();
  echo "<BR>";
  echo $x->getline();
  echo "<BR>";
 }

?>

