<?php
  $olimpiady = simplexml_load_file('ns.xml');
  $ucz=$olimpiady->xpath('Uczestnicy/Panstwo[@nazwa="Polska"]') ;  
  print $ucz[0]->asXML();
?>
