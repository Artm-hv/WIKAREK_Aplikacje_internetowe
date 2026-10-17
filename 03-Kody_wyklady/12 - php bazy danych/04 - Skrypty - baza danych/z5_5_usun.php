<?php
 include "z5_0_konfig.php";
 $id_conn = mysqli_connect($host, $user, $haslo);
 mysqli_select_db($id_conn,$baza);
 if($id_conn == false) die( 'BŁĄD dostępu do bazy danych!');
 $id = $_GET['id'];
 $zapytanie = "DELETE FROM wiadomosci where id='$id';";
 mysqli_query($id_conn,$zapytanie);
 include "z5_2_przeg.php";
?>
		