<?php
include "konfig.php";
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['dodaj'])) {
        $n = $conn->real_escape_string($_POST['nazwa']);
        $t = $conn->real_escape_string($_POST['typ']);
        $z = $conn->real_escape_string($_POST['zawartosc']);
        $k = (int)$_POST['kolejnosc'];
        $conn->query("INSERT INTO menu (nazwa, typ, zawartosc, kolejnosc) VALUES ('$n', '$t', '$z', $k)");
        $msg = "Pozycja dodana!";
    } elseif (isset($_POST['usun'])) {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM menu WHERE id = $id");
        $msg = "Pozycja usunięta!";
    }
}
$list = $conn ? $conn->query("SELECT * FROM menu ORDER BY kolejnosc ASC") : null;
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Admin - Mini Portal</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header><h1>Administracja Menu Portalu</h1><nav><a href="index.php">Podgląd Portalu</a><a href="install.php">Instalator</a></nav></header>
<?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
<h2>Dodaj pozycję menu:</h2>
<form method="post" style="margin-bottom:25px;">
    <div class="form-group"><label>Nazwa pozycji:</label><input type="text" name="nazwa" required></div>
    <div class="form-group"><label>Typ zawartości:</label>
        <select name="typ"><option value="html">Kod HTML</option><option value="link_wew">Link wewnętrzny</option><option value="link_zew">Link zewnętrzny</option></select>
    </div>
    <div class="form-group"><label>Zawartość / URL:</label><textarea name="zawartosc" required></textarea></div>
    <div class="form-group"><label>Kolejność:</label><input type="text" name="kolejnosc" value="10"></div>
    <button type="submit" name="dodaj" class="btn">Zapisz pozycję</button>
</form>
<table><tr><th>Kolejność</th><th>Nazwa</th><th>Typ</th><th>Akcje</th></tr>
<?php if ($list) while($r = $list->fetch_assoc()): ?>
<tr><td><?php echo $r['kolejnosc']; ?></td><td><?php echo htmlspecialchars($r['nazwa']); ?></td><td><?php echo $r['typ']; ?></td>
<td><form method="post"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button type="submit" name="usun" class="btn btn-danger" style="padding:4px 8px; font-size:12px;">Usuń</button></form></td></tr>
<?php endwhile; ?></table></div></body></html>
