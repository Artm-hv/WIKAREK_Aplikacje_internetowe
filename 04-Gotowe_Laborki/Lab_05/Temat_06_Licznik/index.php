<?php
session_start();
include "konfig.php";
$msg = ""; $err = "";
$user_ip = $_SERVER['REMOTE_ADDR'];

// Sprawdzenie czy IP jest zablokowane
$is_blocked = false;
if ($conn) {
    $chk = $conn->query("SELECT * FROM zablokowane_ip WHERE ip = '$user_ip'");
    if ($chk && $chk->num_rows > 0) $is_blocked = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_wpis'])) {
    if ($is_blocked) {
        $err = "Twój adres IP ($user_ip) został zablokowany przez administratora!";
    } elseif ($_POST['captcha'] !== $_SESSION['captcha_code']) {
        $err = "Niepoprawny kod z obrazka CAPTCHA!";
    } else {
        $autor = $conn->real_escape_string($_POST['autor']);
        $wpis = $conn->real_escape_string($_POST['wpis']);
        $conn->query("INSERT INTO wpisy (autor, wpis, data_wpisu, ip) VALUES ('$autor', '$wpis', NOW(), '$user_ip')");
        $msg = "Dziękujemy! Wpis został dodany.";
    }
}
$wpisy = $conn ? $conn->query("SELECT * FROM wpisy ORDER BY data_wpisu DESC") : null;
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Księga Gości</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header><h1>Elektroniczna Księga Gości</h1><nav><a href="admin.php">Moderacja (Admin)</a><a href="install.php">Instalator</a></nav></header>
<?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
<?php if ($err) echo "<div class='alert alert-danger'>$err</div>"; ?>
<?php if (!$is_blocked): ?>
<h2>Dodaj wpis do księgi:</h2>
<form method="post" style="margin-bottom:25px;">
    <div class="form-group"><label>Twój podpis / Nick:</label><input type="text" name="autor" required></div>
    <div class="form-group"><label>Treść wiadomości:</label><textarea name="wpis" required></textarea></div>
    <div class="form-group" style="display:flex; align-items:center; gap:10px;">
        <img src="captcha.php" alt="CAPTCHA">
        <input type="text" name="captcha" placeholder="Przepisz kod z rysunku" required style="width:160px;">
    </div>
    <button type="submit" name="dodaj_wpis" class="btn">Dodaj wpis</button>
</form>
<?php else: ?>
<div class="alert alert-danger">Możliwość dodawania wpisów dla Twojego adresu IP została zablokowana.</div>
<?php endif; ?>
<h2>Wpisy gości:</h2>
<?php if ($wpisy) while($w = $wpisy->fetch_assoc()): ?>
<div style="border-bottom:1px solid #eee; padding:10px 0;">
    <strong><?php echo htmlspecialchars($w['autor']); ?></strong> <span class="badge"><?php echo $w['data_wpisu']; ?></span>
    <p style="margin-top:5px; color:#444;"><?php echo nl2br(htmlspecialchars($w['wpis'])); ?></p>
</div>
<?php endwhile; ?>
</div></body></html>
