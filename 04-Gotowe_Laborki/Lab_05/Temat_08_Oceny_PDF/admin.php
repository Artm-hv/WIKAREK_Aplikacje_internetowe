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
    <title>Logowanie do Panelu Administratora - Portal Ocen Studentów</title>
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
$msg_error = '';

// Bezpieczny import ocen z pliku tekstowego za pomocą Prepared Statements i transakcji
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_import'])) {
    if (!isset($_FILES['plik_ocen']) || $_FILES['plik_ocen']['error'] !== UPLOAD_ERR_OK) {
        $msg_error = "Wybierz prawidłowy plik tekstowy (.txt) do importu.";
    } else {
        $lines = file($_FILES['plik_ocen']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (count($lines) < 2) {
            $msg_error = "Plik musi zawierać co najmniej nagłówek i jeden wpis z oceną.";
        } else {
            // Linia 1: kod_przedmiotu, nazwa_przedmiotu, data
            $header = explode(',', $lines[0]);
            if (count($header) < 3) {
                $msg_error = "Błędny format nagłówka pliku! Wymagany: kod, nazwa, data (np. PTI-01, Nazwa, 2026-10-15)";
            } else {
                $kod = trim($header[0]);
                $nazwa = trim($header[1]);
                $data = trim($header[2]);

                $conn->begin_transaction();
                try {
                    $stmt_ins = $conn->prepare("INSERT INTO " . TBL_PREFIX . "oceny (kod_przedmiotu, nazwa_przedmiotu, data_zaliczenia, nr_albumu, ocena, status) 
                                                VALUES (?, ?, ?, ?, ?, 'nieprzejrzana')");

                    $imported_count = 0;
                    for ($i = 1; $i < count($lines); $i++) {
                        $line = trim($lines[$i]);
                        if (empty($line)) continue;
                        $parts = explode(';', $line);
                        if (count($parts) >= 2) {
                            $album = trim($parts[0]);
                            $grade = (float)trim($parts[1]);
                            $stmt_ins->bind_param("ssssd", $kod, $nazwa, $data, $album, $grade);
                            $stmt_ins->execute();
                            $imported_count++;
                        }
                    }
                    $stmt_ins->close();
                    $conn->commit();
                    $msg_success = "Pomyślnie zaimportowano $imported_count ocen dla przedmiotu '$nazwa' ($kod)!";
                } catch (Exception $e) {
                    $conn->rollback();
                    $msg_error = "Błąd podczas importu ocen: " . $e->getMessage();
                }
            }
        }
    }
}

if (isset($_GET['usun'])) {
    $did = (int)$_GET['usun'];
    $conn->query("DELETE FROM " . TBL_PREFIX . "oceny WHERE id = $did");
    $msg_success = "Wpis oceny został usunięty.";
}

$wszystkie_oceny = $conn->query("SELECT * FROM " . TBL_PREFIX . "oceny ORDER BY kod_przedmiotu ASC, nr_albumu ASC");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Panel Prowadzącego - Wgrywanie i Raporty Ocen</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Panel Prowadzącego (Zarządzanie Ocenami)</h1>
            <div class="team-info">Zalogowany jako: <strong><?php echo htmlspecialchars($_SESSION['admin_auth']); ?></strong></div>
        </div>
        <div>
            <a href="index.php" class="btn" style="background:#27ae60;">Strona Główna</a>
            <a href="generuj_pdf.php" target="_blank" class="btn btn-success" style="margin-left:8px;">Generuj Raport PDF</a>
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
        <h3>Wgrywanie Ocen z Pliku Tekstowego (.txt)</h3>
        <p style="font-size:13px; color:#666; margin-bottom:10px;">
            Układ pliku: 1. linia: <code>kod, nazwa_przedmiotu, data</code> | Kolejne linie: <code>numer_albumu; ocena</code>
        </p>
        <form method="post" enctype="multipart/form-data" style="display:flex; gap:15px; align-items:center; flex-wrap:wrap;">
            <input type="file" name="plik_ocen" class="form-control" style="width:280px;" accept=".txt" required>
            <button type="submit" name="btn_import" class="btn">Importuj Oceny</button>
            <a href="oceny_wzorzec.txt" download class="btn" style="background:#7f8c8d;">Pobierz wzorzec pliku (.txt)</a>
        </form>
    </div>

    <h3>Zestawienie Wszystkich Ocen i Decyzji Studentów</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Przedmiot</th>
                <th>Data</th>
                <th>Album</th>
                <th>Ocena</th>
                <th>Status Wyświetlenia / Decyzji</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while($o = $wszystkie_oceny->fetch_assoc()): ?>
            <tr>
                <td><?php echo $o['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($o['nazwa_przedmiotu']); ?></strong> (<?php echo htmlspecialchars($o['kod_przedmiotu']); ?>)</td>
                <td><?php echo $o['data_zaliczenia']; ?></td>
                <td><code><?php echo htmlspecialchars($o['nr_albumu']); ?></code></td>
                <td><strong><?php echo number_format($o['ocena'], 1); ?></strong></td>
                <td>
                    <?php if ($o['status'] === 'zaakceptowana'): ?>
                        <span class="badge badge-success">Zaakceptowana</span>
                    <?php elseif ($o['status'] === 'odrzucona'): ?>
                        <span class="badge badge-danger">Odrzucona</span>
                    <?php elseif ($o['status'] === 'przejrzana'): ?>
                        <span class="badge badge-warning">Przejrzana przez studenta</span>
                    <?php else: ?>
                        <span class="badge" style="background:#7f8c8d; color:#fff;">Nieprzejrzana</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="admin.php?usun=<?php echo $o['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Usunąć ten wpis?');">Usuń</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
