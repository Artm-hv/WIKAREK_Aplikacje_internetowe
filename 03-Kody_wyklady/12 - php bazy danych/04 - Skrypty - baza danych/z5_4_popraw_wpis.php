<?php
 include "z5_0_konfig.php";
 $id_conn = mysqli_connect($host, $user, $haslo);
 mysqli_select_db($id_conn,$baza);
 if($id_conn == false) die( 'BŁĄD dostępu do bazy danych!');
 $id   = htmlspecialchars($_POST['id']);
 $imie = htmlspecialchars($_POST['imie']);
 $plec = htmlspecialchars($_POST['plec']);
 if (isset($_POST['lubisz1'])) $lubi = htmlspecialchars($_POST['lubisz1']);
 else 
  $lubi ='' ;
 if (isset($_POST['lubisz2'])) $lubi .= ' '.htmlspecialchars($_POST['lubisz2']);
 if (isset($_POST['lubisz3'])) $lubi .= ' '.htmlspecialchars($_POST['lubisz3']);
 $muzyka = htmlspecialchars($_POST['muzyka']);
 $komentarz = htmlspecialchars($_POST['komentarz']);
 $zapytanie = "update wiadomosci set 
			id='$id', 
		        imie='$imie', 
		        plec='$plec', 
		        lubi= '$lubi', 
		        muzyka='$muzyka', 
		        komentarz='$komentarz' 
		where id='$id';";
 echo $zapytanie; 
 mysqli_query($id_conn,$zapytanie);
 /* include "z5_2_przeg.php" */
  header('Location:z5_2_przeg.php');
?>
		