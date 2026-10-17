<?php
 session_cache_limiter('nocache');
 session_name('TESTOWA3');
 session_set_cookie_params (30);
 session_start();
 if (isset( $_SESSION['UZYTKOWNIK']))
  { echo 'POPRAWNA AUTORYZACJA'; }
 else
  { echo 'STRONA WYGASLA ';}
?>
