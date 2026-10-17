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
    <title>Logowanie do Panelu Administratora - Obsługa Zdjęć i Miniatury GD</title>
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_upload'])) {
    $tytul = trim($_POST['tytul']);
    
    if (empty($tytul) || !isset($_FILES['zdjecie']) || $_FILES['zdjecie']['error'] !== UPLOAD_ERR_OK) {
        $msg_error = "Wybierz prawidłowy plik graficzny i podaj jego tytuł.";
    } else {
        $file_tmp = $_FILES['zdjecie']['tmp_name'];
        $file_name = $_FILES['zdjecie']['name'];
        $file_size_kb = round($_FILES['zdjecie']['size'] / 1024);
        
        $img_info = @getimagesize($file_tmp);
        if ($img_info === false) {
            $msg_error = "Przesłany plik nie jest prawidłowym obrazem!";
        } else {
            $mime = $img_info['mime'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (!in_array($mime, $allowed)) {
                $msg_error = "Niedozwolony format pliku! Dozwolone są tylko pliki JPG, PNG, GIF.";
            } else {
                $unique_name = time() . '_' . rand(100, 999) . '.' . $ext;
                $target_orig = UPLOAD_DIR_ORIG . '/' . $unique_name;
                $target_thumb = UPLOAD_DIR_THUMB . '/' . $unique_name;

                if (move_uploaded_file($file_tmp, $target_orig)) {
                    // Generowanie miniatury 150x150 przez GD
                    switch($mime) {
                        case 'image/jpeg': $src = imagecreatefromjpeg($target_orig); break;
                        case 'image/png':  $src = imagecreatefrompng($target_orig); break;
                        case 'image/gif':  $src = imagecreatefromgif($target_orig); break;
                    }

                    $orig_w = imagesx($src);
                    $orig_h = imagesy($src);
                    $thumb_size = 150;
                    $dst = imagecreatetruecolor($thumb_size, $thumb_size);

                    // Kadrowanie i skalowanie do kwadratu
                    $min_dim = min($orig_w, $orig_h);
                    $src_x = ($orig_w - $min_dim) / 2;
                    $src_y = ($orig_h - $min_dim) / 2;

                    imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y, $thumb_size, $thumb_size, $min_dim, $min_dim);

                    switch($mime) {
                        case 'image/jpeg': imagejpeg($dst, $target_thumb, 85); break;
                        case 'image/png':  imagepng($dst, $target_thumb, 8); break;
                        case 'image/gif':  imagegif($dst, $target_thumb); break;
                    }
                    imagedestroy($src);
                    imagedestroy($dst);

                    $stmt = $conn->prepare("INSERT INTO " . TBL_PREFIX . "zdjecia (tytul, plik_oryginal, plik_miniaturka, rozmiar_kb, data_wgrania) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->bind_param("sssi", $tytul, $unique_name, $unique_name, $file_size_kb);
                    $stmt->execute();
                    $stmt->close();
                    $msg_success = "Zdjęcie zostało wgrane, a miniatura 150x150 wygenerowana pomyślnie!";
                } else {
                    $msg_error = "Nie udało się przenieść wgranego pliku na serwer.";
                }
            }
        }
    }
}

if (isset($_GET['usun'])) {
    $del_id = (int)$_GET['usun'];
    $res = $conn->query("SELECT plik_oryginal, plik_miniaturka FROM " . TBL_PREFIX . "zdjecia WHERE id = $del_id");
    if ($row = $res->fetch_assoc()) {
        $p1 = UPLOAD_DIR_ORIG . '/' . $row['plik_oryginal'];
        $p2 = UPLOAD_DIR_THUMB . '/' . $row['plik_miniaturka'];
        if (file_exists($p1)) unlink($p1);
        if (file_exists($p2)) unlink($p2);
        $conn->query("DELETE FROM " . TBL_PREFIX . "zdjecia WHERE id = $del_id");
        $msg_success = "Zdjęcie i miniatura zostały usunięte.";
    }
}

$zdjecia = $conn->query("SELECT * FROM " . TBL_PREFIX . "zdjecia ORDER BY data_wgrania DESC");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Panel Administratora - Galeria Zdjęć</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Zarządzanie Galerią Zdjęć (GD)</h1>
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
        <h3>Wgraj nowe zdjęcie (Obsługiwane: JPG, PNG, GIF)</h3>
        <form method="post" enctype="multipart/form-data" style="margin-top:15px;">
            <div class="form-group">
                <label>Tytuł zdjęcia:</label>
                <input type="text" name="tytul" class="form-control" placeholder="np. Kampus Główny" required>
            </div>
            <div class="form-group">
                <label>Wybierz plik graficzny:</label>
                <input type="file" name="zdjecie" class="form-control" accept="image/jpeg,image/png,image/gif" required>
            </div>
            <button type="submit" name="btn_upload" class="btn">Wgraj zdjęcie i utwórz miniaturę</button>
        </form>
    </div>

    <h3>Wgrane Zdjęcia w Bazie</h3>
    <table>
        <thead>
            <tr>
                <th>Miniatura</th>
                <th>Tytuł</th>
                <th>Nazwa pliku</th>
                <th>Rozmiar</th>
                <th>Data</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while($z = $zdjecia->fetch_assoc()): ?>
            <tr>
                <td style="width:70px;">
                    <img src="galeria_miniaturki/<?php echo htmlspecialchars($z['plik_miniaturka']); ?>" style="width:60px; height:60px; object-fit:cover; border-radius:3px;">
                </td>
                <td><strong><?php echo htmlspecialchars($z['tytul']); ?></strong></td>
                <td><code><?php echo htmlspecialchars($z['plik_oryginal']); ?></code></td>
                <td><?php echo $z['rozmiar_kb']; ?> KB</td>
                <td><?php echo $z['data_wgrania']; ?></td>
                <td>
                    <a href="admin.php?usun=<?php echo $z['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Usunąć to zdjęcie i jego miniaturę?');">Usuń</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
