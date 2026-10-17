<?php
header('Content-Type: text/html; charset=utf-8');

$xml_file = 'baza.xml';
if (!file_exists($xml_file)) {
    die("Błąd: Plik $xml_file nie istnieje.");
}

$baza = simplexml_load_file($xml_file);
if ($baza === false) {
    die("Błąd parsowania pliku XML.");
}

// Pobranie listy dozwolonych ID encji 1
$valid_refs = [];
foreach ($baza->pacjenci->pacjent as $item) {
    $valid_refs[] = (string)$item['id'];
}

// Bezpieczne pobranie filtru (biała lista)
$selected_ref = '';
if (isset($_POST['ref1_id']) && in_array($_POST['ref1_id'], $valid_refs, true)) {
    $selected_ref = $_POST['ref1_id'];
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Baza XML: Pacjent – Badanie – Lekarz (SimpleXML &amp; XPath)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Baza XML: Pacjent – Badanie – Lekarz (3NF)</h1>
        <p>Grupa: 3ID11B | Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</p>
    </header>

    <div class="info-box">
        Wybierz rekord z tabeli <strong>pacjenci</strong>, aby przefiltrować powiązania w relacji za pomocą SimpleXML i XPath:
    </div>

    <form method="post" style="display:flex; gap:10px; align-items:center; margin-bottom:20px;">
        <select name="ref1_id" style="padding:8px 12px; border-radius:4px; border:1px solid #ccc; font-size:14px;">
            <option value="">-- Pokaż wszystkie powiązania --</option>
            <?php foreach($baza->pacjenci->pacjent as $item): ?>
                <option value="<?php echo htmlspecialchars($item['id']); ?>" <?php if ($selected_ref === (string)$item['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($item['id'] . ' - ' . $item->pesel . ' ' . $item->imie); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">Filtruj powiązania</button>
        <?php if (!empty($selected_ref)): ?>
            <a href="index.php" style="font-size:13px; color:#c0392b; text-decoration:none;">[Wyczyść filtr]</a>
        <?php endif; ?>
    </form>

    <h2>Wyniki zapytania relacyjnego (SimpleXML &amp; XPath):</h2>
    <table>
        <thead>
            <tr>
                <th>ID Relacji</th>
                <th>Podmiot (pacjent)</th>
                <th>Obiekt (lekarz)</th>
                <th>Data badania</th>
                <th>Rodzaj badania</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Absolutny XPath względem dokumentu XML
            $query = empty($selected_ref) ? "/baza/badania/badanie" : "/baza/badania/badanie[@ref1='$selected_ref']";
            $wyniki = $baza->xpath($query);

            if ($wyniki && count($wyniki) > 0):
                foreach($wyniki as $r):
                    $ref1_id = (string)$r['ref1'];
                    $ref2_id = (string)$r['ref2'];
                    
                    // Bezpieczne pobranie powiązanych obiektów
                    $target1 = $baza->xpath("/baza/pacjenci/pacjent[@id='$ref1_id']");
                    $opis1 = $target1 ? (string)$target1[0]->pesel . ' ' . (string)$target1[0]->imie : $ref1_id;

                    $target2 = $baza->xpath("/baza/lekarze/lekarz[@id='$ref2_id']");
                    $opis2 = $target2 ? (string)$target2[0]->imie_nazwisko : $ref2_id;
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars((string)$r['id']); ?></strong></td>
                <td><?php echo htmlspecialchars($opis1); ?> <span style="color:#888; font-size:12px;">(<?php echo htmlspecialchars($ref1_id); ?>)</span></td>
                <td><?php echo htmlspecialchars($opis2); ?> <span style="color:#888; font-size:12px;">(<?php echo htmlspecialchars($ref2_id); ?>)</span></td>
                <td><?php echo htmlspecialchars((string)$r->data); ?></td>
                <td><?php echo htmlspecialchars((string)$r->opis); ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#7f8c8d;">Brak wyników dla podanego kryterium.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:25px; padding:12px; background:#e8f4f8; border-radius:5px; font-size:13px;">
        <strong>Informacja:</strong> Zapytanie XPath: <code><?php echo htmlspecialchars($query); ?></code> zwróciło <strong><?php echo $wyniki ? count($wyniki) : 0; ?></strong> rekordów.
    </div>
</div>
</body>
</html>
