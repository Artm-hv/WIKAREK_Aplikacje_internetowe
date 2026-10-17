<?php
$db_host = "localhost"; $db_user = "root"; $db_pass = "student"; $db_name = "lab5_t4_portal";
$conn = @new mysqli($db_host, $db_user, $db_pass);
if ($conn->connect_error) $conn = @new mysqli($db_host, $db_user, "");
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->query("DROP DATABASE IF EXISTS `$db_name`");
    $conn->query("CREATE DATABASE `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci");
    $conn->select_db($db_name);
    $conn->query("CREATE TABLE menu (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nazwa VARCHAR(50) NOT NULL,
        typ ENUM('html', 'link_wew', 'link_zew') NOT NULL,
        zawartosc TEXT NOT NULL,
        kolejnosc INT DEFAULT 0
    )");
    $conn->query("INSERT INTO menu (nazwa, typ, zawartosc, kolejnosc) VALUES 
        ('Strona Główna', 'html', '<h2>Witamy w naszym portalu!</h2><p>Portal zrealizowany w ramach Laboratorium 5 przez Artem Hizhovskyi i Ihor Namachynskyi (3ID11B).</p>', 1),
        ('Regulamin', 'html', '<h3>Regulamin serwisu</h3><p>Wszystkie tresci sa wlasnoscia autorow.</p>', 2),
        ('Kontakt wewnetrzny', 'link_wew', 'kontakt.html', 3),
        ('Politechnika', 'link_zew', 'https://www.tu.kielce.pl', 4)");
    $msg = "Baza portalu zainstalowana!";
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Instalacja - Portal</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header><h1>Instalator Mini Portalu (Temat 4)</h1><nav><a href="index.php">Portal</a><a href="admin.php">Admin</a></nav></header>
<?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
<form method="post"><button type="submit" class="btn btn-success">Zainstaluj bazę portalu</button></form></div></body></html>
