<html> 
  <head>
    <title> Wyniki </title> 
  </head>
  <body>
   <?php
    echo "Imie:".$_GET['imie']."<br>";
    echo "Nazwisko: $_GET[nazwisko] <br>"; 
    if (isset($_GET['faktura'])) 
      echo "Faktura:  TAK  <br>";
    else
      echo "Faktura:  NIE  <br>";
    echo " Zamowienie:";
    switch($_GET['zamow'])
    { case "kola"  :echo "Coca-cola"; break; 
      case "fanta" :echo "Fanta"; break; 
      case "sprite":echo "Sprite"; break;
    };
  ?>
  </body>
</html>
