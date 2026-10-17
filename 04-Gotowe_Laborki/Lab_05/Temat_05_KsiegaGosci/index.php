<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/konfig.php';
$conn = get_db_connection();

$msg_success = '';
$msg_error = '';
$user_ip = $_SERVER['REMOTE_ADDR'];

// Sprawdzenie czy IP jest zablokowane
$stmt_b = $conn->prepare("SELECT powod FROM " . TBL_PREFIX . "zablokowane_ip WHERE ip_address = ?");
$stmt_b->bind_param("s", $user_ip);
$stmt_b->execute();
$is_blocked = $stmt_b->get_result()->fetch_assoc();
$stmt_b->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_add'])) {
    if ($is_blocked) {
        $msg_error = "Twój adres IP ($user_ip) został zablokowany przez administratora. Powód: " . htmlspecialchars($is_blocked['powod']);
    } else {
        $autor = trim($_POST['autor']);
        $email = trim($_POST['email']);
        $wpis = trim($_POST['wpis']);
        $captcha = trim($_POST['captcha_code']);

        if (!isset($_SESSION['captcha_code']) || $captcha !== $_SESSION['captcha_code']) {
            $msg_error = "Nieprawidłowy kod z obrazka CAPTCHA!";
        } elseif (empty($autor) || empty($wpis)) {
            $msg_error = "Wypełnij wymagane pola (Imię/Pseudonim oraz Treść wpisu).";
        } else {
            unset($_SESSION['captcha_code']);
            $stmt = $conn->prepare("INSERT INTO " . TBL_PREFIX . "wpisy (autor, email, wpis, ip_address, data_wpisu) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $autor, $email, $wpis, $user_ip);
            $stmt->execute();
            $stmt->close();
            $msg_success = "Twój wpis został pomyślnie dodany do księgi gości!";
        }
    }
}

$page = isset($_GET['strona']) ? max(1, (int)$_GET['strona']) : 1;
$limit = GUESTBOOK_PER_PAGE;
$offset = ($page - 1) * $limit;

$tot = $conn->query("SELECT COUNT(*) as cnt FROM " . TBL_PREFIX . "wpisy")->fetch_assoc()['cnt'];
$total_pages = ceil($tot / $limit);

$wpisy = $conn->query("SELECT * FROM " . TBL_PREFIX . "wpisy ORDER BY data_wpisu DESC LIMIT $limit OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Księga Gości - PTI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Księga Gości</h1>
            <div class="team-info">Grupa: 3ID11B | Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</div>
        </div>
        <div>
            <a href="admin.php" class="btn">Panel Administratora</a>
        </div>
    </header>

    <?php if (!empty($msg_success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg_success); ?></div>
    <?php endif; ?>
    <?php if (!empty($msg_error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($msg_error); ?></div>
    <?php endif; ?>

    <?php if ($is_blocked): ?>
        <div class="alert alert-error">
            <strong>Dostęp zablokowany:</strong> Twój adres IP (<code><?php echo htmlspecialchars($user_ip); ?></code>) znajduje się na czarnej liście. Nie możesz dodawać wpisów.
        </div>
    <?php else: ?>
        <div class="card">
            <h3>Dodaj nowy wpis do księgi</h3>
            <form method="post" style="margin-top:15px;">
                <div class="form-group">
                    <label>Twój podpis (Pseudonim / Imię i nazwisko):</label>
                    <input type="text" name="autor" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Adres e-mail (opcjonalnie):</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Treść wpisu:</label>
                    <textarea name="wpis" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group" style="display:flex; gap:10px; align-items:center;">
                    <img src="captcha.php?r=<?php echo rand(); ?>" alt="CAPTCHA" style="border:1px solid #ccc; border-radius:4px;">
                    <input type="text" name="captcha_code" placeholder="Wynik z obrazka" class="form-control" style="width:140px;" required>
                    <button type="submit" name="btn_add" class="btn btn-success">Opublikuj wpis</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <h3>Wpisy w Księdze (Łącznie: <?php echo $tot; ?>)</h3>
    <?php while($row = $wpisy->fetch_assoc()): ?>
        <div class="card" style="margin-bottom:12px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <strong><?php echo htmlspecialchars($row['autor']); ?></strong>
                <span style="font-size:12px; color:#777;"><?php echo $row['data_wpisu']; ?></span>
            </div>
            <p style="font-size:14px; color:#333; line-height:1.5;"><?php echo nl2br(htmlspecialchars($row['wpis'])); ?></p>
        </div>
    <?php endwhile; ?>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="index.php?strona=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
