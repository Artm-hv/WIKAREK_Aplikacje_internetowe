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

// ========== PAGINACJA ==========
$records_per_page = 20; // Ilość rekordów na stronę

// Pobierz aktualną stronę z URL (domyślnie 1)
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

// Policz wszystkie rekordy
$count_sql = "SELECT COUNT(*) as total FROM `$table_name`";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];

// Oblicz ilość stron
$total_pages = ceil($total_records / $records_per_page);

// Zabezpieczenie przed przekroczeniem liczby stron
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// Oblicz OFFSET (od którego rekordu zacząć)
$offset = ($current_page - 1) * $records_per_page;

// Pobierz tylko rekordy dla aktualnej strony
$sql = "SELECT * FROM `$table_name` ORDER BY zd LIMIT $records_per_page OFFSET $offset";
$result = $conn->query($sql);

if (!$result) {
    die("Błąd zapytania: " . htmlspecialchars($conn->error));
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wyświetlanie danych</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .pagination a:hover {
            background-color: #e9e9e9;
        }
        .pagination .active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        .pagination .disabled {
            color: #aaa;
            cursor: not-allowed;
        }
        .page-info {
            text-align: center;
            color: #666;
            margin: 10px 0;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista zwierząt</h1>
        
        <div class="menu">
            <a href="a6.php" class="button">Dodaj nowe zwierzę</a>
            <a href="a5.php" class="button">Sortowanie</a>
        </div>
        
        <!-- Informacja o rekordach -->
        <div class="page-info">
            <?php if ($total_records > 0): ?>
                Wyświetlanie <?= $offset + 1 ?>-<?= min($offset + $records_per_page, $total_records) ?> 
                z <?= $total_records ?> rekordów
            <?php endif; ?>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ZD</th>
                    <th>Nazwa</th>
                    <th>Typ</th>
                    <th>Masa (kg)</th>
                    <th>Data urodzenia</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['zd']) ?></td>
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
                        <td colspan="6" style="text-align:center;">
                            Brak danych w tabeli. <a href="a6.php">Dodaj pierwsze zwierzę</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- PAGINACJA -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <!-- Przycisk: Pierwsza strona -->
                <?php if ($current_page > 1): ?>
                    <a href="?page=1" title="Pierwsza strona">&laquo;</a>
                    <a href="?page=<?= $current_page - 1 ?>" title="Poprzednia">&lsaquo;</a>
                <?php else: ?>
                    <span class="disabled">&laquo;</span>
                    <span class="disabled">&lsaquo;</span>
                <?php endif; ?>
                
                <!-- Numery stron -->
                <?php
                // Pokaż maksymalnie 5 stron wokół aktualnej
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                // Jeśli jesteśmy na początku, pokaż więcej stron z przodu
                if ($current_page <= 2) {
                    $end_page = min($total_pages, 5);
                }
                // Jeśli jesteśmy na końcu, pokaż więcej stron z tyłu
                if ($current_page >= $total_pages - 1) {
                    $start_page = max(1, $total_pages - 4);
                }
                
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <?php if ($i == $current_page): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <!-- Przycisk: Ostatnia strona -->
                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?= $current_page + 1 ?>" title="Następna">&rsaquo;</a>
                    <a href="?page=<?= $total_pages ?>" title="Ostatnia strona">&raquo;</a>
                <?php else: ?>
                    <span class="disabled">&rsaquo;</span>
                    <span class="disabled">&raquo;</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>
</body>
</html>
<?php $conn->close(); ?>