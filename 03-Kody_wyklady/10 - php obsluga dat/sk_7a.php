<?php
 echo "dziś jest : ".date('Y-m-d')."<BR>";
 $r = (int) date('Y');
 $m = (int) date('m');
 $d = (int) date('d');
 if ($m>5)
  { $termin = mktime(0,0,0,5,1,$r+1);
    $teraz  =time(); 
  }
 if ($m==5 and $d>2)
  { $termin = mktime(0,0,$r+1,5,1,$r+1);
    $teraz  =time(); 
  }
 if ($m==5 and $d==1)
  { $termin = 0;
    $teraz  = 0; 
  }
 if ($m<5 )
  { $termin = mktime(0,0,$r+1,5,1,$r);
    $teraz  =time(); 
  }
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
