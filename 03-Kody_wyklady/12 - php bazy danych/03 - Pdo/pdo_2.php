<?php
   $dsn = "mysql:host=localhost;dbname=test";
   $user = "root";
   $passwd = "admin";
   $p = new PDO($dsn, $user, $passwd); 
   $sql1="delete from oceny where nr_albumu= '".$_POST['kas']."'";
   $p->query($sql1);
   include("pdo_1.php");
?>
