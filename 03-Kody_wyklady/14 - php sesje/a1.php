<?php 
 session_set_cookie_params (30);
 session_cache_limiter('nocache');
 session_name('TESTOWA3');
 session_start();
 if (!isset($_SESSION['licznik'])) $_SESSION['licznik']=0;
 if ($_SESSION['licznik']>=3 )
      { session_unset();
        header('Location: a3.php');
      }
 if ((isset($_POST['UZYTKOWNIK'])) && (isset($_POST ['HASLO'])))       /* PROCES UWIERZYTELNIANIE DANYCH */
 {	
  if(($_POST['UZYTKOWNIK']=='root1') && ($_POST['HASLO']=='1121')) 
   {
     $_SESSION['UZYTKOWNIK']= $_POST['UZYTKOWNIK']; 
     header('Location: a2.php');
   }
   else
   { $_SESSION['licznik']++;}
 }
 else
 $_SESSION['licznik']++;
 echo 'PROBA LOGOWANIA NA SERWERZE:'.$_SESSION['licznik'];
 echo '<FORM ACTI0N="A1.PHP" METHOD="POST">
 <table>
 <tr>
  <td> LOGIN </TD>
  <td> <INPUT TYPE="TEXT" name="UZYTKOWNIK" value="root1"> </TD> </tr>
 <tr>
  <td> HASLO </td>
  <td> <INPUT TYPE="PASSWORD" name="HASLO" value="1121"> </TD> 
 </tr>
</TABLE>
<INPUT TYPE="SUBMIT" VALUE="WYSLIJ DANE">
</FORM>';
?>