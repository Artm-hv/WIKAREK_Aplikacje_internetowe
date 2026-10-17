<?php
include "FtpClient.php";
include "FtpException.php";
include "FtpWrapper.php";
$ftp = new \FtpClient\FtpClient();
$ftp->connect("localhost", true, 21);
$ftp->login("student1", "student1");
$items = $ftp->scanDir();
foreach ($items as $item) {
    echo "Nazwa: " . $item['name'] . "<BR>";
    echo "Typ: " . $item['type'] . "<BR>"; 
    echo "Rozmiar: " . $item['size'] . " bajtów<BR>";
    echo "--------------------------- <BR>";
}
?>