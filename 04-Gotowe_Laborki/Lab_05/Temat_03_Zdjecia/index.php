<?php
require_once __DIR__ . '/konfig.php';
$conn = get_db_connection();

$page = isset($_GET['strona']) ? max(1, (int)$_GET['strona']) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$tot = $conn->query("SELECT COUNT(*) as cnt FROM " . TBL_PREFIX . "zdjecia")->fetch_assoc()['cnt'];
$total_pages = ceil($tot / $limit);

$stmt = $conn->prepare("SELECT * FROM " . TBL_PREFIX . "zdjecia ORDER BY data_wgrania DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$zdjecia = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Galeria Zdjęć z Miniaturami GD - PTI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <h1>Galeria Zdjęć (Miniatury GD)</h1>
            <div class="team-info">Grupa: 3ID11B | Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</div>
        </div>
        <div>
            <a href="admin.php" class="btn">Panel Administratora (Upload)</a>
        </div>
    </header>

    <div class="alert alert-info">
        System automatycznie generuje kwadratowe miniatury 150x150 px za pomocą biblioteki GD (<code>imagecopyresampled</code>). Kliknij miniaturę, aby otworzyć oryginalne zdjęcie w pełnym rozmiarze.
    </div>

    <?php if ($zdjecia->num_rows > 0): ?>
        <div class="gallery-grid">
            <?php while($row = $zdjecia->fetch_assoc()): ?>
                <div class="gallery-item">
                    <a href="galeria_oryginaly/<?php echo htmlspecialchars($row['plik_oryginal']); ?>" target="_blank" title="Kliknij, aby powiększyć">
                        <img src="galeria_miniaturki/<?php echo htmlspecialchars($row['plik_miniaturka']); ?>" alt="<?php echo htmlspecialchars($row['tytul']); ?>">
                    </a>
                    <div class="title"><?php echo htmlspecialchars($row['tytul']); ?></div>
                    <div style="font-size:11px; color:#888; margin-top:3px;"><?php echo $row['rozmiar_kb']; ?> KB | <?php echo date('d.m.Y', strtotime($row['data_wgrania'])); ?></div>
                </div>
            <?php endwhile; ?>
        </div>

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
        <p style="text-align:center; padding:40px; color:#777;">Galeria jest obecnie pusta. Zaloguj się do panelu administratora, aby dodać zdjęcia.</p>
    <?php endif; ?>
</div>
</body>
</html>
