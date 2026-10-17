<?php
header('Content-Type: text/html; charset=utf-8');
$xml_file = "baza.xml";
if (!file_exists($xml_file)) die("Brak pliku $xml_file");

$baza = simplexml_load_file($xml_file);
$selected_ref = isset($_POST['ref1_id']) ? $_POST['ref1_id'] : '';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Baza XML: Spektakl – Rezerwacja – Widz (3NF) - SimpleXML</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Baza XML: Spektakl – Rezerwacja – Widz (3NF)</h1>
        <p>Grupa: 3ID11B | Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</p>
    </header>

    <div class="info-box">
        Wybierz rekord z tabeli <strong>widzowie</strong>, aby przefiltrować powiązania w relacji za pomocą SimpleXML i XPath:
    </div>

    <form method="post" style="display:flex; gap:10px; align-items:center; margin-bottom:20px;">
        <select name="ref1_id">
            <option value="">-- Pokaż wszystkie powiązania --</option>
            <?php foreach($baza->widzowie->widz as $item): ?>
                <option value="<?php echo $item['id']; ?>" <?php if ($selected_ref == $item['id']) echo 'selected'; ?>>
                    <?php echo $item['id'] . ' - ' . $item->imie . ' ' . $item->nazwisko; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">Filtruj powiązania</button>
    </form>

    <h2>Wyniki zapytania relacyjnego (SimpleXML):</h2>
    <table>
        <thead>
            <tr>
                <th>ID Relacji</th>
                <th>Obiekt 1 (Ref1)</th>
                <th>Obiekt 2 (Ref2)</th>
                <th>data</th> <th>miejsce</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = empty($selected_ref) ? "baza/rezerwacje/rezerwacja" : "baza/rezerwacje/rezerwacja[@ref1='$selected_ref']";
            $wyniki = $baza->xpath($query);

            if ($wyniki):
                foreach($wyniki as $r):
                    // Wyszukanie obiektu 2 po IDREF
                    $ref2_id = (string)$r['ref2'];
                    $target2 = $baza->xpath("baza/spektakle/spektakl[@id='$ref2_id']");
                    $opis2 = $target2 ? (string)$target2[0]->tytul : $ref2_id;
            ?>
            <tr>
                <td><strong><?php echo $r['id']; ?></strong></td>
                <td><?php echo $r['ref1']; ?></td>
                <td><?php echo htmlspecialchars($opis2); ?> (<?php echo $ref2_id; ?>)</td>
                <td><?php echo htmlspecialchars($r->data); ?></td> <td><?php echo htmlspecialchars($r->miejsce); ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="5" style="text-align:center; color:#888;">Brak wyników dla wybranego filtru.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <br>
    <p><a href="baza.xml" target="_blank" class="btn" style="text-decoration:none; display:inline-block;">Otwórz surowy plik baza.xml w przeglądarce (XSLT)</a></p>
</div>
</body>
</html>
