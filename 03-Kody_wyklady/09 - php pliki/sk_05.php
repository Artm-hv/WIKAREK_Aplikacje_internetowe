<?php
 $df="licznik.dat"; 
 if (!file_exists($df)) {$count=0;} 
 else { 
  $roz=filesize($df);
  $fp=fopen($df, "r");
  while (!flock($fp,LOCK_SH)) { sleep(2);} 
  $count = fgets($fp, $roz+1); 
  fclose($fp);
  };
 $count+=1;
 $fp = fopen($df,"w");
 while (!flock($fp, LOCK_EX)) {sleep(2);}
 fputs($fp, $count);
 flock($fp, LOCK_UN);
 fclose($fp);
 echo $count;
?>

