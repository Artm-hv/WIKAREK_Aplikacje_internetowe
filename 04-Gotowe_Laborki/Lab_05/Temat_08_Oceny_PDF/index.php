<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/konfig.php';
$conn = get_db_connection();

$album = isset($_SESSION['current_student_album']) ? $_SESSION['current_student_album'] : '';
$msg_success = '';
$msg_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_search_album'])) {
    $album = trim($_POST['nr_albumu']);
    $_SESSION['current_student_album'] = $album;
}

// Bezpieczna zmiana statusu oceny (tylko dla ocen należących do bieżącego albumu!)
if (isset($_GET['akcja']) && isset($_GET['id']) && !empty($album)) {
    $action = $_GET['akcja'];
    $oid = (int)$_GET['id'];
    
    if ($action === 'akceptuj' || $action === 'odrzuc') {
        $new_status = ($action === 'akceptuj') ? 'zaakceptowana' : 'odrzucona';
        $stmt_upd = $conn->prepare("UPDATE " . TBL_PREFIX . "oceny SET status = ? WHERE id = ? AND nr_albumu = ?");
        $stmt_upd->bind_param("sis", $new_status, $oid, $album);
        $stmt_upd->execute();
        $stmt_upd->close();
        $msg_success = "Status oceny został zmieniony na: $new_status";
    }
}

// Jeśli podano album -> oznacz nieprzejrzane oceny jako 'przejrzana' (wymóg śledzenia czy wyświetliła się ocena)
if (!empty($album)) {
    $stmt_view = $conn->prepare("UPDATE " . TBL_PREFIX . "oceny SET status = 'przejrzana' WHERE nr_albumu = ? AND status = 'nieprzejrzana'");
    $stmt_view->bind_param("s", $album);
    $stmt_view->execute();
    $stmt_view->close();
}

$oceny = null;
if (!empty($album)) {
    $stmt = $conn->prepare("SELECT * FROM " . TBL_PREFIX . "oceny WHERE nr_albumu = ? ORDER BY nazwa_przedmiotu ASC, data_zaliczenia ASC");
    $stmt->bind_param("s", $album);
    $stmt->execute();
    $oceny = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Portal Ocen Studentów - PTI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Portal Wyników i Ocen Studentów</h1>
            <div class="team-info">Grupa: 3ID11B | Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</div>
        </div>
        <div>
            <a href="admin.php" class="btn">Panel Prowadzącego</a>
        </div>
    </header>

    <?php if (!empty($msg_success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg_success); ?></div>
    <?php endif; ?>

    <div class="card">
        <h3>Sprawdź swoje oceny</h3>
        <form method="post" style="display:flex; gap:10px; margin-top:15px; align-items:center;">
            <label style="margin:0; font-weight:bold;">Numer albumu:</label>
            <input type="text" name="nr_albumu" class="form-control" style="width:160px;" placeholder="np. 97882" value="<?php echo htmlspecialchars($album); ?>" required>
            <button type="submit" name="btn_search_album" class="btn">Wyświetl oceny</button>
            <span style="font-size:13px; color:#666; margin-left:10px;">(Testowe albumy: <strong>97882</strong>, <strong>97889</strong>)</span>
        </form>
    </div>

    <?php if (!empty($album)): ?>
        <h3>Twoje Oceny (Album: <?php echo htmlspecialchars($album); ?>)</h3>
        <p style="font-size:13px; color:#666; margin-bottom:15px;">
            Oceny posortowane według przedmiotu, a następnie terminu zaliczenia. Wyświetlenie ocen automatycznie odnotowuje status <strong>przejrzana</strong>.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Kod</th>
                    <th>Nazwa Przedmiotu</th>
                    <th>Termin / Data</th>
                    <th>Ocena</th>
                    <th>Status Decyzji</th>
                    <th>Twoja Decyzja</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($oceny && $oceny->num_rows > 0): ?>
                    <?php while($row = $oceny->fetch_assoc()): ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($row['kod_przedmiotu']); ?></code></td>
                        <td><strong><?php echo htmlspecialchars($row['nazwa_przedmiotu']); ?></strong></td>
                        <td><?php echo $row['data_zaliczenia']; ?></td>
                        <td><span class="badge badge-info" style="font-size:14px;"><?php echo number_format($row['ocena'], 1); ?></span></td>
                        <td>
                            <?php if ($row['status'] === 'zaakceptowana'): ?>
                                <span class="badge badge-success">Zaakceptowana</span>
                            <?php elseif ($row['status'] === 'odrzucona'): ?>
                                <span class="badge badge-danger">Odrzucona</span>
                            <?php elseif ($row['status'] === 'przejrzana'): ?>
                                <span class="badge badge-warning">Przejrzana (Oczekuje na decyzję)</span>
                            <?php else: ?>
                                <span class="badge" style="background:#7f8c8d; color:#fff;">Nieprzejrzana</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] !== 'zaakceptowana' && $row['status'] !== 'odrzucona'): ?>
                                <a href="index.php?akcja=akceptuj&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Akceptuj ocenę</a>
                                <a href="index.php?akcja=odrzuc&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz odrzucić tę ocenę?');">Odrzuć</a>
                            <?php else: ?>
                                <span style="font-size:12px; color:#666;">Decyzja podjęta</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; padding:20px; color:#888;">Brak wpisanych ocen dla tego numeru albumu.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
