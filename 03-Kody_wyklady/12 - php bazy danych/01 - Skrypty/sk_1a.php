<HTML>
 <HEAD>
  <meta http-equiv="content-type" 
    content="text/html; charset=iso-8859-2">
  <TITLE> Obsługa bazy danych MySQL</TITLE>
 </HEAD>
 <BODY>
  <?php
   try 
   {
    $link=mysqli_connect("localhost","root1","student");
    print ("Połączenie nawiązane");
    mysqli_select_db ($link,"test") 
    or die ("Nie można wybrać bazy danych");
    /* INSTRUKCJE OBSŁUGI BAZY */
    mysqli_close($link);
   }
   catch (Exception $e) 
   {
     echo 'Błąd połaczenia z bazą: ',  $e->getMessage(), "\n";
   }
  ?>
 </BODY>
</HTML>
