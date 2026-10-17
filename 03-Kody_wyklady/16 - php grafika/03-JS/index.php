<HTML>
 <?php
 echo ' <HEAD>
          <meta http-equiv="content-type" content="text/html; charset=iso-8859-2">
          <TITLE>Wykres na stronie</TITLE>
        </HEAD>
        <BODY> ';
 include("konfig.php");
 $link=mysqli_connect($h,$u,$p) or die ("Brak polaczenia do bazy <br>");
 print ("<b>Baza danych OK</b><br><br>");
 mysqli_select_db($link, $b) or die ("Brak instancji <br>");
 $sql1='select * from `'.$t.'` ';
 $que1=mysqli_query($link, $sql1);

 /*DODANE*/
 $Tab_wy[]=['x','y'];
 while ($row1=mysqli_fetch_row($que1))
 {
  $a1 = (int) $row1[0];
  $a2 = (int) $row1[1];
  $Tab_wy[]=[$a1,$a2];
 };
 echo "<b>Rysuj wykres ...</b><br><br>
       <form action='wykres.php' method='post'>
	<input type='hidden' value='".json_encode($Tab_wy)."' name='dane_all'/>
	<input type='submit' value='Rysuj'/>
       </form>";
  mysqli_close($link);
 echo '</BODY> 
       </HTML>';
?>
