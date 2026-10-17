<?php
$u=$_POST['user'];
$p=$_POST['pass'];
$h=$_POST['host'];

 if (! $id=fsockopen($h,'110',$err,$err_stri,1500))
  {echo 'BLAD POLACZENIA';
  exit(1);
 }
 $end='
';
 $na=fgets($id);
 $linia='user '.$u.$end;
 fwrite($id,$linia);
 $na=fgets($id);
 $linia='pass '.$p.$end;
 fwrite($id,$linia);
 $na=(string) substr(fgets($id),0,3);
 if ($na != '+OK')
 {
  echo 'BŁAD AUTORYZACJI';
  exit(2);
 }

 $linia="stat ".$end;
 fwrite($id,$linia);
 $na=fgets($id);
 $inf=explode(" ",$na);
 echo "<p align=center>Masz: ".$inf[1]. " wiadomosci ktore zajmuja :". $inf[2]." bajtow</p><br/>" ;
 echo "<TABLE border='1' align=center>

 <TR>
 <TH>NADAWCA</TH>
 <TH>ODBIORCA</TH>
 <TH>TEMAT</TH>
 <TH>ROZMIAR</TH>
 <TH>TRESC</TH>
 <TH>zalacznik</TH>
 </TR>";
 for ($x=1 ;$x<=$inf[1]; $x++)
 {
  $linia="list ".$x.".$end";
  fwrite($id,$linia);
  $na=fgets($id);
  $inf1=explode(" ",$na);
  $linia="retr ".$x.".$end";
  fwrite($id,$linia);
  $nap='';
  while(strlen($nap)<$inf1[2]){
   $tmp=fgets($id);
   $nap .= $tmp;
  }

  $zmienna=fgets($id);
  $tresc=explode($end,$nap);
  $kon=0;
  $kon_w=0;
  $nap1='';
  $tem='';
  $nad='';
  $odb='';
  $zal='';
  $granica='x';
   for ($y=0; $y < count($tresc); $y++)
   {
    if ($kon==3) #Naglowek zalacznika
     {
       $po=explode('=',$tresc[$y]);
       if(trim($po[0])=='name') $zal=$po[1];
     };
    if ($kon==2) #Naglowek tresci
     {
      if ((strlen($tresc[$y])==0)) $kon_w=1;
      if (trim($tresc[$y])==trim($granica)) {$kon=3;};

      if (($kon_w==1) and ($kon==2)) {$nap1=$nap1.$tresc[$y];};
     }
    if ($kon==0) #Naglowek ogolny
    { 
     $linia=explode(':',$tresc[$y]);
     if ($linia[0]=='Subject') {$tem=$linia[1];}
     if ($linia[0]=='From') {$nad=$linia[1]; $nad=str_replace('<',' ',$nad); $nad=str_replace('>',' ',$nad);};
     if ($linia[0]=='To') {$odb=$linia[1];   $odb=str_replace('<',' ',$odb); $odb=str_replace('>',' ',$odb);}
     if ($linia[0]=='Content-Type') {$gr=explode('"',$linia[1]); if (isset($gr[1])) $granica ="--".$gr[1];};
     if ((strlen($linia[0])==0) and ($granica=='x')) $kon=1; #Jednoczesciowa wiadomosc
     if (trim($tresc[$y])==trim($granica))
       $kon=2; #Koniec nagłowka ogólnego
    };
   }
  echo "<TR>
  <TH>".$nad."</TH>
  <TH>".$odb."</TH>
  <TH>".$tem." </TH>
  <TH>".$inf1[2]."</TH>
  <TH> <Textarea>".$nap1."</TEXTAREA> </TH>
  <TH>".$zal."</TH>
  </TR>";
 };
 echo "</TABLE>";
?>
