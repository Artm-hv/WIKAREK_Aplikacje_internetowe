<?php
session_start();
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$message = htmlspecialchars($_POST['message']);
$dokogo = "j.wikarek@tu.kielce.pl"; //Do kogo
$kodek = htmlspecialchars($_POST['kod']);
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: <j.wikarek@tu.kielce.pl>' . "\r\n";
$tytul = "Formularz kontaktowy z PSK";
$wiadomosc = "";
$wiadomosc .= "Imię i Nazwisko: " . $name . "\n";
$wiadomosc .= "Email: " . $email . "\n";
$wiadomosc .= "Wiadomość: " . $message . "\n";
if ($kodek==$_SESSION['captcha'])
{
  $sukces = mail($dokogo, $tytul, $wiadomosc, $headers);
  if ($sukces)
  {
   print "<meta http-equiv=\"refresh\" content=\"0;URL=potwierdzenie.html\">";
  }
  else
  {
    print "<meta http-equiv=\"refresh\" content=\"0;URL=error_serwer.html\">";
  }   
}
else
 {
  echo "<meta http-equiv=\"refresh\" content=\"0;URL=error_captcha.html\">";
 }
?>