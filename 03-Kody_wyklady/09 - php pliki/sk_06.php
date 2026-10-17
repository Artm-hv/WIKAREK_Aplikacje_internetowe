<?php
 if ($dir = @opendir("./"))
 {
  while($file = readdir($dir))  
  {echo $file,"<BR>"; }
  closedir($dir);
 }
?>
