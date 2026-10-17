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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nazwa = $conn->real_escape_string($_POST['nazwa']);
    $typ = isset($_POST['typ']) ? 1 : 0;
    $masa = floatval($_POST['masa']);
    $uro = $conn->real_escape_string($_POST['uro']);
    
    $sql = "INSERT INTO `$table_name` (zd, nazwa, typ, masa, uro) 
            VALUES ('', '$nazwa', $typ, $masa, '$uro')";
    
    if ($conn->query($sql)) {
        $last_id = $conn->insert_id;
        
        $zd = 'ZD' . str_pad($last_id, 3, '0', STR_PAD_LEFT);
        
        $update_sql = "UPDATE `$table_name` SET zd = '$zd' WHERE id = $last_id";
        $conn->query($update_sql);
        
        header('Location: a2.php');
        exit;
    } else {
        $error = "Błąd dodawania: " . htmlspecialchars($conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodawanie danych</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Dodaj nowe zwierzę</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post">
            <label>
                Nazwa:
                <input type="text" name="nazwa" maxlength="20" required>
            </label>
            
            <label>
                <input type="checkbox" name="typ">
                Zwierzę domowe (odznacz dla dzikiego)
            </label>
            
            <label>
                Masa (kg):
                <input type="number" name="masa" step="0.01" min="0" max="9999.99" required>
            </label>
            
            <label>
                Data urodzenia:
                <input type="date" name="uro" required>
            </label>
            
            <div class="menu">
                <button type="submit" class="button">Dodaj zwierzę</button>
                <a href="a2.php" class="button">Anuluj</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>