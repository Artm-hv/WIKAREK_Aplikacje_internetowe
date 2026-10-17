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
                  <A HREF='ftp://".$logi.":".$ha."@".$adres."/".$pli[$x]."'> POBIERZ </A>
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
<form action="al.php" method="post">
 <input type="submit" value=" POWROT "> 
</form>

 
