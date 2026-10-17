<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "student";
$db_name = "lab5_t2_sonda";

$conn = @new mysqli($db_host, $db_user, $db_pass);
if ($conn->connect_error) $conn = @new mysqli($db_host, $db_user, "");

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->query("DROP DATABASE IF EXISTS `$db_name`");
    $conn->query("CREATE DATABASE `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci");
    $conn->select_db($db_name);
    
    $conn->query("CREATE TABLE pozycje (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tytul VARCHAR(100) NOT NULL,
        opis TEXT NOT NULL,
        glosy INT DEFAULT 0
    )");
    
    $conn->query("CREATE TABLE glosujacy_ip (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pozycja_id INT NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        data_glosu DATETIME NOT NULL
    )");
    
    // Dane startowe
    $conn->query("INSERT INTO pozycje (tytul, opis, glosy) VALUES 
        ('Projekt 1: Architektura Cloud', 'Zaawansowany system chmurowy w technologii mikroserwisow.', 5),
        ('Projekt 2: Bezpieczenstwo API', 'Modul autoryzacji bazujacy na tokenach i szyfrowaniu.', 12),
        ('Projekt 3: Interfejs UI/UX', 'Nowoczesny frontend bez uzycia tabel z pelna responsywnoscia.', 8)");
        
    $msg = "Baza danych i tabele dla Tematu 1 (Sonda internetowa) zostały pomyślnie utworzone!";
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Instalacja - Temat 1</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header><h1>Instalator Systemu Sonda internetowa (Temat 1)</h1><nav><a href="index.php">Strona Główna</a><a href="admin.php">Panel Admina</a></nav></header>
<?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
<form method="post"><p>Kliknij poniższy przycisk, aby zainstalować bazę danych i utworzyć strukturę tabel.</p><br>
<button type="submit" class="btn btn-success">Zainstaluj / Zresetuj Bazę Danych</button></form></div></body></html>
