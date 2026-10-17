<?php
$config = parse_ini_file('a.conf', true);
if (!$config) {
    die("Błąd: Nie można odczytać pliku konfiguracyjnego a.conf");
}

$db_host = $config['database']['host'];
$db_user = $config['database']['user'];
$db_pass = $config['database']['pass'];
$db_name = $config['database']['name'];
$table_name = $config['database']['table'];
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Błąd połączenia: " . htmlspecialchars($conn->connect_error));
}

$zd = isset($_GET['zd']) ? $conn->real_escape_string($_GET['zd']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_zd = $conn->real_escape_string($_POST['old_zd']);
    $zd = $conn->real_escape_string($_POST['zd']);
    $nazwa = $conn->real_escape_string($_POST['nazwa']);
    $typ = isset($_POST['typ']) ? 1 : 0;
    $masa = floatval($_POST['masa']);
    $uro = $conn->real_escape_string($_POST['uro']);
    
    $sql = "UPDATE `$table_name` SET 
            zd='$zd', 
            nazwa='$nazwa', 
            typ=$typ, 
            masa=$masa, 
            uro='$uro' 
            WHERE zd='$old_zd'";
    
    if ($conn->query($sql)) {
        header('Location: a2.php');
        exit;
    } else {
        $error = "Błąd aktualizacji: " . htmlspecialchars($conn->error);
    }
}

$sql = "SELECT * FROM `$table_name` WHERE zd='$zd'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if (!$row) {
    die("Nie znaleziono rekordu");
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edycja zwierzęcia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edycja zwierzęcia</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post">
            <input type="hidden" name="old_zd" value="<?= htmlspecialchars($row['zd']) ?>">
            
            <label>
                ID (ZD):
                <input type="text" name="zd" value="<?= htmlspecialchars($row['zd']) ?>" maxlength="5" required>
            </label>
            
            <label>
                Nazwa:
                <input type="text" name="nazwa" value="<?= htmlspecialchars($row['nazwa']) ?>" maxlength="20" required>
            </label>
            
            <label class="checkbox-label">
                <input type="checkbox" name="typ" <?= $row['typ'] ? 'checked' : '' ?>>
                Zwierzę domowe
            </label>
            
            <label>
                Masa (kg):
                <input type="number" name="masa" value="<?= htmlspecialchars($row['masa']) ?>" step="0.01" min="0" max="9999.99" required>
            </label>
            
            <label>
                Data urodzenia:
                <input type="date" name="uro" value="<?= htmlspecialchars($row['uro']) ?>" required>
            </label>
            
            <div class="menu">
                <button type="submit" class="button">Zapisz zmiany</button>
                <a href="a2.php" class="button">Anuluj</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>