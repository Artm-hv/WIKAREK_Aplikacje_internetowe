<?php
 $termin = mktime(0,0,0,5,1);
 $teraz  = time(); 
 $pozostalo=$termin -$teraz;
 echo "POZOSTALO SEKUND :".$pozostalo; 
 echo"<BR>"; $minut=(int)($pozostalo/(60)); 
 echo "POZOSTALO MINUT :".$minut; echo"<BR>"; 
 $godzin=(int)($minut/60);
 echo "POZOSTALO GODZIN :".$godzin; echo"<BR>";
 $dni=(int)($godzin/(24));
 echo "POZOSTALO DNI :".$dni; 
 echo"<BR>";
?>
