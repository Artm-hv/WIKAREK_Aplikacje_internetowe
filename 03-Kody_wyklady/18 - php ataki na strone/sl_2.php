<?php
 include "xss_c.php";
 $id_conn = mysqli_connect($host,$user,$haslo);
 mysqli_select_db($id_conn,$baza);
 if($id_conn == false) die( 'BŁĄD dostępu');
  $zapytanie = "SELECT * FROM osoba where login='".$_GET['login']."'and haslo='".$_GET['haslo']."'"; 
  echo $zapytanie;  
 $wynik = mysqli_query($id_conn,$zapytanie);
  echo '<p>Dane osoby</p>';
  echo '<table>';
  echo '<tr><th>Login</th>
	 <th>Haslo<th> </tr>';
  while($row = mysqli_fetch_row($wynik)) 
   {echo '<tr>	<td>'.$row[0].'</td>
          <td>'.$row[1].'</td>  </tr>';
   }
?>
