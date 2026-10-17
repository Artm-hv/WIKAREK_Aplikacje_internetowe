<?php
 $adres=trim($_POST['adres']);
 $logi=$_POST['logi'];
 $ha=$_POST['ha'];
 $pli_pob=$_POST['pli_pob'];
 if ($ser=ftp_connect($adres,'21'))
 {
  if ($kon=ftp_login($ser,$logi,$ha))
  {
   echo "Pobierasz plik :".$pli_pob; 
   ftp_get($ser,$pli_pob,$pli_pob,FTP_BINARY);
   echo "<A HREF=$pli_pob> Pobierz plik </A>";
   echo "<BR>
    <form action='sk_2a.php' method='post'>
                  <input type='hidden' value='$adres ' name='adres'> 
                   <input type='hidden' value='$logi' name='logi'>
                   <input type='hidden' value='$ha' name='ha'>
 
    <input type='submit' value=' POWROT'>
  </FORM>";
  } else {echo "Blad autoryzacji";}
 } else echo "Nie udalo sie zainicjowac polaczenia";
?>

   