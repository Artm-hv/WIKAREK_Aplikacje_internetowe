<?php  
$link=mysqli_connect("localhost","root","student")
or die ("Nie można się połączyć");
print ("Połączenie nawiązane");
mysqli_close($link);
?>
