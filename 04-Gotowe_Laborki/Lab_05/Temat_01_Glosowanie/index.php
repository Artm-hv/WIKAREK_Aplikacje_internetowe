<?php
session_start();
include "konfig.php";
$msg = "";
$err = "";

// Obsługa głosu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['glosuj'])) {
    $pos_id = (int)$_POST['pozycja_id'];
    $captcha = trim($_POST['captcha']);
    $user_ip = $_SERVER['REMOTE_ADDR'];

    if ($captcha !== $_SESSION['captcha_code']) {
        $err = "Błędny kod z obrazka CAPTCHA!";
    } else {
        // Sprawdzenie blokady IP (np. 10 minut)
        $check = $conn->query("SELECT id FROM glosujacy_ip WHERE ip_address = '$user_ip' AND pozycja_id = $pos_id AND data_glosu > NOW() - INTERVAL 10 MINUTE");
        if ($check && $check->num_rows > 0) {
            $err = "Oddałeś już głos na tę pozycję z adresu $user_ip w ciągu ostatnich 10 minut!";
        } else {
            $conn->query("UPDATE pozycje SET glosy = glosy + 1 WHERE id = $pos_id");
            $conn->query("INSERT INTO glosujacy_ip (pozycja_id, ip_address, data_glosu) VALUES ($pos_id, '$user_ip', NOW())");
            $msg = "Dziękujemy! Twój głos został zarejestrowany.";
        }
    }
}

$pozycje = $conn ? $conn->query("SELECT * FROM pozycje ORDER BY glosy DESC") : null;
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>System Głosowania</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header><h1>System Oceniania i Głosowania</h1><nav><a href="admin.php">Panel Administratora</a><a href="install.php">Instalator</a></nav></header>
<?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
<?php if ($err) echo "<div class='alert alert-danger'>$err</div>"; ?>
<h2>Lista pozycji do głosowania:</h2>
<?php if ($pozycje && $pozycje->num_rows > 0): while($row = $pozycje->fetch_assoc()): ?>
    <div style="background:#fdfdfd; border:1px solid #ddd; padding:15px; border-radius:6px; margin-bottom:15px;">
        <h3><?php echo htmlspecialchars($row['tytul']); ?> <span class="badge">Głosów: <?php echo $row['glosy']; ?></span></h3>
        <p style="margin:8px 0;"><?php echo nl2br(htmlspecialchars($row['opis'])); ?></p>
        <form method="post" style="display:flex; gap:10px; align-items:center;">
            <input type="hidden" name="pozycja_id" value="<?php echo $row['id']; ?>">
            <img src="captcha.php" alt="CAPTCHA" style="vertical-align:middle; border:1px solid #ccc;">
            <input type="text" name="captcha" placeholder="Kod z obrazka" required style="width:120px;">
            <button type="submit" name="glosuj" class="btn">Oddaj głos</button>
        </form>
    </div>
<?php endwhile; else: echo "<p>Brak pozycji. Zainstaluj bazę w install.php!</p>"; endif; ?>
</div></body></html>
