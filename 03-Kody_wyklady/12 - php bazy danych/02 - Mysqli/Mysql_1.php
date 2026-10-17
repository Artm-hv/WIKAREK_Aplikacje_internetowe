<HTML>
 <HEAD>
  <meta http-equiv="content-type" 
    content="text/html; charset=iso-8859-2">
 <TITLE> Obsługa bazy danych MySQL</TITLE>
 </HEAD>
 <BODY>
 <?php
   $p = new mysqli('localhost','root','student','test');
   if($p->connect_errno){ echo $p->connect_error;} 
   else 
    echo "Polczenie zestawione";
   echo "<form action='' method='post'>
	grupy: <select name='gru'>";
   $sq12='select distinct grupa from oceny';
   $zaw2=$p->query($sq12);
   while ($row2 = $zaw2->fetch_row()) {
    echo '<option>'.$row2[0].'</option>'; }
   echo "</select><br>
   <input type='submit' value='wyślij dane'> 
  </form>";

    echo " <TABLE border='1' bgcolor=yellow width=400 summary='wyniki zaliczenia'  frame=border>
     <TR>
      <TH>Nr_albumu</TH>
      <TH>Nazwisko</TH>
      <TH>Imie</TH>
      <TH>Grupa</TH>
      <TH>Ocena</TH>
     </TR>";
        
    if ($_SERVER['METHOD_REQUEST']='POST') 
      if (isset($_POST['gru']))
 	$sql1='select * from oceny where grupa="'.$_POST['gru'].'"';
      else 
 	$sql1='select * from oceny ';
    else
      $sql1='select * from oceny ' ; 
 
     $zaw1=$p->query($sql1);
      while ($row1 =$zaw1->fetch_row()) {
       echo" 
       <tr>
        <td> 
         <form action='mysql_2.php' method='post'>
          <input type='submit' value='".$row1[0]."'name='kas'> 
         </form>
        </td>
        <td>".$row1[1]."</td>
        <td>".$row1[2]."</td>
        <td>".$row1[3]."</td>
        <td>".$row1[4]."</td>
      </tr>"; };
     echo " </TABLE> ";
  ?>
 </BODY>
</HTML>
