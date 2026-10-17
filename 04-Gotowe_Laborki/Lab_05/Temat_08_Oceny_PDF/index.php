<?php
include "konfig.php";
$album = isset($_GET['album']) ? trim($_GET['album']) : '';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['ocena_id'];
    $act = $_POST['akcja'];
    $status = ($act === 'akceptuj') ? 'zaakceptowana' : 'odrzucona';
    $conn->query("UPDATE oceny SET status = '$status' WHERE id = $id");
    $msg = "Status oceny zaktualizowany na: $status";
}

$oceny = null;
if (!empty($album) && $conn) {
    $a_esc = $conn->real_escape_string($album);
    $oceny = $conn->query("SELECT * FROM oceny WHERE nr_albumu = '$a_esc' ORDER BY nazwa_przedmiotu ASC, termin ASC");
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Wykaz Ocen Studenta</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header><h1>Elektroniczny Wykaz Ocen</h1><nav><a href="admin.php">Wgrywanie ocen (Admin)</a><a href="install.php">Instalator</a></nav></header>
<?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
<form method="get" style="display:flex; gap:10px; margin-bottom:20px;">
    <input type="text" name="album" value="<?php echo htmlspecialchars($album); ?>" placeholder="Wpisz numer albumu (np. 97882 lub 97889)" required style="max-width:300px;">
    <button type="submit" class="btn">Szukaj ocen</button>
</form>

<?php if ($oceny && $oceny->num_rows > 0): ?>
<h2>Oceny dla albumu: <?php echo htmlspecialchars($album); ?></h2>
<table><tr><th>Kod</th><th>Przedmiot</th><th>Termin</th><th>Ocena</th><th>Status decyzji</th><th>Akcja</th></tr>
<?php while($o = $oceny->fetch_assoc()): ?>
<tr>
    <td><?php echo $o['kod_przedmiotu']; ?></td>
    <td><?php echo htmlspecialchars($o['nazwa_przedmiotu']); ?></td>
    <td><?php echo $o['termin']; ?></td>
    <td><strong><?php echo $o['ocena']; ?></strong></td>
    <td><span class="badge"><?php echo $o['status']; ?></span></td>
    <td>
        <form method="post" style="display:inline;">
            <input type="hidden" name="ocena_id" value="<?php echo $o['id']; ?>">
            <button type="submit" name="akcja" value="akceptuj" class="btn btn-success" style="padding:3px 7px; font-size:12px;">Akceptuj</button>
            <button type="submit" name="akcja" value="odrzuc" class="btn btn-danger" style="padding:3px 7px; font-size:12px;">Odrzuć</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
<br>
<a href="generuj_pdf.php?album=<?php echo urlencode($album); ?>" class="btn" style="display:inline-block;" target="_blank">Pobierz Oficjalny Raport PDF (FPDF)</a>
<?php elseif (!empty($album)): ?>
<div class="alert alert-danger">Nie odnaleziono ocen dla podanego numeru albumu.</div>
<?php endif; ?>
</div></body></html>
