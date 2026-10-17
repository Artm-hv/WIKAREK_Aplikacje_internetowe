<HTML>
 <HEAD>
  <meta http-equiv="content-type" 
    content="text/html; charset=iso-8859-2">
 <TITLE> Obsługa bazy danych MySQL</TITLE>
 </HEAD>
 <BODY>
 <?php
   $link = mysqli_connect("localhost","root","student") or die ("Nie można się połączyć");
   print ("Połączenie nawiązane");
   mysqli_select_db ($link,"test") 
   or die ("Nie można wybrać bazy danych");
  
    echo " <TABLE border='1' bgcolor=yellow width=400 
       summary='wyniki zaliczenia'  frame=border>
     <TR>
      <TH>Nr_albumu</TH>
      <TH>Nazwisko</TH>
      <TH>Imie</TH>
      <TH>Grupa</TH>
      <TH>Ocena</TH>
     </TR>";
     $sql1='select * from oceny';
     $zaw1=mysqli_query($link,$sql1);
     while ($row1 = mysqli_fetch_row($zaw1)) {
       echo" 
       <tr>
        <td>".$row1[0]."</td>
        <td>".$row1[1]."</td>
        <td>".$row1[2]."</td>
        <td>".$row1[3]."</td>
        <td>".$row1[4]."</td>
      </tr>"; };
     echo " </TABLE> ";
   mysqli_close($link);
  ?>
 </BODY>
</HTML>
