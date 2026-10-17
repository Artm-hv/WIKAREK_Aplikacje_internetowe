<?php
 $host=trim($_POST['adres']);
 $login=$_POST['logi'];
 $password=$_POST['ha'];
 $fname=$_POST['pli_pob'];
 $fsize=$_POST['roz'];
 header('HTTP/1.1 200 OK'); 
 header('Content-Disposition:' . 'attachment; filename="' . $fname . '"');
 header('Accept-Ranges: bytes'); 
 header('Content-Type: application/x-unknown');
 header('Content-Transfer-Encoding: binary');
 header('Cache-Control: must-revalidate,post-check=0, pre-check=0');
 header('Content-Length: ' . $fsize);
 $uri='ftp://'.$login.':'.$password.'@'.$host.'/'.$fname; 
 echo readfile($uri);
?>