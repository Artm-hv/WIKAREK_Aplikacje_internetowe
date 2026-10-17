<?php
   $p = new mysqli('localhost','root','student','test');
   if($p->connect_errno){
      echo $p->connect_error;
    } 
   else 
    echo "Polczenie zestawione";
   $sql1="delete from oceny where nr_albumu= '".$_POST['kas']."'";
   $p->query($sql1);
   include("Mysql_1.php");
?>
