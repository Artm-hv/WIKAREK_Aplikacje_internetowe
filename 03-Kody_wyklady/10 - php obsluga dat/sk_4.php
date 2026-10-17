<?php
function getmicrotime()
 {
  list($usec,$sec) = explode(" ", microtime()); 
  return ((float) $usec + (float) $sec);
 };
 $time_start = getmicrotime(); 
 echo "wysietlanie tego napisu trwa"; 
 $time_end = getmicrotime();
 $time = $time_end - $time_start; 
 echo "$time sekund";
?>
