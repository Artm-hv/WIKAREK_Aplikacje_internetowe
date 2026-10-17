<?php
if (isset ($_COOKIE)){
 if (isset ($_COOKIE['cookie']))
{ 
   foreach ($_COOKIE['cookie'] as $name=> $value)  
   {echo "$name = $value <br>"; }
 }
}
?>
