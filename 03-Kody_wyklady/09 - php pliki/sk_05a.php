<?php
 $df="licznik.dat"; 
 if (!file_exists($df)) 
   {$st=0; $fp=fopen($df,"w");}
 else  
   {$roz=filesize($df);$st=1;$fp=fopen($df,"r+");}
 while (!flock($fp,LOCK_EX)) { sleep(2);}
 if ($st==0) {$count=0;} 
 else
   { $count = fgets($fp, $roz+1); 
    rewind($fp);
   };
 $count+=1;
 fputs($fp, $count);
 flock($fp, LOCK_UN);
 fclose($fp);
 echo $count;
?>

