<?php
session_start();
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

$allowed_columns = ['zd', 'nazwa', 'typ', 'masa', 'uro'];

$column = isset($_GET['col']) ? $_GET['col'] : 'zd';
if (!in_array($column, $allowed_columns)) {
    $column = 'zd';
}

if (!isset($_SESSION['sort_direction'])) {
    $_SESSION['sort_direction'] = [];
}

if (isset($_SESSION['sort_direction'][$column]) && $_SESSION['sort_direction'][$column] === 'ASC') {
    $_SESSION['sort_direction'][$column] = 'DESC';
    $direction = 'DESC';
} else {
    $_SESSION['sort_direction'][$column] = 'ASC';
    $direction = 'ASC';
}

$sql = "SELECT * FROM `$table_name` ORDER BY `$column` $direction";
$result = $conn->query($sql);

if (!$result) {
    die("Błąd zapytania: " . htmlspecialchars($conn->error));
}

function getSortArrow($col, $current_col, $session_direction) {
    if ($col !== $current_col) {
        return '';
    }
    return $session_direction === 'ASC' ? ' ▲' : ' ▼';
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Sortowanie danych</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Lista zwierząt - Sortowanie</h1>
        
        <div class="menu">
            <a href="a2.php" class="button">Lista bez sortowania</a>
            <a href="a6.php" class="button">Dodaj nowe zwierzę</a>
        </div>        
        <table>
            <thead>
                <tr>
                    <th>
                        <a href="?col=nazwa">Nazwa<?= getSortArrow('nazwa', $column, $direction) ?></a>
                    </th>
                    <th>
                        <a href="?col=typ">Typ<?= getSortArrow('typ', $column, $direction) ?></a>
                    </th>
                    <th>
                        <a href="?col=masa">Masa (kg)<?= getSortArrow('masa', $column, $direction) ?></a>
                    </th>
                    <th>
                        <a href="?col=uro">Data urodzenia<?= getSortArrow('uro', $column, $direction) ?></a>
                    </th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nazwa']) ?></td>
                            <td><?= $row['typ'] ? 'domowe' : 'dzikie' ?></td>
                            <td><?= htmlspecialchars($row['masa']) ?></td>
                            <td><?= htmlspecialchars($row['uro']) ?></td>
                            <td>
                                <a href="a3.php?zd=<?= urlencode($row['zd']) ?>" class="button-small">Edytuj</a>
                                <a href="a4.php?zd=<?= urlencode($row['zd']) ?>" class="button-small delete">Usuń</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">
                            Brak danych w tabeli. <a href="a6.php">Dodaj pierwsze zwierzę</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>