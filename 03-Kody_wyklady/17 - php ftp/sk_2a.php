<?php
$adres=trim($_POST['adres']);
$logi=$_POST['logi'];
$ha=$_POST['ha'];
 if ($ser=ftp_connect($adres,'21'))
 {
   echo "PODLACZONY JESTES DO SERWERA :".$adres; 
   if ($kon=ftp_login($ser,$logi,$ha))
   {
     echo " Jako uzytkownik ".$logi; 
     if ( $pli = ftp_nlist($ser,""))
     {
       echo "<table border='10' bgcolor=yellow frame=border>
       <TR>
        <TH>NAZWA PLIKU </TH>
        <TH>ROZMIAR </TH>
        <TH>POBIERZ </TH>
       </TR>";
       for ($x=0;$x<count($pli);$x++)
       {
        $roz=ftp_size($ser,$pli[$x]); 
        echo " <TR>
                <TH>".$pli[$x]."</TH>
                <TH>".$roz."</TH>
                <TH>
                  <form action='sk_3c.php' method='post'>
                   <input type='submit' value='POBIERZ'>
                   <input type='hidden' value='$pli[$x]' name='pli_pob'> 
                   <input type='hidden' value='$adres ' name='adres'> 
                   <input type='hidden' value='$logi' name='logi'>
                   <input type='hidden' value='$ha' name='ha'>
                   <input type='hidden' value='$roz' name='roz'>

                  </form>
                </TH>
              </TR>";
        };
        echo "</TABLE>"; 
        ftp_quit($ser);
     } else  echo "BLAD POBRANIA LISTY PLIKOW"  ;
    }
    else echo "Blad autoryzacji";
   } else {echo "Nie udalo sie polaczyc";};
?>
<form action="sk_1.php" method="post">
 <input type="submit" value=" POWROT "> 
</form>

 
