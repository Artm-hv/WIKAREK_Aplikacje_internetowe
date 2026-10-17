<?php
 if ($dir = @opendir("dane"))
 {
  while($file = readdir($dir))  
  {if  (is_file("dane/".$file))
   {
    readfile("dane/".$file); 
    echo "<BR>------------<BR>";
   };
  };
  closedir($dir);
 }
?>
