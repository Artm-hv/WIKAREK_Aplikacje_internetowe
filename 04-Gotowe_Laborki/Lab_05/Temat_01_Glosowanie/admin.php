<?php
include "konfig.php";
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['dodaj'])) {
        $t = $conn->real_escape_string($_POST['tytul']);
        $o = $conn->real_escape_string($_POST['opis']);
        $conn->query("INSERT INTO pozycje (tytul, opis, glosy) VALUES ('$t', '$o', 0)");
        $msg = "Dodano nową pozycję!";
    } elseif (isset($_POST['usun'])) {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM pozycje WHERE id = $id");
        $msg = "Usunięto pozycję!";
    } elseif (isset($_POST['zeruj'])) {
        $id = (int)$_POST['id'];
        $conn->query("UPDATE pozycje SET glosy = 0 WHERE id = $id");
        $conn->query("DELETE FROM glosujacy_ip WHERE pozycja_id = $id");
        $msg = "Zresetowano głosy dla pozycji!";
    }
}
$list = $conn ? $conn->query("SELECT * FROM pozycje") : null;
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Panel Admina - Głosowanie</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header><h1>Panel Administratora - Głosowanie</h1><nav><a href="index.php">Strona Główna</a><a href="install.php">Instalator</a></nav></header>
<?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
<h2>Dodaj nową pozycję:</h2>
<form method="post" style="margin-bottom:25px;">
    <div class="form-group"><label>Tytuł:</label><input type="text" name="tytul" required></div>
    <div class="form-group"><label>Opis:</label><textarea name="opis" required></textarea></div>
    <button type="submit" name="dodaj" class="btn">Dodaj pozycję</button>
</form>
<h2>Zarządzanie istniejącymi pozycjami:</h2>
<table><tr><th>ID</th><th>Tytuł</th><th>Głosy</th><th>Akcje</th></tr>
<?php if ($list) while($r = $list->fetch_assoc()): ?>
<tr><td><?php echo $r['id']; ?></td><td><?php echo htmlspecialchars($r['tytul']); ?></td><td><?php echo $r['glosy']; ?></td>
<td><form method="post" style="display:inline;"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button type="submit" name="zeruj" class="btn" style="padding:4px 8px; font-size:12px;">Zeruj</button> <button type="submit" name="usun" class="btn btn-danger" style="padding:4px 8px; font-size:12px;">Usuń</button></form></td></tr>
<?php endwhile; ?></table></div></body></html>
