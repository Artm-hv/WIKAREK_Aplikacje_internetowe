<?php
require_once __DIR__ . '/konfig.php';
$conn = get_db_connection();

$page = isset($_GET['strona']) ? max(1, (int)$_GET['strona']) : 1;
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

$total_res = $conn->query("SELECT COUNT(*) as cnt FROM " . TBL_PREFIX . "wiadomosci");
$total_rows = $total_res->fetch_assoc()['cnt'];
$total_pages = ceil($total_rows / $limit);

$stmt = $conn->prepare("SELECT id, tytul, autor, plik_tresci, data_dodania FROM " . TBL_PREFIX . "wiadomosci ORDER BY data_dodania DESC, id DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$wiadomosci = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>System Publikacji Wiadomości - PTI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Portal Informacyjny (Wiadomości)</h1>
            <div class="team-info">Grupa: 3ID11B | Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</div>
        </div>
        <div>
            <a href="admin.php" class="btn">Panel Administratora</a>
        </div>
    </header>

    <div class="alert alert-info">
        Wiadomości przechowywane są na dysku w plikach tekstowych HTML i porcjowane od najnowszych.
    </div>

    <?php if ($wiadomosci->num_rows > 0): ?>
        <?php while($row = $wiadomosci->fetch_assoc()): ?>
            <?php 
                $file_path = NEWS_DIR . '/' . $row['plik_tresci'];
                $content = file_exists($file_path) ? file_get_contents($file_path) : '<em>Brak pliku treści na dysku.</em>';
            ?>
            <article class="card">
                <h2 style="color:#2c3e50; font-size:20px; margin-bottom:8px;"><?php echo htmlspecialchars($row['tytul']); ?></h2>
                <div style="font-size:12px; color:#7f8c8d; margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:5px;">
                    Autor: <strong><?php echo htmlspecialchars($row['autor']); ?></strong> | Data: <?php echo htmlspecialchars($row['data_dodania']); ?> | Plik: <code><?php echo htmlspecialchars($row['plik_tresci']); ?></code>
                </div>
                <div class="news-body" style="line-height:1.6; font-size:15px;">
                    <?php echo $content; ?>
                </div>
            </article>
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
    <?php else: ?>
        <p style="text-align:center; padding:30px; color:#777;">Brak opublikowanych wiadomości.</p>
    <?php endif; ?>
</div>
</body>
</html>
