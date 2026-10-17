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
        if ($row['rola'] === 'admin' && password_verify($haslo, $row['haslo'])) {
            session_regenerate_id(true);
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
    <title>Logowanie do Panelu Administratora - System Rankingowy (Sonda)</title>
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
            <input type="password" name="haslo" class="form-control" value="" required autocomplete="current-password">
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


$conn = get_db_connection();
$msg_success = '';
$msg_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_add'])) {
    $nazwa = trim($_POST['nazwa']);
    $opis = trim($_POST['opis']);
    if (!empty($nazwa) && !empty($opis)) {
        $stmt = $conn->prepare("INSERT INTO " . TBL_PREFIX . "pozycje (nazwa, opis, glosy) VALUES (?, ?, 0)");
        $stmt->bind_param("ss", $nazwa, $opis);
        $stmt->execute();
        $stmt->close();
        $msg_success = "Dodano nową pozycję do rankingu!";
    }
}

if (isset($_GET['reset_all'])) {
    $conn->query("UPDATE " . TBL_PREFIX . "pozycje SET glosy = 0");
    $conn->query("TRUNCATE TABLE " . TBL_PREFIX . "glosy_ip");
    $msg_success = "Zresetowano wszystkie głosy i historię adresów IP.";
}

if (isset($_GET['usun'])) {
    $id = (int)$_GET['usun'];
    $conn->query("DELETE FROM " . TBL_PREFIX . "pozycje WHERE id = $id");
    $conn->query("DELETE FROM " . TBL_PREFIX . "glosy_ip WHERE pozycja_id = $id");
    $msg_success = "Pozycja została usunięta.";
}

$pozycje = $conn->query("SELECT * FROM " . TBL_PREFIX . "pozycje ORDER BY glosy DESC");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Panel Administratora - Ranking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Zarządzanie Rankingiem i Sondą</h1>
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
        <h3>Dodaj nową pozycję do rankingu</h3>
        <form method="post" style="margin-top:15px;">
            <div class="form-group">
                <label>Nazwa pozycji / technologii:</label>
                <input type="text" name="nazwa" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Krótki opis:</label>
                <textarea name="opis" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" name="btn_add" class="btn">Dodaj pozycję</button>
            <a href="admin.php?reset_all=1" class="btn btn-danger" style="margin-left:15px;" onclick="return confirm('Czy na pewno chcesz zresetować wszystkie głosy do 0?');">Zresetuj wszystkie głosy</a>
        </form>
    </div>

    <h3>Aktualne Pozycje w Rankingu</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>Opis</th>
                <th>Liczba głosów</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while($p = $pozycje->fetch_assoc()): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($p['nazwa']); ?></strong></td>
                <td><?php echo htmlspecialchars($p['opis']); ?></td>
                <td><span class="badge badge-info"><?php echo $p['glosy']; ?></span></td>
                <td>
                    <a href="admin.php?usun=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Usunąć tę pozycję?');">Usuń</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
