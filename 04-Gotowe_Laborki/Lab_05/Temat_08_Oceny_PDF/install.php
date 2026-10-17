<?php
$conn = @new mysqli("localhost", "root", "student");
if ($conn->connect_error) $conn = @new mysqli("localhost", "root", "");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->query("DROP DATABASE IF EXISTS lab5_t8_oceny");
    $conn->query("CREATE DATABASE lab5_t8_oceny CHARACTER SET utf8mb4");
    $conn->select_db("lab5_t8_oceny");
    $conn->query("CREATE TABLE oceny (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kod_przedmiotu VARCHAR(20) NOT NULL,
        nazwa_przedmiotu VARCHAR(100) NOT NULL,
        termin DATE NOT NULL,
        nr_albumu VARCHAR(10) NOT NULL,
        ocena VARCHAR(5) NOT NULL,
        status ENUM('nieprzejrzana', 'zaakceptowana', 'odrzucona') DEFAULT 'nieprzejrzana'
    )");
    // Dane startowe dla albumów autorów
    $conn->query("INSERT INTO oceny (kod_przedmiotu, nazwa_przedmiotu, termin, nr_albumu, ocena, status) VALUES 
        ('PTI', 'Podstawy Technologii Internetowych', '2026-10-15', '97882', '5.0', 'nieprzejrzana'),
        ('AI', 'Aplikacje Internetowe', '2026-10-20', '97882', '4.5', 'nieprzejrzana'),
        ('PTI', 'Podstawy Technologii Internetowych', '2026-10-15', '97889', '5.0', 'nieprzejrzana'),
        ('AI', 'Aplikacje Internetowe', '2026-10-20', '97889', '5.0', 'nieprzejrzana')");
    $msg = "Baza ocen zainstalowana!";
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Instalacja - Oceny</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header><h1>Instalator Systemu Ocen (Temat 8)</h1><nav><a href="index.php">Oceny</a><a href="admin.php">Admin</a></nav></header>
<form method="post"><button type="submit" class="btn btn-success">Zainstaluj Bazę Ocen</button></form></div></body></html>
