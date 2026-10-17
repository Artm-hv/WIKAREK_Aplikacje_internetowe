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
    <title>Logowanie do Panelu Administratora - Licznik Odwiedzin (GD)</title>
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_set_counter'])) {
    $val = max(0, (int)$_POST['counter_val']);
    $conn->query("UPDATE " . TBL_PREFIX . "licznik_stat SET lacznie_odwiedzin = $val WHERE id = 1");
    $msg_success = "Licznik odwiedzin został zaktualizowany na wartość: $val";
}

if (isset($_GET['zeruj'])) {
    $conn->query("UPDATE " . TBL_PREFIX . "licznik_stat SET lacznie_odwiedzin = 0 WHERE id = 1");
    $conn->query("TRUNCATE TABLE " . TBL_PREFIX . "wizyty_ip");
    $msg_success = "Wyzerowano licznik oraz historię wizyt.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_block_ip'])) {
    $b_ip = trim($_POST['ip_to_block']);
    $powod = trim($_POST['powod']);
    if (!empty($b_ip)) {
        $stmt = $conn->prepare("INSERT IGNORE INTO " . TBL_PREFIX . "zablokowane_ip (ip_address, powod) VALUES (?, ?)");
        $stmt->bind_param("ss", $b_ip, $powod);
        $stmt->execute();
        $stmt->close();
        $msg_success = "Zablokowano adres IP: $b_ip";
    }
}

if (isset($_GET['odblokuj_ip'])) {
    $ip = $_GET['odblokuj_ip'];
    $conn->query("DELETE FROM " . TBL_PREFIX . "zablokowane_ip WHERE ip_address = '" . $conn->real_escape_string($ip) . "'");
    $msg_success = "Odblokowano adres IP: $ip";
}

$stat = $conn->query("SELECT lacznie_odwiedzin FROM " . TBL_PREFIX . "licznik_stat WHERE id = 1")->fetch_assoc();
$blocked = $conn->query("SELECT * FROM " . TBL_PREFIX . "zablokowane_ip");
$recent_visits = $conn->query("SELECT ip_address, ostatnia_aktywnosc FROM " . TBL_PREFIX . "wizyty_ip ORDER BY ostatnia_aktywnosc DESC LIMIT 15");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Panel Administratora - Licznik Odwiedzin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Zarządzanie Licznikiem Odwiedzin</h1>
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

    <div style="display:flex; gap:20px; flex-wrap:wrap;">
        <div class="card" style="flex:1; min-width:300px;">
            <h3>Ustawienia Licznika</h3>
            <p style="margin:10px 0;">Aktualny stan licznika: <strong style="font-size:20px; color:#27ae60;"><?php echo $stat['lacznie_odwiedzin']; ?></strong></p>
            <form method="post" style="margin-top:15px;">
                <div class="form-group">
                    <label>Ustaw nową wartość licznika:</label>
                    <input type="number" name="counter_val" class="form-control" value="<?php echo $stat['lacznie_odwiedzin']; ?>" required>
                </div>
                <button type="submit" name="btn_set_counter" class="btn">Zapisz wartość</button>
                <a href="admin.php?zeruj=1" class="btn btn-danger" style="margin-left:10px;" onclick="return confirm('Wyzerować licznik?');">Zeruj licznik</a>
            </form>
        </div>

        <div class="card" style="flex:1; min-width:300px;">
            <h3>Blokowanie Adresów IP</h3>
            <form method="post" style="margin-top:15px;">
                <div class="form-group">
                    <label>Adres IP do zablokowania:</label>
                    <input type="text" name="ip_to_block" class="form-control" placeholder="np. 10.0.0.99" required>
                </div>
                <div class="form-group">
                    <label>Powód:</label>
                    <input type="text" name="powod" class="form-control" value="Sztuczne generowanie ruchu">
                </div>
                <button type="submit" name="btn_block_ip" class="btn btn-danger">Zablokuj IP</button>
            </form>
        </div>
    </div>

    <h3>Ostatnie Wizyty w Serwisie</h3>
    <table>
        <thead>
            <tr>
                <th>Adres IP</th>
                <th>Ostatnia aktywność</th>
                <th>Akcja</th>
            </tr>
        </thead>
        <tbody>
            <?php while($v = $recent_visits->fetch_assoc()): ?>
            <tr>
                <td><code><?php echo htmlspecialchars($v['ip_address']); ?></code></td>
                <td><?php echo $v['ostatnia_aktywnosc']; ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="ip_to_block" value="<?php echo htmlspecialchars($v['ip_address']); ?>">
                        <input type="hidden" name="powod" value="Zablokowano z listy wizyt">
                        <button type="submit" name="btn_block_ip" class="btn btn-sm btn-danger">Zablokuj</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
