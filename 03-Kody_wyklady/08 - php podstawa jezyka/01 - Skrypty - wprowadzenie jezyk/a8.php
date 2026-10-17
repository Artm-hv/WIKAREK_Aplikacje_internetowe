<?php
 function Test()
 {
  static $a = 0;
  echo $a;
  $a++;
 }
 Test();
 Test();
?>