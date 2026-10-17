<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/konfig.php';
$conn = get_db_connection();

$msg_success = '';
$msg_error = '';

// Obsługa oddawania głosu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_vote'])) {
    $pozycja_id = (int)$_POST['pozycja_id'];
    $captcha_input = trim($_POST['captcha_code']);
    $user_ip = $_SERVER['REMOTE_ADDR'];

    if (!isset($_SESSION['captcha_code']) || $captcha_input !== $_SESSION['captcha_code']) {
        $msg_error = "Nieprawidłowy wynik z obrazka CAPTCHA. Spróbuj ponownie.";
    } else {
        unset($_SESSION['captcha_code']); // Jednorazowy kod
        
        // Sprawdzenie blokady IP (10 minut)
        $timeout = IP_VOTE_TIMEOUT_MIN;
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM " . TBL_PREFIX . "glosy_ip WHERE ip_address = ? AND data_glosu > NOW() - INTERVAL ? MINUTE");
        $stmt->bind_param("si", $user_ip, $timeout);
        $stmt->execute();
        $ip_cnt = $stmt->get_result()->fetch_assoc()['cnt'];
        $stmt->close();

        if ($ip_cnt > 0) {
            $msg_error = "Z Twojego adresu IP ($user_ip) oddano już głos w ciągu ostatnich $timeout minut. Prosimy odczekać.";
        } else {
            // Zapis głosu w transakcji
            $conn->begin_transaction();
            $upd = $conn->prepare("UPDATE " . TBL_PREFIX . "pozycje SET glosy = glosy + 1 WHERE id = ?");
            $upd->bind_param("i", $pozycja_id);
            $upd->execute();
            $upd->close();

            $ins = $conn->prepare("INSERT INTO " . TBL_PREFIX . "glosy_ip (pozycja_id, ip_address, data_glosu) VALUES (?, ?, NOW())");
            $ins->bind_param("is", $pozycja_id, $user_ip);
            $ins->execute();
            $ins->close();

            $conn->commit();
            $msg_success = "Twój głos został pomyślnie zarejestrowany! Dziękujemy za udział w rankingu.";
        }
    }
}

// Pobranie sumy głosów do obliczenia procentów
$sum_res = $conn->query("SELECT SUM(glosy) as total FROM " . TBL_PREFIX . "pozycje");
$total_votes = (int)$sum_res->fetch_assoc()['total'];

$pozycje = $conn->query("SELECT * FROM " . TBL_PREFIX . "pozycje ORDER BY glosy DESC, id ASC");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>System Rankingowy (Sonda) - PTI</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .progress-bar-container { background: #e9ecef; border-radius: 4px; overflow: hidden; height: 22px; width: 100%; margin: 8px 0; }
        .progress-bar-fill { background: linear-gradient(90deg, #3498db, #2ecc71); height: 100%; text-align: right; padding-right: 8px; color: white; font-weight: bold; font-size: 12px; line-height: 22px; transition: width 0.4s ease; min-width: 30px; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Ranking Technologii Webowych</h1>
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

    <div class="alert alert-info">
        Oddaj swój głos na wybraną technologię. Wymagane jest przepisanie wyniku z obrazka CAPTCHA. Ograniczenie: 1 głos na <?php echo IP_VOTE_TIMEOUT_MIN; ?> minut z jednego adresu IP. Łącznie oddano <strong><?php echo $total_votes; ?></strong> głosów.
    </div>

    <?php while($row = $pozycje->fetch_assoc()): ?>
        <?php 
            $pct = $total_votes > 0 ? round(($row['glosy'] / $total_votes) * 100, 1) : 0;
        ?>
        <div class="card" style="margin-bottom:15px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="color:#2c3e50;"><?php echo htmlspecialchars($row['nazwa']); ?></h3>
                <span class="badge badge-info" style="font-size:14px;"><?php echo $row['glosy']; ?> głosów (<?php echo $pct; ?>%)</span>
            </div>
            <p style="color:#666; font-size:14px; margin:6px 0;"><?php echo htmlspecialchars($row['opis']); ?></p>
            
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: <?php echo max(5, $pct); ?>%;">
                    <?php echo $pct; ?>%
                </div>
            </div>

            <form method="post" style="display:flex; gap:10px; align-items:center; margin-top:10px; flex-wrap:wrap;">
                <input type="hidden" name="pozycja_id" value="<?php echo $row['id']; ?>">
                <img src="captcha.php?r=<?php echo rand(); ?>" alt="CAPTCHA" style="border:1px solid #ccc; border-radius:4px; height:36px;">
                <input type="text" name="captcha_code" placeholder="Wynik..." style="width:90px; padding:7px 10px; border:1px solid #ccc; border-radius:4px;" required>
                <button type="submit" name="btn_vote" class="btn btn-sm btn-success">Głosuj na tę pozycję</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
