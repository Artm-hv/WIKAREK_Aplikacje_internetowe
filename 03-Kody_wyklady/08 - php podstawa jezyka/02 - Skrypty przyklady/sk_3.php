<html> 
 <head><title> Test formularza </title></head> </body> 
 <?php
  if ($_SERVER["REQUEST_METHOD"]=="GET")
  {
    echo '<form action=" " method="post">
    Podaj liczbe pol: <input type=text name="ile"/><br> 
   <input type=submit value="Wyslij "/>
   </form>' ;
  }
  else 
  {
  echo '<form action="sk_4.php" method="post">'; 
   for($x=1;$x<=$_POST["ile"];$x++) 
  { echo "Pole o nazwie ".$x." " ; 
     echo '<input type=checkbox name="lista[]" value="'.$x.'"/></BR>';
  };
  echo '<input type=submit value="Wyslij"/></form>';
  }
 ?>
 </body>
</html>
