<?php
$u=$_POST['user'];
$p=$_POST['pass'];
$h=$_POST['host']='poczta.student.pl';
  $end='
';

if (!isset($_GET['send'])) 
 {$ster='0';}
else 
 {$ster='1';};

if($ster=='0')
 {
  echo "
  <head>
  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-2'>
  </head>
  <body>
  <form enctype='multipart/form-data' action='sk_2.php?send=1' method='POST'>
   <table align=center>
    <tr>
     <td>odbiorca</td>
     <td><input type='tekst' name='odbiorca'/></td>
    </tr>
    <tr>
     <td>temat</td>
      <td><input type='tekst' name='subject'/></td>
    </tr>
    <tr>
     <td>wiadomosc</td>
     <td><textarea name='wiadomosc' rows='10' cols='60'></textarea></td>
    </tr>
    <tr>
     <td>zalaczniki</td>
     <td><input type='file' size='32' name='plik_upload' value=''>
      <input type='hidden' name='nad' value='".$u."@".$h."'/>
      <input type='hidden' name='host' value='".$h."'/>
      <input type='hidden' name='user' value='".$u."'/>
      <input type='hidden' name='pass' value='".$p."'/></td>
   </tr>
   <tr>
    <td colspan='2' align=center><input type='submit' name='wyslij' value='wyslij'/></td>
   </tr>
  </table>
 </form>
 </body>";
}

if($ster=='1'){
  $user64=base64_encode($u);
  $pass64=base64_encode($p);
  if (! $id=fsockopen($h,'25',$err,$err_stri,30))
   {echo 'BLAD POLACZENIA';
    exit(1);
   }
 $linia='helo a'.$end;
 fwrite($id,$linia);
 $tekst=fgets($id);
 $na=(string) substr(fgets($id),0,3);
 if ($na != '250')
 {
  echo 'BŁAD POWITANIA';
  exit(2);
 }
 $linia='auth login '.$end;
 fwrite($id,$linia);
 $tekst=fgets($id);
 $linia=$user64.$end;
 fwrite($id,$linia);
 $tekst=fgets($id);
 $linia=$pass64.$end;
 fwrite($id,$linia);
 $tekst=fgets($id);
 $na=(string) substr($tekst,0,3);
 if ($na != '235')
 {
  echo 'BŁAD AUTORYZACJI';
  exit(2);
 }
 $linia="mail from: <".$u."@".$h.">.$end";
  fwrite($id,$linia);
 $tekst=fgets($id);
 $linia="rcpt to: <".$_POST['odbiorca'].">.$end";
 fwrite($id,$linia);
 $tekst=fgets($id); 
 $linia="data.$end";
 fwrite($id,$linia);
 $tekst=fgets($id); 
  ## FORMATOWANIE WIADOMOSCI ##
    $tab=$_FILES['plik_upload'];
    $nazwa_pliku=$tab['name']; 
    $plik_t=file_get_contents($tab['tmp_name']);
    $tresc_pliku=base64_encode($plik_t);
    $typ=$tab['type'];
    $boundary = "----".strtoupper(md5(uniqid(time())));
 
    $nadawca=$_POST['user']."@".$h;
    $nadawca=$_POST['user']."@poczta.student.pl";

    $linia = "From: <".$nadawca.">.$end";
    $linia .= "To: <".$_POST['odbiorca'].">.$end";
    $linia .= "Subject:".$_POST['subject']."$end";   
    $linia .= "Message-ID: <".strtoupper(md5(uniqid(time())))."@poczta.student.pl>$end";   
    $linia .= "User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:68.0) Gecko/20100101 Thunderbird/68.2.1".$end;
    $linia .= "MIME-Version: 1.0".$end;
    $linia .= "Content-Type: multipart/mixed; ";
    $linia .= ' boundary="'.$boundary.'"'.$end;
    $linia .= "Content-Language: pl".$end;
    $linia .= "Return-Path: ".$nadawca.$end.$end;
    fwrite($id,$linia);
    
    $linia  = "This is a multi-part message in MIME format.".$end.$end;
    $linia .= "--".$boundary.$end;
    $linia .= "Content-Type: text/plain; charset=utf-8; format=flowed".$end;
    $linia .= "Content-Transfer-Encoding: 7bit".$end;
    $linia .= $end;
    fwrite($id,$linia);


    $linia  = $_POST['wiadomosc'];
    $linia .= $end;
    $linia .= $end;
    $linia .= "--".$boundary.$end;
    fwrite($id,$linia);

    $linia  = "Content-Type: ".$typ.";".$end;
    $linia .= " name=\"".$nazwa_pliku."\"".$end;
    $linia .= "Content-Transfer-Encoding: base64".$end;
    $linia .= "Content-Disposition: attachment;".$end;
    $linia .= " filename=\"".$nazwa_pliku."\"".$end.$end;
    fwrite($id,$linia);
    
    fwrite($id, $tresc_pliku);
    $linia =  $end;
    $linia .= "--".$boundary."--".$end;
    fwrite($id,$linia);
    $linia = ".".$end;
    fwrite($id,$linia);
    $tekst=fgets($id); 
    $linia="quit".$end;
 fwrite($id,$linia);
 $tekst=fgets($id);
 fclose($id);
  echo "<p align= center><a href='sk_1.html'>POWROT</a></p>";
}
?>