<?php
include "konfig.php";
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['plik_ocen'])) {
    if (is_uploaded_file($_FILES['plik_ocen']['tmp_name'])) {
        $lines = file($_FILES['plik_ocen']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (count($lines) >= 2) {
            $header = explode(";", $lines[0]);
            $kod = trim($header[0]);
            $nazwa = trim($header[1]);
            $termin = trim($header[2]);
            
            $count = 0;
            for ($i = 1; $i < count($lines); $i++) {
                $parts = explode(";", $lines[$i]);
                if (count($parts) >= 2) {
                    $alb = trim($parts[0]);
                    $oc = trim($parts[1]);
                    // Nadpisanie lub dodanie
                    $conn->query("DELETE FROM oceny WHERE kod_przedmiotu = '$kod' AND nr_albumu = '$alb' AND termin = '$termin'");
                    $conn->query("INSERT INTO oceny (kod_przedmiotu, nazwa_przedmiotu, termin, nr_albumu, ocena, status) VALUES ('$kod', '$nazwa', '$termin', '$alb', '$oc', 'nieprzejrzana')");
                    $count++;
                }
            }
            $msg = "Pomyślnie zaimportowano $count ocen dla przedmiotu $nazwa!";
        }
    }
}
$wszystkie = $conn ? $conn->query("SELECT * FROM oceny ORDER BY id DESC LIMIT 50") : null;
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Admin - Wgrywanie Ocen</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header><h1>Wgrywanie ocen z pliku tekstowego</h1><nav><a href="index.php">Widok studenta</a><a href="install.php">Instalator</a></nav></header>
<?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
<form method="post" enctype="multipart/form-data" style="margin-bottom:25px;">
    <div class="form-group"><label>Wybierz plik tekstowy (.txt):</label><input type="file" name="plik_ocen" required></div>
    <button type="submit" class="btn">Wgraj oceny</button>
</form>
<h2>Wszystkie oceny w bazie (ostatnie 50):</h2>
<table><tr><th>Album</th><th>Przedmiot</th><th>Termin</th><th>Ocena</th><th>Status decyzji</th></tr>
<?php if ($wszystkie) while($r = $wszystkie->fetch_assoc()): ?>
<tr><td><?php echo $r['nr_albumu']; ?></td><td><?php echo htmlspecialchars($r['nazwa_przedmiotu']); ?></td><td><?php echo $r['termin']; ?></td><td><?php echo $r['ocena']; ?></td><td><?php echo $r['status']; ?></td></tr>
<?php endwhile; ?></table></div></body></html>
