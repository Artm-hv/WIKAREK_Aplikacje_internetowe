<?php
 echo '<form action="z5_3_dodaj_wpis.php" method="POST">
	Id:<br><input type="text" name="id" size="20" maxlength="20"><br>
	Imię:<br><input type="text" name="imie" size="20" maxlength="20"><br>
        Płeć:<br>
	 <input type="radio" name="plec" value="Kobieta" checked="checked">Kobieta
	 <input type="radio" name="plec" value="Mężczyzna" checked="checked">Mężczyzna
        <br>
        Co lubisz:<br>
	  <input type="checkbox" name="lubisz1" value="imprezy"> imprezy<br>
	  <input type="checkbox" name="lubisz2" value="slodycze"> slodycze<br>
   	  <input type="checkbox" name="lubisz3" value="asg"> asg
      	<br>
	Jakiej muzyki słuchasz:<br>
	  <select name="muzyka" size="1">
		<option> pop </option>
		<option> rock </option>
		<option> metal </option>
		<option> hip-hop </option>
		<option> trance </option>
		<option> house </option>
	   </select>
 	<br>
	Komentarze:<br>
	<textarea name="komentarz" rows="3" cols="30" style="color: gray; "
          >tutaj jest miejsce na twoje swobodne uwagi
	</textarea><br>
	<input type="submit" value="Wyślij">
	<input type="reset"  value="Wyczyść">
       </form>';		
?>
