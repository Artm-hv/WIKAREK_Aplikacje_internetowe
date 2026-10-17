<?php        
  session_name('TESTOWA');
  session_start();
  echo 'Witaj na stronie #1';
  echo "<form action='sk_4.php' method='post'>
     URL obrazka:
     <input type='text' name='obr' value='http://localhost' length='50'> <br>
     <input type='submit' value='Prześlij link do obrazka'>
  <form>";
 ?>


