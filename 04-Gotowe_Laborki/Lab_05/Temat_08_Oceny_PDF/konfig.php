<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "student"; // lub puste w zaleznosci od konfiguracji WAMPa/XAMPPa na VM
$db_name = "lab5_t8_oceny";

$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    // Proba z pustym haslem
    $conn = @new mysqli($db_host, $db_user, "", $db_name);
}
?>