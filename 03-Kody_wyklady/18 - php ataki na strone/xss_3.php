<?php
 include "xss_c.php";
 $id_conn = mysqli_connect($host,$user,$haslo);
 mysqli_select_db($id_conn,$baza);
 if($id_conn == false) die( 'BŁĄD dostępu');
 $zapytanie = 'SELECT * FROM wpis';
 $wynik = mysqli_query($id_conn,$zapytanie);
 if($wynik == FALSE) echo 'BLĄD! Pobierania!';
 else {
  echo '<p>Komentarze</p>'; 
  echo '<table>';
  echo '<tr> <th>NICK</th> 
        <th>Wpis<th></tr>';
  while($row = mysqli_fetch_row($wynik)) 
  {echo '<tr>	<td>'.$row[0].'</td>
	 <td>'.$row[1].'</td> </tr>';
  }
 }
?>
