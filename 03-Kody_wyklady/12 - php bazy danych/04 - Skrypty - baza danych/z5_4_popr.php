<?php
 include "z5_0_konfig.php";
 $id_conn = mysqli_connect($host, $user, $haslo);
 mysqli_select_db($id_conn,$baza);
 if($id_conn == false) die( 'BŁĄD dostępu do bazy danych!');
 $zapytanie = 'SELECT * FROM wiadomosci where id="'.$_GET['id'].'"';
 $wynik = mysqli_query($id_conn,$zapytanie);
 if($wynik == FALSE) echo 'BLĄD! Pobierania treści z bazy!';
 else 
   $row = mysqli_fetch_row($wynik);   
 echo '<form action="z5_4_popraw_wpis.php" method="POST">
	Id:<br><input type="text" name="id1" disabled
		value="'.$row[0].'"
                rsize="20" maxlength="20">
	<input type="text" name="id" hidden
		value="'.$row[0].'"
                rsize="20" maxlength="20"><br>
	Imię:<br><input type="text" name="imie" 
		value="'.$row[1].'"
		size="20" maxlength="20"><br>
        Płeć:<br>';
        if ($row[2]='Kobieta')  echo ' 
	 <input type="radio" name="plec" value="Kobieta" checked="checked">Kobieta
	 <input type="radio" name="plec" value="Mężczyzna" >Mężczyzna
         <br>';
        else 
         echo'
	 <input type="radio" name="plec" value="Kobieta">Kobieta
	 <input type="radio" name="plec" value="Mężczyzna" checked="checked">Mężczyzna
         <br>';
        echo'
        Co lubisz:<br>';   
	$list=explode(" ",$row[3]);
        $i=0;
        $s=0;
        $a=0;   
        for($x=1;$x <count($list); $x++) 
	{
	        if ($list[$x]='imprezy') 	$i=1;
	        if ($list[$x]='slodycze')	$s=1;
	        if ($list[$x]='asg') 		$a=1;

	};
        if ($i==1) echo '<input type="checkbox"  checked="checked" name="lubisz1" value="imprezy"> imprezy<br>';
        else       echo '<input type="checkbox"  name="lubisz1" value="imprezy"> imprezy<br>';
        if ($s==1) echo '<input type="checkbox"  checked="checked" name="lubisz2" value="slodycze"> slodycze<br>';
        else       echo '<input type="checkbox"  name="lubisz2" value="slodycze"> slodycze<br>';
        if ($a==1) echo '<input type="checkbox"  checked="checked" name="lubisz3" value="asg"> asg<br>';
        else       echo '<input type="checkbox"  name="lubisz3" value="asg"> asg<br>';
       
	echo ' 
      	<br>
	Jakiej muzyki słuchasz:<br>
	  <select name="muzyka" size="1">';
         if (trim($row[4])=='pop') echo '<option selected="selected"> pop </option>'; 
          else  echo '<option> pop </option>';
         if (trim($row[4])=='rock') echo '<option selected="selected"> rock </option>'; 
         else  echo '<option> rock </option>';
         if (trim($row[4])=='metal') echo '<option selected="selected"> metal </option>'; 
         else  echo '<option> metal </option>';
         if (trim($row[4])=='hip-hop') echo '<option selected="selected"> hip-hop </option>'; 
         else  echo '<option> hip-hop </option>';
         if (trim($row[4])=='trance') echo '<option selected="selected"> trance </option>'; 
         else  echo '<option> trance </option>';
         if (trim($row[4])=='house') echo '<option selected="selected"> house </option>'; 
         else  echo '<option> house </option>';
        echo'   
	   </select>
 	<br>
	Komentarze:<br>
	<textarea name="komentarz" rows="3" cols="30" style="color: gray; ">'
        .$row[5].'
	</textarea><br>
	<input type="submit" value="Wyślij">
	<input type="reset"  value="Wyczyść">
       </form>';		
?>
