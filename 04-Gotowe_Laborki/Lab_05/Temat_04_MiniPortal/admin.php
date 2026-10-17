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
    <title>Logowanie do Panelu Administratora - Mini Portal</title>
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save'])) {
    $nazwa = trim($_POST['nazwa']);
    $typ = $_POST['typ'];
    $zawartosc = trim($_POST['zawartosc']);
    $kolejnosc = (int)$_POST['kolejnosc'];

    if (!empty($nazwa) && !empty($zawartosc)) {
        if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
            $eid = (int)$_POST['edit_id'];
            $stmt = $conn->prepare("UPDATE " . TBL_PREFIX . "menu SET nazwa = ?, typ = ?, zawartosc = ?, kolejnosc = ? WHERE id = ?");
            $stmt->bind_param("sssii", $nazwa, $typ, $zawartosc, $kolejnosc, $eid);
            $stmt->execute();
            $msg_success = "Pozycja menu została zaktualizowana!";
        } else {
            $stmt = $conn->prepare("INSERT INTO " . TBL_PREFIX . "menu (nazwa, typ, zawartosc, kolejnosc) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $nazwa, $typ, $zawartosc, $kolejnosc);
            $stmt->execute();
            $msg_success = "Dodano nową pozycję do menu!";
        }
        $stmt->close();
    } else {
        $msg_error = "Wypełnij wszystkie pola formularza!";
    }
}

if (isset($_GET['usun'])) {
    $did = (int)$_GET['usun'];
    $conn->query("DELETE FROM " . TBL_PREFIX . "menu WHERE id = $did");
    $msg_success = "Pozycja menu została usunięta.";
}

$edit_item = null;
if (isset($_GET['edycja'])) {
    $eid = (int)$_GET['edycja'];
    $edit_item = $conn->query("SELECT * FROM " . TBL_PREFIX . "menu WHERE id = $eid")->fetch_assoc();
}

$items = $conn->query("SELECT * FROM " . TBL_PREFIX . "menu ORDER BY kolejnosc ASC, id ASC");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Panel Administratora - Mini Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Zarządzanie Menu Portalu</h1>
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
    <?php if (!empty($msg_error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($msg_error); ?></div>
    <?php endif; ?>

    <div class="card">
        <h3><?php echo $edit_item ? 'Edycja pozycji menu #' . $edit_item['id'] : 'Dodaj nową pozycję menu'; ?></h3>
        <form method="post" style="margin-top:15px;">
            <?php if ($edit_item): ?>
                <input type="hidden" name="edit_id" value="<?php echo $edit_item['id']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label>Nazwa w menu:</label>
                <input type="text" name="nazwa" class="form-control" value="<?php echo htmlspecialchars($edit_item ? $edit_item['nazwa'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Typ zawartości:</label>
                <select name="typ" class="form-control">
                    <option value="html" <?php echo ($edit_item && $edit_item['typ'] === 'html') ? 'selected' : ''; ?>>Kod HTML (bezpośrednio w oknie)</option>
                    <option value="link_wew" <?php echo ($edit_item && $edit_item['typ'] === 'link_wew') ? 'selected' : ''; ?>>Link wewnętrzny (ścieżka do pliku np. podstrony/kontakt.html)</option>
                    <option value="link_zew" <?php echo ($edit_item && $edit_item['typ'] === 'link_zew') ? 'selected' : ''; ?>>Link zewnętrzny (adres URL w ramce iframe)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Zawartość / Ścieżka / Adres URL:</label>
                <textarea name="zawartosc" class="form-control" rows="4" required><?php echo htmlspecialchars($edit_item ? $edit_item['zawartosc'] : '<p>Wpisz treść HTML...</p>'); ?></textarea>
            </div>
            <div class="form-group">
                <label>Kolejność wyświetlania (liczba):</label>
                <input type="number" name="kolejnosc" class="form-control" value="<?php echo $edit_item ? $edit_item['kolejnosc'] : 1; ?>" required>
            </div>
            <button type="submit" name="btn_save" class="btn"><?php echo $edit_item ? 'Zapisz zmiany' : 'Dodaj do menu'; ?></button>
            <?php if ($edit_item): ?>
                <a href="admin.php" class="btn" style="background:#7f8c8d; margin-left:10px;">Anuluj</a>
            <?php endif; ?>
        </form>
    </div>

    <h3>Zdefiniowane Pozycje Menu</h3>
    <table>
        <thead>
            <tr>
                <th>Kolejność</th>
                <th>Nazwa</th>
                <th>Typ</th>
                <th>Zawartość / Ścieżka</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while($m = $items->fetch_assoc()): ?>
            <tr>
                <td><strong><?php echo $m['kolejnosc']; ?></strong></td>
                <td><?php echo htmlspecialchars($m['nazwa']); ?></td>
                <td><span class="badge badge-info"><?php echo $m['typ']; ?></span></td>
                <td><code><?php echo htmlspecialchars(substr($m['zawartosc'], 0, 60)) . (strlen($m['zawartosc']) > 60 ? '...' : ''); ?></code></td>
                <td>
                    <a href="admin.php?edycja=<?php echo $m['id']; ?>" class="btn btn-sm">Edytuj</a>
                    <a href="admin.php?usun=<?php echo $m['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Usunąć tę pozycję z menu?');">Usuń</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
