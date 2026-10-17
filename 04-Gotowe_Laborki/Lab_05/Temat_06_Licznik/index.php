<?php
require_once __DIR__ . '/konfig.php';
$conn = get_db_connection();

$user_ip = $_SERVER['REMOTE_ADDR'];

// Sprawdzenie czy IP zablokowane
$stmt_b = $conn->prepare("SELECT powod FROM " . TBL_PREFIX . "zablokowane_ip WHERE ip_address = ?");
$stmt_b->bind_param("s", $user_ip);
$stmt_b->execute();
$is_blocked = $stmt_b->get_result()->fetch_assoc();
$stmt_b->close();

if (!$is_blocked) {
    // Sprawdzenie debounce (czy minęło DEBOUNCE_WINDOW_MIN od ostatniej wizyty)
    $stmt_c = $conn->prepare("SELECT id FROM " . TBL_PREFIX . "wizyty_ip WHERE ip_address = ? AND ostatnia_aktywnosc > NOW() - INTERVAL " . DEBOUNCE_WINDOW_MIN . " MINUTE");
    $stmt_c->bind_param("s", $user_ip);
    $stmt_c->execute();
    $recent = $stmt_c->get_result()->fetch_assoc();
    $stmt_c->close();

    if (!$recent) {
        // Nowa unikalna wizyta -> zwiększ łączny licznik
        $conn->query("UPDATE " . TBL_PREFIX . "licznik_stat SET lacznie_odwiedzin = lacznie_odwiedzin + 1 WHERE id = 1");
    }

    // Aktualizacja czasu aktywności dla online
    $stmt_u = $conn->prepare("INSERT INTO " . TBL_PREFIX . "wizyty_ip (ip_address, ostatnia_aktywnosc) VALUES (?, NOW())");
    $stmt_u->bind_param("s", $user_ip);
    $stmt_u->execute();
    $stmt_u->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Licznik Odwiedzin (GD) - PTI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width:700px; text-align:center;">
    <header style="justify-content:space-between; text-align:left;">
        <div>
            <h1>Wizualny Licznik Odwiedzin (GD)</h1>
            <div class="team-info">Grupa: 3ID11B | Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</div>
        </div>
        <div>
            <a href="admin.php" class="btn">Panel Administratora</a>
        </div>
    </header>

    <?php if ($is_blocked): ?>
        <div class="alert alert-error">
            Twój adres IP (<code><?php echo htmlspecialchars($user_ip); ?></code>) jest zablokowany.
        </div>
    <?php endif; ?>

    <div class="alert alert-info" style="text-align:left;">
        Licznik generowany jest dynamicznie w formacie graficznym PNG za pomocą biblioteki GD. Posiada zabezpieczenie przed sztucznym nabijaniem przy odświeżaniu (debounce <?php echo DEBOUNCE_WINDOW_MIN; ?> minut dla adresu IP).
    </div>

    <div class="card" style="display:inline-block; padding:30px; margin-top:20px;">
        <h3 style="margin-bottom:15px; color:#2c3e50;">Statystyka Odwiedzin Serwisu:</h3>
        <img src="licznik_img.php?r=<?php echo rand(); ?>" alt="Graficzny Licznik Odwiedzin GD" style="border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
        <p style="margin-top:15px; font-size:13px; color:#666;">
            Twój adres IP: <strong><?php echo htmlspecialchars($user_ip); ?></strong><br>
            Odśwież stronę (F5) — łączny licznik nie zwiększy się natychmiast z uwagi na mechanizm debounce.
        </p>
    </div>
</div>
</body>
</html>
