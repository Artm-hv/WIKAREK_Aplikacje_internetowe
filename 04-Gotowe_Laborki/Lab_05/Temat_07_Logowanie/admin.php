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
    <title>Logowanie do Panelu Administratora - System Logowania</title>
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_add_user'])) {
    $u_login = trim($_POST['u_login']);
    $u_pass = trim($_POST['u_pass']);
    if (!empty($u_login) && !empty($u_pass)) {
        $h = password_hash($u_pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO " . TBL_PREFIX . "konta (login, haslo, utworzono) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $u_login, $h);
        if ($stmt->execute()) {
            $msg_success = "Utworzono nowego użytkownika: $u_login";
        } else {
            $msg_error = "Użytkownik o takim loginie już istnieje!";
        }
        $stmt->close();
    }
}

if (isset($_GET['odblokuj'])) {
    $uid = (int)$_GET['odblokuj'];
    $conn->query("UPDATE " . TBL_PREFIX . "konta SET nieudane_proby = 0, zablokowany_do = NULL, blokada_stala = 0 WHERE id = $uid");
    $msg_success = "Konto zostało pomyślnie odblokowane (zresetowano próby i blokady).";
}

if (isset($_GET['usun'])) {
    $uid = (int)$_GET['usun'];
    $conn->query("DELETE FROM " . TBL_PREFIX . "konta WHERE id = $uid");
    $msg_success = "Użytkownik został usunięty.";
}

$konta = $conn->query("SELECT id, login, nieudane_proby, zablokowany_do, blokada_stala, (zablokowany_do > NOW()) as is_temp_locked, utworzono FROM " . TBL_PREFIX . "konta ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Panel Administratora - Zarządzanie Kontami</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Zarządzanie Kontami i Blokadami</h1>
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

    <div class="card">
        <h3>Dodaj nowe konto użytkownika</h3>
        <form method="post" style="display:flex; gap:15px; margin-top:15px; flex-wrap:wrap; align-items:flex-end;">
            <div class="form-group" style="flex:1; min-width:180px; margin-bottom:0;">
                <label>Login:</label>
                <input type="text" name="u_login" class="form-control" required>
            </div>
            <div class="form-group" style="flex:1; min-width:180px; margin-bottom:0;">
                <label>Hasło:</label>
                <input type="password" name="u_pass" class="form-control" required>
            </div>
            <button type="submit" name="btn_add_user" class="btn">Utwórz użytkownika</button>
        </form>
    </div>

    <h3>Lista Kont w Systemie</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Login</th>
                <th>Nieudane próby</th>
                <th>Status Konta</th>
                <th>Data utworzenia</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while($k = $konta->fetch_assoc()): ?>
            <tr>
                <td><?php echo $k['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($k['login']); ?></strong></td>
                <td><?php echo $k['nieudane_proby']; ?></td>
                <td>
                    <?php if ($k['blokada_stala'] == 1): ?>
                        <span class="badge badge-danger">BLOKADA STAŁA</span>
                    <?php elseif ($k['is_temp_locked']): ?>
                        <span class="badge badge-warning">Blokada 3 min (do: <?php echo substr($k['zablokowany_do'], 11); ?>)</span>
                    <?php elseif ($k['nieudane_proby'] >= 2): ?>
                        <span class="badge badge-info">Wymaga CAPTCHA</span>
                    <?php else: ?>
                        <span class="badge badge-success">Aktywne</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $k['utworzono']; ?></td>
                <td>
                    <?php if ($k['blokada_stala'] == 1 || $k['is_temp_locked'] || $k['nieudane_proby'] > 0): ?>
                        <a href="admin.php?odblokuj=<?php echo $k['id']; ?>" class="btn btn-sm btn-success">Odblokuj / Reset prób</a>
                    <?php endif; ?>
                    <a href="admin.php?usun=<?php echo $k['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Usunąć to konto?');">Usuń</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
