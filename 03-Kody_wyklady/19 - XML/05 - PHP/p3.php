<?php
  $olimpiady = simplexml_load_file('ns.xml');
  $ucz=$olimpiady->Uczestnicy ;  
  print $ucz->asXML();
  file_put_contents('plik.txt', $ucz->asXML());
?>
