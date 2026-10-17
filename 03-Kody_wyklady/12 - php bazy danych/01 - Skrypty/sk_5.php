<?php
 $link = mysqli_connect("localhost","root","student" );
  mysqli_select_db ($link,"test");
  $sql1="delete from oceny where nr_albumu= '".$_POST['kas']."'";
  $zaw1=mysqli_query($link,$sql1);
  include("sk_4.php");
?>
