<?php
session_start();

$saved_host = isset($_SESSION['saved_host']) ? $_SESSION['saved_host'] : 'localhost';
$saved_user = isset($_SESSION['saved_user']) ? $_SESSION['saved_user'] : 'root';
$saved_dbname = isset($_SESSION['saved_dbname']) ? $_SESSION['saved_dbname'] : 'b_3ID11B';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = isset($_POST['host']) ? htmlspecialchars($_POST['host']) : 'localhost';
    $user = isset($_POST['user']) ? htmlspecialchars($_POST['user']) : '';
    $pass = isset($_POST['pass']) ? htmlspecialchars($_POST['pass']) : '';
    $dbname = isset($_POST['dbname']) ? htmlspecialchars($_POST['dbname']) : '';
    
    $_SESSION['saved_host'] = $host;
    $_SESSION['saved_user'] = $user;
    $_SESSION['saved_dbname'] = $dbname;
    
    mysqli_report(MYSQLI_REPORT_OFF);
    
    try {
        $conn = @new mysqli($host, $user, $pass);
        
        if ($conn->connect_error) {
            throw new Exception($conn->connect_error);
        }
    } catch (Exception $e) {
        $connection_error = "Nie można połączyć się z bazą danych. Sprawdź czy dane są poprawne:";
        $connection_details = "• Host: $host<br>• Użytkownik: $user<br>• Hasło: " . (empty($pass) ? "nie podano" : "****");
        $show_form = true;
        $conn = null; 
    }
    
    if (isset($conn) && $conn !== null && !$conn->connect_error) {
        unset($_SESSION['saved_host']);
        unset($_SESSION['saved_user']);
        unset($_SESSION['saved_dbname']);
        
        $result = $conn->query("SHOW DATABASES LIKE '$dbname'");
        $db_exists = ($result && $result->num_rows > 0);
        
        if ($db_exists) {
            if (isset($_POST['drop_db'])) {
                $conn->query("DROP DATABASE `$dbname`");
                $conn->query("CREATE DATABASE `$dbname`");
                $message = "Baza została usunięta i utworzona ponownie. ";
            } elseif (!isset($_POST['keep_db']) && !isset($_POST['drop_table']) && !isset($_POST['keep_table'])) {
                $confirm_db = true;
            }
        } else {
            if ($conn->query("CREATE DATABASE `$dbname`")) {
                $message = "Baza została utworzona. ";
            } else {
                $error = "Błąd tworzenia bazy: " . htmlspecialchars($conn->error);
                $show_form = true;
            }
        }
        
        if (!isset($confirm_db) && !isset($error)) {
            if (!$conn->select_db($dbname)) {
                $error = "Nie można wybrać bazy danych: " . htmlspecialchars($conn->error);
                $show_form = true;
            } else {
                $table_name = "T_3ID11B_02_zwierzeta";
                $result = $conn->query("SHOW TABLES LIKE '$table_name'");
                $table_exists = ($result && $result->num_rows > 0);
                
                if ($table_exists) {
                    if (isset($_POST['drop_table'])) {
                        $conn->query("DROP TABLE `$table_name`");
                        $sql = "CREATE TABLE `$table_name` (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            zd VARCHAR(5) NOT NULL,
                            nazwa VARCHAR(20) NOT NULL,
                            typ BOOLEAN,
                            masa DECIMAL(6,2),
                            uro DATE
                        )";
                        if ($conn->query($sql)) {
                            if (!isset($message)) $message = "";
                            $message .= "Tabela została usunięta i utworzona ponownie.";
                        } else {
                            $error = "Błąd tworzenia tabeli: " . htmlspecialchars($conn->error);
                            $show_form = true;
                        }
                    } elseif (!isset($_POST['keep_table'])) {
                        $confirm_table = true;
                    } 
                } else {
                    $sql = "CREATE TABLE `$table_name` (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        zd VARCHAR(5) NOT NULL,
                        nazwa VARCHAR(20) NOT NULL,
                        typ BOOLEAN,
                        masa DECIMAL(6,2),
                        uro DATE
                    )";
                    if ($conn->query($sql)) {
                        if (!isset($message)) $message = "";
                        $message .= "Tabela została utworzona.";
                    } else {
                        $error = "Błąd tworzenia tabeli: " . htmlspecialchars($conn->error);
                        $show_form = true;
                    }
                }
                
                if (!isset($confirm_table) && !isset($error)) {
                    $config = "[database]\n";
                    $config .= "host = \"" . addslashes($host) . "\"\n";
                    $config .= "user = \"" . addslashes($user) . "\"\n";
                    $config .= "pass = \"" . addslashes($pass) . "\"\n";
                    $config .= "name = \"" . addslashes($dbname) . "\"\n";
                    $config .= "table = \"T_3ID11B_02_zwierzeta\"\n";
 
                    if (file_put_contents('a.conf', $config)) {
                        $success = "Konfiguracja zakończona pomyślnie!";
                    } else {
                        $error = "Nie można zapisać pliku a.conf. Sprawdź uprawnienia do zapisu.";
                        $show_form = true;
                    }
                }
            }
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Konfiguracja bazy danych</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .connection-error {
            background-color: #fff3e0;
            border: 2px solid #ff9800;
            color: #e65100;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
        }
        .connection-error strong {
            font-size: 1.1em;
        }
        .connection-details {
            font-size: 0.9em;
            margin-top: 10px;
            line-height: 1.8;
            background: rgba(255,255,255,0.5);
            padding: 10px;
            border-radius: 4px;
        }
        .hint {
            color: #666;
            font-size: 0.85em;
            margin-top: 15px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Konfiguracja bazy danych</h1>
        
        <?php if (isset($connection_error)): ?>
            <div class="connection-error">
                <strong>⚠️ <?= $connection_error ?></strong>
                <div class="connection-details"><?= $connection_details ?></div>
                <p class="hint">Popraw poniższe dane i spróbuj ponownie:</p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error) && !isset($show_form)): ?>
            <div class="error"><?= $error ?></div>
            <a href="a1.php" class="button">Spróbuj ponownie</a>
        <?php elseif (isset($error) && isset($show_form)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($message) && !isset($success)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?= $success ?></div>
            <div class="menu">
                <a href="a2.php" class="button">Przejdź do wyświetlania danych</a>
            </div>
        <?php elseif (isset($confirm_db)): ?>
            <div class="warning">Baza danych "<?= htmlspecialchars($dbname) ?>" już istnieje. Czy chcesz ją usunąć i utworzyć ponownie?</div>
            <div class="menu">
                <form method="post" style="display:inline;">
                    <input type="hidden" name="host" value="<?= htmlspecialchars($host) ?>">
                    <input type="hidden" name="user" value="<?= htmlspecialchars($user) ?>">
                    <input type="hidden" name="pass" value="<?= htmlspecialchars($pass) ?>">
                    <input type="hidden" name="dbname" value="<?= htmlspecialchars($dbname) ?>">
                    <input type="hidden" name="drop_db" value="1">
                    <button type="submit" class="button delete">Tak, usuń i utwórz ponownie</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="host" value="<?= htmlspecialchars($host) ?>">
                    <input type="hidden" name="user" value="<?= htmlspecialchars($user) ?>">
                    <input type="hidden" name="pass" value="<?= htmlspecialchars($pass) ?>">
                    <input type="hidden" name="dbname" value="<?= htmlspecialchars($dbname) ?>">
                    <input type="hidden" name="keep_db" value="1">
                    <button type="submit" class="button">Nie, zachowaj bazę</button>
                </form>
            </div>
        <?php elseif (isset($confirm_table)): ?>
            <div class="warning">Tabela "T_3ID11B_02_zwierzeta" już istnieje. Czy chcesz ją usunąć i utworzyć ponownie?</div>
            <div class="menu">
                <form method="post" style="display:inline;">
                    <input type="hidden" name="host" value="<?= htmlspecialchars($host) ?>">
                    <input type="hidden" name="user" value="<?= htmlspecialchars($user) ?>">
                    <input type="hidden" name="pass" value="<?= htmlspecialchars($pass) ?>">
                    <input type="hidden" name="dbname" value="<?= htmlspecialchars($dbname) ?>">
                    <input type="hidden" name="keep_db" value="1">
                    <input type="hidden" name="drop_table" value="1">
                    <button type="submit" class="button delete">Tak, usuń i utwórz ponownie</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="host" value="<?= htmlspecialchars($host) ?>">
                    <input type="hidden" name="user" value="<?= htmlspecialchars($user) ?>">
                    <input type="hidden" name="pass" value="<?= htmlspecialchars($pass) ?>">
                    <input type="hidden" name="dbname" value="<?= htmlspecialchars($dbname) ?>">
                    <input type="hidden" name="keep_db" value="1">
                    <input type="hidden" name="keep_table" value="1">
                    <button type="submit" class="button">Nie, zachowaj tabelę</button>
                </form>
            </div>
        <?php else: ?>
            <form method="post">
                <label>Host: <input type="text" name="host" value="localhost" required></label>
                <label>Użytkownik: <input type="text" name="user" value="root" required></label>
                <label>Hasło: <input type="password" name="pass" value="admin" required></label>
                <label>Baza danych: <input type="text" name="dbname" value="b_3ID11B" required></label>
                <button type="submit" class="button">Połącz</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>