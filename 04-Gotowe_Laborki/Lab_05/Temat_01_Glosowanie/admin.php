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
    <title>Logowanie do Panelu Administratora - System Publikacji Wiadomości</title>
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
    $tytul = trim($_POST['tytul']);
    $autor = trim($_POST['autor']);
    $tresc_html = trim($_POST['tresc_html']);

    if (!empty($tytul) && !empty($autor) && !empty($tresc_html)) {
        $stmt = $conn->prepare("INSERT INTO " . TBL_PREFIX . "wiadomosci (tytul, autor, plik_tresci, data_dodania) VALUES (?, ?, '', NOW())");
        $stmt->bind_param("ss", $tytul, $autor);
        if ($stmt->execute()) {
            $new_id = $conn->insert_id;
            $file_name = "msg_" . $new_id . ".txt";
            file_put_contents(NEWS_DIR . '/' . $file_name, $tresc_html);
            $conn->query("UPDATE " . TBL_PREFIX . "wiadomosci SET plik_tresci = '$file_name' WHERE id = $new_id");
            $msg_success = "Wiadomość została pomyślnie dodana!";
        } else {
            $msg_error = "Błąd bazy danych przy zapisie wiadomości.";
        }
        $stmt->close();
    } else {
        $msg_error = "Wypełnij wszystkie pola formularza!";
    }
}

$edit_row = null;
$edit_content = '';
if (isset($_GET['edycja'])) {
    $edit_id = (int)$_GET['edycja'];
    $res = $conn->query("SELECT * FROM " . TBL_PREFIX . "wiadomosci WHERE id = $edit_id");
    if ($edit_row = $res->fetch_assoc()) {
        $fp = NEWS_DIR . '/' . $edit_row['plik_tresci'];
        if (file_exists($fp)) {
            $edit_content = file_get_contents($fp);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_update'])) {
    $id = (int)$_POST['id'];
    $tytul = trim($_POST['tytul']);
    $autor = trim($_POST['autor']);
    $tresc_html = trim($_POST['tresc_html']);

    $res = $conn->query("SELECT plik_tresci FROM " . TBL_PREFIX . "wiadomosci WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        file_put_contents(NEWS_DIR . '/' . $row['plik_tresci'], $tresc_html);
        $stmt = $conn->prepare("UPDATE " . TBL_PREFIX . "wiadomosci SET tytul = ?, autor = ? WHERE id = ?");
        $stmt->bind_param("ssi", $tytul, $autor, $id);
        $stmt->execute();
        $stmt->close();
        $msg_success = "Wiadomość została zaktualizowana!";
        $edit_row = null;
    }
}

if (isset($_GET['usun'])) {
    $del_id = (int)$_GET['usun'];
    $res = $conn->query("SELECT plik_tresci FROM " . TBL_PREFIX . "wiadomosci WHERE id = $del_id");
    if ($row = $res->fetch_assoc()) {
        $fp = NEWS_DIR . '/' . $row['plik_tresci'];
        if (file_exists($fp)) {
            unlink($fp);
        }
        $conn->query("DELETE FROM " . TBL_PREFIX . "wiadomosci WHERE id = $del_id");
        $msg_success = "Wiadomość oraz plik tekstowy zostały usunięte.";
    }
}

$all_news = $conn->query("SELECT * FROM " . TBL_PREFIX . "wiadomosci ORDER BY data_dodania DESC");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Panel Administratora - Publikacja Wiadomości</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Zarządzanie Wiadomościami</h1>
            <div class="team-info">Zalogowany jako: <strong><?php echo htmlspecialchars($_SESSION['admin_auth']); ?></strong> | Grupa: 3ID11B</div>
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
        <h3><?php echo $edit_row ? 'Edycja wiadomości #' . $edit_row['id'] : 'Dodaj nową wiadomość'; ?></h3>
        <form method="post" style="margin-top:15px;">
            <?php if ($edit_row): ?>
                <input type="hidden" name="id" value="<?php echo $edit_row['id']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label>Tytuł wiadomości:</label>
                <input type="text" name="tytul" class="form-control" value="<?php echo htmlspecialchars($edit_row ? $edit_row['tytul'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Autor:</label>
                <input type="text" name="autor" class="form-control" value="<?php echo htmlspecialchars($edit_row ? $edit_row['autor'] : $_SESSION['admin_auth']); ?>" required>
            </div>
            <div class="form-group">
                <label>Treść wiadomości (format HTML):</label>
                <textarea name="tresc_html" class="form-control" rows="6" required><?php echo htmlspecialchars($edit_row ? $edit_content : '<p>Wpisz tutaj treść wiadomości z formatowaniem HTML...</p>'); ?></textarea>
            </div>
            <?php if ($edit_row): ?>
                <button type="submit" name="btn_update" class="btn btn-success">Zapisz Zmiany</button>
                <a href="admin.php" class="btn" style="background:#7f8c8d; margin-left:10px;">Anuluj Edycję</a>
            <?php else: ?>
                <button type="submit" name="btn_add" class="btn">Opublikuj Wiadomość</button>
            <?php endif; ?>
        </form>
    </div>

    <h3>Wszystkie Opublikowane Wiadomości</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tytuł</th>
                <th>Autor</th>
                <th>Plik na dysku</th>
                <th>Data</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while($n = $all_news->fetch_assoc()): ?>
            <tr>
                <td><?php echo $n['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($n['tytul']); ?></strong></td>
                <td><?php echo htmlspecialchars($n['autor']); ?></td>
                <td><code><?php echo htmlspecialchars($n['plik_tresci']); ?></code></td>
                <td><?php echo htmlspecialchars($n['data_dodania']); ?></td>
                <td>
                    <a href="admin.php?edycja=<?php echo $n['id']; ?>" class="btn btn-sm">Edytuj</a>
                    <a href="admin.php?usun=<?php echo $n['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Usunąć tę wiadomość i powiązany plik?');">Usuń</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
