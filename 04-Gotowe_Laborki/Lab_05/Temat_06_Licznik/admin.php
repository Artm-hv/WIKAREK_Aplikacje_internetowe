<?php
include "konfig.php";
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['usun_wpis'])) {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM wpisy WHERE id = $id");
        $msg = "Wpis usunięty!";
    } elseif (isset($_POST['blokuj_ip'])) {
        $ip = $conn->real_escape_string($_POST['ip']);
        $conn->query("INSERT IGNORE INTO zablokowane_ip (ip, powod, data_blokady) VALUES ('$ip', 'Spam', NOW())");
        $msg = "Adres IP $ip zablokowany!";
    } elseif (isset($_POST['odblokuj_ip'])) {
        $ip = $conn->real_escape_string($_POST['ip']);
        $conn->query("DELETE FROM zablokowane_ip WHERE ip = '$ip'");
        $msg = "Adres IP $ip odblokowany!";
    }
}
$wpisy = $conn ? $conn->query("SELECT * FROM wpisy ORDER BY id DESC") : null;
$blocked = $conn ? $conn->query("SELECT * FROM zablokowane_ip") : null;
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Admin - Księga Gości</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header><h1>Moderacja Księgi Gości</h1><nav><a href="index.php">Księga</a><a href="install.php">Instalator</a></nav></header>
<?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
<h2>Lista wpisów:</h2>
<table><tr><th>Autor</th><th>Wpis</th><th>IP</th><th>Akcje</th></tr>
<?php if ($wpisy) while($w = $wpisy->fetch_assoc()): ?>
<tr><td><?php echo htmlspecialchars($w['autor']); ?></td><td><?php echo htmlspecialchars(substr($w['wpis'], 0, 60)) . '...'; ?></td><td><?php echo $w['ip']; ?></td>
<td>
    <form method="post" style="display:inline;"><input type="hidden" name="id" value="<?php echo $w['id']; ?>"><button type="submit" name="usun_wpis" class="btn btn-danger" style="padding:3px 7px; font-size:12px;">Usuń</button></form>
    <form method="post" style="display:inline;"><input type="hidden" name="ip" value="<?php echo $w['ip']; ?>"><button type="submit" name="blokuj_ip" class="btn" style="padding:3px 7px; font-size:12px; background:#e67e22;">Blokuj IP</button></form>
</td></tr>
<?php endwhile; ?></table>
<h2 style="margin-top:25px;">Zablokowane adresy IP:</h2>
<table><tr><th>IP</th><th>Data blokady</th><th>Akcja</th></tr>
<?php if ($blocked) while($b = $blocked->fetch_assoc()): ?>
<tr><td><?php echo $b['ip']; ?></td><td><?php echo $b['data_blokady']; ?></td><td><form method="post"><input type="hidden" name="ip" value="<?php echo $b['ip']; ?>"><button type="submit" name="odblokuj_ip" class="btn btn-success" style="padding:3px 7px; font-size:12px;">Odblokuj</button></form></td></tr>
<?php endwhile; ?></table></div></body></html>
