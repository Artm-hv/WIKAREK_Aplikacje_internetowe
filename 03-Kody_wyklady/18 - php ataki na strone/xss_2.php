<?php
 include "xss_c.php";
 $id_conn = mysqli_connect($host,$user,$haslo);
 mysqli_select_db($id_conn,$baza);
 if($id_conn == false) die( 'BŁĄD dostępu');
 $nick      = $_POST['nick'];
 $komentarz = $_POST['komentarz'];
 $zapytanie = "INSERT INTO wpis VALUES  ('$nick','$komentarz');";
 mysqli_query($id_conn,$zapytanie);
 header('Location:xss_1.php');
?>
