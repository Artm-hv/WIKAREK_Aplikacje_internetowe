<h2> DZIEKUJE ZA SKORZYSTANIE Z KSIEGI GOSCI <br>
 JESLI CHCESZ DODAC KOLEJNY WPIS	<br>
 <a href="http://localhost/sk_07a.php"> nacisnij tutaj </a>
</h2>
<?php
 if ($_SERVER["REQUEST_METHOD"]=="POST")
 {
  $napis="<tr><td>$_POST[n]</td><td>$_POST[e]</td><td>$_POST[t]</td></tr>"; 
  $file=fopen ("DANE.TXT","a"); 
  fputs($file,$napis); 
  fclose($file);
 }
  echo " <TABLE border='10' bgcolor=yellow width=400 frame=border > 
  <TR>
   <TH> NAZWISKO I IMIE </TH>
   <TH> ADRES POCZTOWY </TH>
   <TH> OPIS	</TH>
  </TR>";
  readfile("DANE.TXT");
 echo " </TABLE>";
?>


