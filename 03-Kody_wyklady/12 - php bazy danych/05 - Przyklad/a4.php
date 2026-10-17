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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $zd = $conn->real_escape_string($_POST['zd']);
    $sql = "DELETE FROM `$table_name` WHERE zd='$zd'";
    
    if ($conn->query($sql)) {
        header('Location: a2.php');
        exit;
    } else {
        $error = "Błąd usuwania: " . htmlspecialchars($conn->error);
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
    <title>Usuwanie zwierzęcia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Usuwanie zwierzęcia</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="warning">
            <p>Czy na pewno chcesz usunąć to zwierzę?</p>
            <table style="margin: 20px auto;">
                <tr>
                    <td><strong>ID (ZD):</strong></td>
                    <td><?= htmlspecialchars($row['zd']) ?></td>
                </tr>
                <tr>
                    <td><strong>Nazwa:</strong></td>
                    <td><?= htmlspecialchars($row['nazwa']) ?></td>
                </tr>
                <tr>
                    <td><strong>Typ:</strong></td>
                    <td><?= $row['typ'] ? 'domowe' : 'dzikie' ?></td>
                </tr>
                <tr>
                    <td><strong>Masa:</strong></td>
                    <td><?= htmlspecialchars($row['masa']) ?> kg</td>
                </tr>
                <tr>
                    <td><strong>Data urodzenia:</strong></td>
                    <td><?= htmlspecialchars($row['uro']) ?></td>
                </tr>
            </table>
        </div>
        
        <form method="post">
            <input type="hidden" name="zd" value="<?= htmlspecialchars($row['zd']) ?>">
            <input type="hidden" name="confirm" value="1">
            
            <div class="menu">
                <button type="submit" class="button delete">Usuń</button>
                <a href="a2.php" class="button">Anuluj</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>