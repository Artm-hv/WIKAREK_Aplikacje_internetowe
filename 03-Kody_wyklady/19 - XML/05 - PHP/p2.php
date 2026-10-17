<?php
  $olimpiady = simplexml_load_file('ns.xml');
  foreach($olimpiady->Uczestnicy->Panstwo as $pan)   
  {echo $pan['nazwa'].":&#8194;" ;
   foreach($pan as $zaw)
   {echo $zaw->Nazwisko."&#8194;";}   
    echo "<BR/>";
  }    
?>
