<?php
 include "z5_0_konfig.php";
 $id_conn = mysqli_connect($host, $user, $haslo);
 mysqli_select_db($id_conn,$baza);
 if($id_conn == false) die( 'BŁĄD dostępu do bazy danych!');
 $zapytanie = 'SELECT * FROM wiadomosci';
 $wynik = mysqli_query($id_conn,$zapytanie);
 if($wynik == FALSE) echo 'BLĄD! Pobierania treści z bazy!';
 else {
	echo '<p>Dane z tabeli wiadomości</p>';
	echo '<table>';
	echo '<tr>
		<th>ID</th>
		<th>Imię</th>
		<th>Płeć</th>
		<th>Lubi</th>
		<th>Muzyka</th>
		<th>Komentarz</th>
		<th> -usun- </th>
		<th> -popraw- </th>

	       </tr>';
	 while($row = mysqli_fetch_row($wynik)) 
          {	
  		echo '<tr>
			<td>'.$row[0].'</td>
			<td>'.$row[1].'</td>
			<td>'.$row[2].'</td>
			<td>'.$row[3].'</td>
			<td>'.$row[4].'</td>
			<td>'.$row[5].'</td>
			<td><a href="z5_5_usun.php?id='.$row[0].'">Usuń</a></td>
			<td><a href="z5_4_popr.php?id='.$row[0].'">Popraw</a></td>
                      </tr>';		
	  };
	 echo '</table>';
       };
  echo '<td><a href="z5_3_dodaj.php?">Dodaj wpis</a></td>';
  mysqli_close($id_conn);
?>