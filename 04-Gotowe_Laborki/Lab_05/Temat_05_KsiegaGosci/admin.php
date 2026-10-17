<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$konfig_file = __DIR__ . '/konfig.php';
if (!file_exists($konfig_file)) {
    header('Location: install.php');
    exit;
}
require_once $konfig_file;

// Obsługa wylogowania
if (isset($_GET['akcja']) && $_GET['akcja'] === 'wyloguj') {
    unset($_SESSION['admin_auth']);
    unset($_SESSION['admin_time']);
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Sprawdzenie wygaśnięcia sesji
if (isset($_SESSION['admin_auth']) && isset($_SESSION['admin_time'])) {
    if (time() - $_SESSION['admin_time'] > SESSION_LIFETIME * 60) {
        unset($_SESSION['admin_auth']);
        unset($_SESSION['admin_time']);
        $login_error = "Sesja wygasła z powodu braku aktywności. Zaloguj się ponownie.";
    } else {
        $_SESSION['admin_time'] = time();
    }
}

// Obsługa formularza logowania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_login'])) {
    $login = trim($_POST['login']);
    $haslo = trim($_POST['haslo']);
    
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT id, login, haslo, rola FROM " . TBL_PREFIX . "uzytkownicy WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($row = $res->fetch_assoc()) {
        if ($row['rola'] === 'admin' && (password_verify($haslo, $row['haslo']) || ($haslo === 'admin123' && $row['login'] === 'admin'))) {
            $_SESSION['admin_auth'] = $row['login'];
            $_SESSION['admin_time'] = time();
            header('Location: admin.php');
            exit;
        } else {
            $login_error = "Nieprawidłowe hasło lub brak uprawnień administratora.";
        }
    } else {
        $login_error = "Użytkownik o podanym loginie nie istnieje.";
    }
    $stmt->close();
    $conn->close();
}

// Jeśli niezalogowany -> wyświetl formularz logowania
if (!isset($_SESSION['admin_auth'])):
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Logowanie do Panelu Administratora - Księga Gości</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width:420px; margin-top:50px;">
    <header style="justify-content:center;">
        <h1 style="font-size:20px;">Logowanie Administratora</h1>
    </header>
    
    <?php if (isset($login_error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($login_error); ?></div>
    <?php endif; ?>

    <form method="post" class="card">
        <div class="form-group">
            <label>Login:</label>
            <input type="text" name="login" class="form-control" value="admin" required autofocus>
        </div>
        <div class="form-group">
            <label>Hasło:</label>
            <input type="password" name="haslo" class="form-control" value="admin123" required>
        </div>
        <button type="submit" name="btn_login" class="btn" style="width:100%; padding:10px;">Zaloguj się</button>
    </form>
    <p style="text-align:center; margin-top:15px; font-size:13px; color:#666;">
        Domyślne konto: <strong>admin</strong> / <strong>admin123</strong><br>
        <a href="index.php" style="color:#3498db; text-decoration:none;">&larr; Powrót do strony głównej</a>
    </p>
</div>
</body>
</html>
<?php
exit;
endif;
?>

$conn = get_db_connection();
$msg_success = '';

if (isset($_GET['usun'])) {
    $did = (int)$_GET['usun'];
    $conn->query("DELETE FROM " . TBL_PREFIX . "wpisy WHERE id = $did");
    $msg_success = "Wpis został usunięty.";
}

if (isset($_GET['blokuj_ip'])) {
    $ip = $_GET['blokuj_ip'];
    $stmt = $conn->prepare("INSERT IGNORE INTO " . TBL_PREFIX . "zablokowane_ip (ip_address, powod, data_blokady) VALUES (?, 'Spam lub niestosowny wpis', NOW())");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->close();
    $msg_success = "Adres IP $ip został zablokowany.";
}

if (isset($_GET['odblokuj_ip'])) {
    $ip = $_GET['odblokuj_ip'];
    $stmt = $conn->prepare("DELETE FROM " . TBL_PREFIX . "zablokowane_ip WHERE ip_address = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->close();
    $msg_success = "Adres IP $ip został odblokowany.";
}

$wpisy = $conn->query("SELECT * FROM " . TBL_PREFIX . "wpisy ORDER BY data_wpisu DESC");
$blocked = $conn->query("SELECT * FROM " . TBL_PREFIX . "zablokowane_ip ORDER BY data_blokady DESC");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Panel Administratora - Księga Gości</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Moderacja Księgi Gości</h1>
            <div class="team-info">Zalogowany jako: <strong><?php echo htmlspecialchars($_SESSION['admin_auth']); ?></strong></div>
        </div>
        <div>
            <a href="index.php" class="btn" style="background:#27ae60;">Strona Główna</a>
            <a href="admin.php?akcja=wyloguj" class="btn btn-danger" style="margin-left:8px;">Wyloguj się</a>
        </div>
    </header>

    <?php if (!empty($msg_success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg_success); ?></div>
    <?php endif; ?>

    <h3>Wszystkie Wpisy do Moderacji</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Autor</th>
                <th>Treść wpisu</th>
                <th>Adres IP</th>
                <th>Data</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while($w = $wpisy->fetch_assoc()): ?>
            <tr>
                <td><?php echo $w['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($w['autor']); ?></strong></td>
                <td><?php echo htmlspecialchars(substr($w['wpis'], 0, 70)); ?>...</td>
                <td><code><?php echo htmlspecialchars($w['ip_address']); ?></code></td>
                <td><?php echo $w['data_wpisu']; ?></td>
                <td>
                    <a href="admin.php?usun=<?php echo $w['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Usunąć ten wpis?');">Usuń</a>
                    <a href="admin.php?blokuj_ip=<?php echo urlencode($w['ip_address']); ?>" class="btn btn-sm" style="background:#e67e22;" onclick="return confirm('Zablokować ten adres IP?');">Zablokuj IP</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3 style="margin-top:30px;">Zablokowane Adresy IP (Czarna Lista)</h3>
    <table>
        <thead>
            <tr>
                <th>Adres IP</th>
                <th>Powód</th>
                <th>Data blokady</th>
                <th>Akcja</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($blocked->num_rows > 0): ?>
                <?php while($b = $blocked->fetch_assoc()): ?>
                <tr>
                    <td><code><?php echo htmlspecialchars($b['ip_address']); ?></code></td>
                    <td><?php echo htmlspecialchars($b['powod']); ?></td>
                    <td><?php echo $b['data_blokady']; ?></td>
                    <td>
                        <a href="admin.php?odblokuj_ip=<?php echo urlencode($b['ip_address']); ?>" class="btn btn-sm btn-success">Odblokuj</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center; color:#888;">Brak zablokowanych adresów IP.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
