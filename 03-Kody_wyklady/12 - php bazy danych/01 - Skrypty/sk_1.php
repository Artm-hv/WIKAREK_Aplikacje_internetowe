<HTML>
 <HEAD>
  <meta http-equiv="content-type" 
    content="text/html; charset=iso-8859-2">
  <TITLE> Obsługa bazy danych MySQL</TITLE>
 </HEAD>
 <BODY>
  <?php
   @$link=mysqli_connect("localhost","root","student")
    or die ("Nie można się połączyć");
   print ("Połączenie nawiązane");
   mysqli_select_db ($link,"test") 
   or die ("Nie można wybrać bazy danych");
   /* INSTRUKCJE OBSŁUGI BAZY */
   mysqli_close($link);
  ?>
 </BODY>
</HTML>
