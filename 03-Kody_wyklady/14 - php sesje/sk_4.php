 <?php
  if (isset ($cookie)) 
  {
   while (list($name, $value)=each($cookie))
    { echo "$name == $value <br>"; }
  }
?>
