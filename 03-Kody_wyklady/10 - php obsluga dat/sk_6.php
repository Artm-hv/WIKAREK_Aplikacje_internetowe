<?php
 $d=localtime(time(),false);
 foreach($d as $klucz=>$wartosc ){
 echo " $klucz = $wartosc <BR>";};
 $d=localtime(time(),true);
 foreach($d as $klucz=>$wartosc ){
 echo " $klucz = $wartosc <BR>";};
?>
