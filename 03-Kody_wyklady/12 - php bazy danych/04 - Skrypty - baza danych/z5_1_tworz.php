<?php
 include "z5_0_konfig.php";
 $id_conn = mysqli_connect($host, $user, $haslo);
 mysqli_select_db($id_conn,$baza);
 if($id_conn == false) die( 'BŁĄD dostępu do bazy danych!');
 $tabela="CREATE TABLE IF NOT EXISTS wiadomosci
	 ( id int(11) NOT NULL ,
	   imie  varchar(20) NOT NULL,
           plec  text NOT NULL,
           lubi  text NOT NULL,
           muzyka text NOT NULL,
  	   komentarz text NOT NULL)" ;
 mysqli_query($id_conn,$tabela);
?>
