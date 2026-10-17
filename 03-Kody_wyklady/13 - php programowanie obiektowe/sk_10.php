<?php
 require "koszyk/koszyk.php";
 $zakupy = new koszyk;
 $zakupy = unserialize($_POST['za']);
 $zakupy->lista();
?>