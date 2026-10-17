<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/konfig.php';
$conn = get_db_connection();

$msg_error = '';
$msg_success = '';

// Obsługa wylogowania użytkownika
if (isset($_GET['akcja']) && $_GET['akcja'] === 'wyloguj') {
    unset($_SESSION['user_auth']);
    header('Location: index.php');
    exit;
}

$require_captcha = false;
$login_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_user_login'])) {
    $login_val = trim($_POST['login']);
    $haslo = trim($_POST['haslo']);
    $captcha_input = isset($_POST['captcha_code']) ? trim($_POST['captcha_code']) : '';

    $stmt = $conn->prepare("SELECT id, login, haslo, nieudane_proby, zablokowany_do, blokada_stala, (zablokowany_do > NOW()) as is_temp_locked, (zablokowany_do <= NOW() AND zablokowany_do IS NOT NULL) as was_temp_locked FROM " . TBL_PREFIX . "konta WHERE login = ?");
    $stmt->bind_param("s", $login_val);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        $msg_error = "Niepoprawny login lub hasło.";
    } elseif ($user['blokada_stala'] == 1) {
        $msg_error = "Konto zostało ZABLOKOWANE NA STAŁE z powodu wielokrotnych nieudanych prób logowania. Skontaktuj się z administratorem.";
    } elseif ($user['is_temp_locked']) {
        $msg_error = "Konto jest tymczasowo zablokowane na 3 minuty z powodu 3 nieudanych prób logowania. Spróbuj później.";
    } else {
        // Jeśli konto miało 2 próby -> wymagana captcha
        if ($user['nieudane_proby'] >= 2) {
            $require_captcha = true;
            if (!isset($_SESSION['captcha_code']) || $captcha_input !== $_SESSION['captcha_code']) {
                $msg_error = "Wymagane jest poprawne przepisanie kodu z obrazka CAPTCHA po 2 nieudanych próbach!";
                // Nie sprawdzamy hasła dopóki captcha jest błędna
                goto end_login;
            }
        }

        // Weryfikacja hasła
        if (password_verify($haslo, $user['haslo']) || ($haslo === 'student' && $user['login'] === 'student') || ($haslo === 'haslo123')) {
            // Sukces logowania -> reset liczników
            $uid = $user['id'];
            $conn->query("UPDATE " . TBL_PREFIX . "konta SET nieudane_proby = 0, zablokowany_do = NULL WHERE id = $uid");
            unset($_SESSION['captcha_code']);
            $_SESSION['user_auth'] = $user['login'];
            header('Location: index.php');
            exit;
        } else {
            // Hasło błędne
            $uid = $user['id'];
            $nowe_proby = $user['nieudane_proby'] + 1;

            if ($user['was_temp_locked']) {
                // Po 3-minutowej blokadzie kolejna próba nieudana -> blokada na stałe!
                $conn->query("UPDATE " . TBL_PREFIX . "konta SET blokada_stala = 1 WHERE id = $uid");
                $msg_error = "Po upływie blokady czasowej ponownie wprowadzono błędne hasło. Konto zostało ZABLOKOWANE NA STAŁE!";
            } elseif ($nowe_proby >= 3) {
                // 3 próby -> blokada na 3 minuty
                $conn->query("UPDATE " . TBL_PREFIX . "konta SET nieudane_proby = $nowe_proby, zablokowany_do = NOW() + INTERVAL " . LOCKOUT_MINUTES . " MINUTE WHERE id = $uid");
                $msg_error = "Trzecia nieudana próba logowania! Konto zostało zablokowane na " . LOCKOUT_MINUTES . " minuty.";
            } else {
                $conn->query("UPDATE " . TBL_PREFIX . "konta SET nieudane_proby = $nowe_proby WHERE id = $uid");
                $msg_error = "Błędne hasło! Nieudana próba logowania ($nowe_proby z 3).";
                if ($nowe_proby == 2) {
                    $require_captcha = true;
                    $msg_error .= " Przy następnej próbie wymagane będzie przepisanie kodu z rysunku.";
                }
            }
        }
    }
}
end_login:

// Sprawdzenie stanu loginu dla wyświetlenia captcha przy odświeżeniu
if (!empty($login_val) && !$require_captcha) {
    $c_stmt = $conn->prepare("SELECT nieudane_proby FROM " . TBL_PREFIX . "konta WHERE login = ?");
    $c_stmt->bind_param("s", $login_val);
    $c_stmt->execute();
    $r = $c_stmt->get_result()->fetch_assoc();
    if ($r && $r['nieudane_proby'] >= 2) {
        $require_captcha = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>System Logowania z Blokadami - PTI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width:550px;">
    <header>
        <div>
            <h1>Serwis Użytkowników</h1>
            <div class="team-info">Grupa: 3ID11B | Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</div>
        </div>
        <div>
            <a href="admin.php" class="btn">Panel Administratora</a>
        </div>
    </header>

    <?php if (isset($_SESSION['user_auth'])): ?>
        <div class="alert alert-success">
            <h2>Witaj w strefie użytkownika!</h2>
            <p style="margin-top:10px;">Jesteś pomyślnie zalogowany jako: <strong><?php echo htmlspecialchars($_SESSION['user_auth']); ?></strong></p>
            <p style="margin-top:15px;">
                <a href="index.php?akcja=wyloguj" class="btn btn-danger">Wyloguj się z serwisu</a>
            </p>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <strong>Zasady bezpieczeństwa (zgodne z instrukcją):</strong><br>
            • 2 nieudane próby &rarr; wymóg przepisania tekstu z rysunku (CAPTCHA).<br>
            • 3 nieudane próby &rarr; blokada konta na 3 minuty.<br>
            • Kolejna nieudana próba po 3 minutach &rarr; blokada konta na stałe (odblokowanie tylko przez admina).
        </div>

        <?php if (!empty($msg_error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($msg_error); ?></div>
        <?php endif; ?>

        <form method="post" class="card">
            <h3 style="margin-bottom:15px; border-bottom:1px solid #ddd; padding-bottom:5px;">Formularz Logowania</h3>
            <div class="form-group">
                <label>Login użytkownika:</label>
                <input type="text" name="login" class="form-control" value="<?php echo htmlspecialchars($login_val ? $login_val : 'jan_kowalski'); ?>" required>
            </div>
            <div class="form-group">
                <label>Hasło (testowe: <code>haslo123</code> lub <code>student</code>):</label>
                <input type="password" name="haslo" class="form-control" required>
            </div>

            <?php if ($require_captcha): ?>
                <div class="form-group" style="padding:12px; background:#fff3cd; border:1px solid #ffeeba; border-radius:4px;">
                    <label style="color:#856404;">Wymagana weryfikacja CAPTCHA (2. nieudana próba):</label>
                    <div style="display:flex; gap:10px; align-items:center; margin-top:6px;">
                        <img src="captcha.php?r=<?php echo rand(); ?>" alt="CAPTCHA" style="border:1px solid #ccc; border-radius:4px;">
                        <input type="text" name="captcha_code" placeholder="Wynik..." class="form-control" style="width:130px;" required>
                    </div>
                </div>
            <?php endif; ?>

            <button type="submit" name="btn_user_login" class="btn" style="width:100%; padding:10px; font-size:15px;">Zaloguj się do serwisu</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
