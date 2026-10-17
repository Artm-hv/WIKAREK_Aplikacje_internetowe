<?php
$conn = @new mysqli("localhost", "root", "student");
if ($conn->connect_error) $conn = @new mysqli("localhost", "root", "");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->query("DROP DATABASE IF EXISTS lab5_t7_logowanie");
    $conn->query("CREATE DATABASE lab5_t7_logowanie CHARACTER SET utf8mb4");
    $conn->select_db("lab5_t7_logowanie");
    $conn->query("CREATE TABLE wpisy (
        id INT AUTO_INCREMENT PRIMARY KEY,
        autor VARCHAR(50) NOT NULL,
        wpis TEXT NOT NULL,
        data_wpisu DATETIME NOT NULL,
        ip VARCHAR(45) NOT NULL
    )");
    $conn->query("CREATE TABLE zablokowane_ip (
        ip VARCHAR(45) PRIMARY KEY,
        powod VARCHAR(100),
        data_blokady DATETIME
    )");
    $conn->query("INSERT INTO wpisy (autor, wpis, data_wpisu, ip) VALUES 
        ('Jan Kowalski', 'Bardzo przydatne zajecia laboratoryjne!', NOW(), '127.0.0.1'),
        ('Anna Nowak', 'Swietna strona i czytelne materialy.', NOW(), '192.168.1.15')");
    $msg = "Baza księgi gości zainstalowana!";
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Instalacja - Księga Gości</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header><h1>Instalator Księgi Gości (Temat 5)</h1><nav><a href="index.php">Księga</a><a href="admin.php">Admin</a></nav></header>
<form method="post"><button type="submit" class="btn btn-success">Zainstaluj Bazę Księgi Gości</button></form></div></body></html>
