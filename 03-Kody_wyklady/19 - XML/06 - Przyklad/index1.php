<?php 
echo '<html>
  <head>
   <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
   <title>XML - przyklad</title>
   <link rel="stylesheet" type="text/css" href="style.css" />
  </head>
 <body>
  <div id="header">
   <b>XML PRZYKLAD</b>
  </div>
  <div id="content">
   <h2>Wykaz pracowników na danym stanowisku</h2>';
   if($_SERVER['REQUEST_METHOD'] == 'GET') 
    {echo '<form method="post" action="index1.php">
      <select name = "stanowisko">';
       $baza = simplexml_load_file("Pracownicy.xml");
       foreach($baza->stanowiska->stanowisko as $stanowisko) 
        {echo '<option value="';
         echo $stanowisko['id'];
         echo '">';
         echo $stanowisko->nazwa;
         echo '</option>';
      };
    echo '</select>
    <input type="submit" value="Wybierz">
   </form>';
  }
 else #metoda get 
 { echo' <table> 
 <tr>
 <td>Imie</td> <td>Nazwisko</td>
 <td>Przepracowane lata</td>
 <td>Przepracowane miesiące</td>
 </tr>';
 $baza = simplexml_load_file('Pracownicy.xml');
 foreach($baza->pracowanie->pracuje as $pracuje)
 {if($pracuje['stanowisko'] == $_POST['stanowisko']) 
 {echo '<tr>';
  $na= 'pracownicy/pracownik[@id="'.$pracuje['pracownik'].'"]';
  $pracownik=$baza->xpath($na) ; 
  echo "<td>".$pracownik[0]->imie."</td>";
  echo "<td>".$pracownik[0]->nazwisko."</td>";
  echo "<td>".$pracuje->lata."</td>";
  echo "<td>".$pracuje->miesiace."</td>";
  echo "</tr>";
 };
 };
 echo '</table> <br><br>
 <h4><a href="index1.php">Powrót</a></h4>'; 
 }
 echo '</div>
 </body>
 </html>'
?>