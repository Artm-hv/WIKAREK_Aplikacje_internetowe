<?php
header('Content-Type: text/html; charset=utf-8');

$lock_file = __DIR__ . '/install.lock';
$is_locked = file_exists($lock_file);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_install'])) {
    if ($is_locked && !isset($_POST['force_reinstall'])) {
        $error = "System jest już zainstalowany. Zaznacz 'Wymuś reinstalację', aby nadpisać tabele.";
    } else {
        $db_host = trim($_POST['db_host']);
        $db_user = trim($_POST['db_user']);
        $db_pass = $_POST['db_pass'];
        $db_name = trim($_POST['db_name']);
        $prefix = trim($_POST['tbl_prefix']);
        $admin_user = trim($_POST['admin_user']);
        $admin_pass = trim($_POST['admin_pass']);
        $session_lifetime = (int)$_POST['session_lifetime'];

        // Fallback dla hasła w MySQL
        $conn = @new mysqli($db_host, $db_user, $db_pass);
        if ($conn->connect_error) {
            $conn = @new mysqli($db_host, $db_user, '');
            if (!$conn->connect_error) {
                $db_pass = '';
            } else {
                $error = "Błąd połączenia z serwerem bazy danych MySQL: " . $conn->connect_error;
            }
        }

        if (empty($error)) {
            $conn->set_charset('utf8mb4');
            $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn->select_db($db_name);

            // 1. Tabela użytkowników i administratorów
            $conn->query("CREATE TABLE IF NOT EXISTS `{$prefix}uzytkownicy` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `login` VARCHAR(50) NOT NULL UNIQUE,
                `haslo` VARCHAR(255) NOT NULL,
                `rola` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
                `utworzono` DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Dodanie konta student / student (wymóg bezwzględny Dr. Wikarka)
            $pass_student = password_hash('student', PASSWORD_DEFAULT);
            $conn->query("INSERT INTO `{$prefix}uzytkownicy` (`login`, `haslo`, `rola`, `utworzono`) 
                          VALUES ('student', '$pass_student', 'student', NOW()) 
                          ON DUPLICATE KEY UPDATE `haslo` = '$pass_student';");

            // Dodanie konta administratora
            $pass_admin = password_hash($admin_pass, PASSWORD_DEFAULT);
            $conn->query("INSERT INTO `{$prefix}uzytkownicy` (`login`, `haslo`, `rola`, `utworzono`) 
                          VALUES ('$admin_user', '$pass_admin', 'admin', NOW()) 
                          ON DUPLICATE KEY UPDATE `haslo` = '$pass_admin', `rola` = 'admin';");

            // 2. Tabele specyficzne dla tematu
            $conn->query("CREATE TABLE IF NOT EXISTS `{$prefix}menu` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nazwa` VARCHAR(100) NOT NULL,
                `typ` ENUM('html', 'link_wew', 'link_zew') NOT NULL,
                `zawartosc` TEXT NOT NULL,
                `kolejnosc` INT NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $conn->query("INSERT IGNORE INTO `{$prefix}menu` (`id`, `nazwa`, `typ`, `zawartosc`, `kolejnosc`) VALUES
            (1, 'Strona Główna', 'html', '<h2>Witamy w naszym portalu!</h2><p>Portal zrealizowany w ramach Laboratorium 5 przez Artem Hizhovskyi i Ihor Namachynskyi (3ID11B).</p><p>Wybierz pozycję z menu po lewej stronie, aby dynamicznie załadować żądaną treść.</p>', 1),
            (2, 'O Katedrze', 'html', '<h2>Katedra Informatyki Stosowanej</h2><p>Katedra prowadzi zajęcia z technologii internetowych, baz danych i systemów wbudowanych.</p>', 2),
            (3, 'Kontakt z Uczelnią', 'link_wew', 'podstrony/kontakt.html', 3),
            (4, 'Regulamin Laboratorium', 'link_wew', 'podstrony/regulamin.html', 4),
            (5, 'Strona PŚk (Zewnętrzna)', 'link_zew', 'https://tu.kielce.pl', 5);");

            // 3. Generowanie konfig.php
            $config_content = "<?php\n" .
                "define('DB_HOST', " . var_export($db_host, true) . ");\n" .
                "define('DB_USER', " . var_export($db_user, true) . ");\n" .
                "define('DB_PASS', " . var_export($db_pass, true) . ");\n" .
                "define('DB_NAME', " . var_export($db_name, true) . ");\n" .
                "define('TBL_PREFIX', " . var_export($prefix, true) . ");\n" .
                "define('SESSION_LIFETIME', " . $session_lifetime . ");\n" .
"define('PORTAL_NAME', 'Mini Portal Uczelniany');\n" .
                "\nfunction get_db_connection() {\n" .
                "    \$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);\n" .
                "    if (\$conn->connect_error) {\n" .
                "        \$conn = @new mysqli(DB_HOST, DB_USER, '', DB_NAME);\n" .
                "    }\n" .
                "    if (\$conn->connect_error) {\n" .
                "        die('Błąd połączenia z bazą danych: ' . \$conn->connect_error);\n" .
                "    }\n" .
                "    \$conn->set_charset('utf8mb4');\n" .
                "    return \$conn;\n" .
                "}\n";

            file_put_contents(__DIR__ . '/konfig.php', $config_content);
            file_put_contents($lock_file, date('Y-m-d H:i:s'));
            $success = "System został pomyślnie skonfigurowany i zainstalowany!";
            $is_locked = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Instalacja - Mini Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width:650px;">
    <header>
        <h1>Instalator Systemu (install.php)</h1>
        <div class="team-info">Grupa: 3ID11B | Temat 4: Mini Portal</div>
    </header>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <strong>Sukces!</strong> <?php echo htmlspecialchars($success); ?><br><br>
            <a href="index.php" class="btn btn-success">Przejdź do strony głównej (index.php)</a>
            <a href="admin.php" class="btn" style="margin-left:10px;">Panel Administratora (admin.php)</a>
        </div>
    <?php else: ?>
        <?php if ($is_locked): ?>
            <div class="alert alert-warning">
                <strong>Uwaga:</strong> Plik blokady <code>install.lock</code> istnieje. System jest już skonfigurowany.
            </div>
        <?php endif; ?>

        <form method="post" class="card">
            <h3 style="margin-bottom:15px; border-bottom:1px solid #ddd; padding-bottom:5px;">Parametry Bazy Danych MySQL</h3>
            <div class="form-group">
                <label>Serwer bazy (Host):</label>
                <input type="text" name="db_host" class="form-control" value="localhost" required>
            </div>
            <div class="form-group">
                <label>Użytkownik MySQL:</label>
                <input type="text" name="db_user" class="form-control" value="root" required>
            </div>
            <div class="form-group">
                <label>Hasło MySQL (domyślnie 'student' na VM lub puste):</label>
                <input type="password" name="db_pass" class="form-control" value="student">
            </div>
            <div class="form-group">
                <label>Nazwa Bazy Danych (zgodnie z instrukcją: 'test'):</label>
                <input type="text" name="db_name" class="form-control" value="test" required>
            </div>
            <div class="form-group">
                <label>Prefiks tabel (zgodnie z instrukcją: T_xxx_yy_):</label>
                <input type="text" name="tbl_prefix" class="form-control" value='T_3ID11B_01_' required>
            </div>

            <h3 style="margin:20px 0 15px 0; border-bottom:1px solid #ddd; padding-bottom:5px;">Konto Administratora i Sesja</h3>
            <div class="form-group">
                <label>Login Administratora:</label>
                <input type="text" name="admin_user" class="form-control" value="admin" required>
            </div>
            <div class="form-group">
                <label>Hasło Administratora:</label>
                <input type="password" name="admin_pass" class="form-control" value="admin123" required>
            </div>
            <div class="form-group">
                <label>Czas ważności sesji (w minutach):</label>
                <input type="number" name="session_lifetime" class="form-control" value="30" min="1" max="1440" required>
            </div>

            <?php if ($is_locked): ?>
            <div class="form-group" style="margin-top:15px;">
                <label style="color:#c0392b;">
                    <input type="checkbox" name="force_reinstall" value="1"> Wymuś reinstalację i nadpisanie konfiguracji
                </label>
            </div>
            <?php endif; ?>

            <button type="submit" name="btn_install" class="btn" style="width:100%; padding:10px; font-size:16px;">
                Zainstaluj i Wygeneruj konfig.php
            </button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
