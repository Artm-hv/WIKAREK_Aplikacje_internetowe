<?php
putenv("ORACLE_SID=TEST9");
putenv("ORACLE_HOME=c:/Oracle ");
putenv("TNS_ADMIN=c:/ora/network/admin ");
if(!$conn=@oci_connect("uzytkownik","haslo", "instancja" ) )
{print("SERWIS CHWILOWO NIECZYNNY");}
else 
{ print("Połączono");}
?>
