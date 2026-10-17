<?php
require_once __DIR__ . '/konfig.php';
$conn = get_db_connection();

$menu_items = $conn->query("SELECT * FROM " . TBL_PREFIX . "menu ORDER BY kolejnosc ASC, id ASC");

// Wybrana pozycja
$selected_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$stmt = $conn->prepare("SELECT * FROM " . TBL_PREFIX . "menu WHERE id = ?");
$stmt->bind_param("i", $selected_id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();
if (!$current) {
    $current = ['nazwa' => 'Nie znaleziono', 'typ' => 'html', 'zawartosc' => '<p>Wybrana pozycja menu nie istnieje.</p>'];
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Mini Portal - <?php echo htmlspecialchars($current['nazwa']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width:1050px;">
    <header>
        <div>
            <h1>Mini Portal Internetowy (MySQL)</h1>
            <div class="team-info">Grupa: 3ID11B | Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</div>
        </div>
        <div>
            <a href="admin.php" class="btn">Zarządzaj Menu</a>
        </div>
    </header>

    <div class="portal-layout">
        <aside class="portal-menu">
            <h3>Nawigacja Portalu</h3>
            <ul>
                <?php while($item = $menu_items->fetch_assoc()): ?>
                    <li>
                        <a href="index.php?id=<?php echo $item['id']; ?>" class="<?php echo ($item['id'] == $selected_id) ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($item['nazwa']); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </aside>

        <main class="portal-content">
            <?php if ($current['typ'] === 'html'): ?>
                <div class="html-render">
                    <?php echo $current['zawartosc']; ?>
                </div>
            <?php elseif ($current['typ'] === 'link_wew'): ?>
                <div class="wew-render">
                    <?php 
                        $safe_path = __DIR__ . '/' . $current['zawartosc'];
                        if (file_exists($safe_path)) {
                            include $safe_path;
                        } else {
                            echo "<div class='alert alert-warning'>Nie znaleziono lokalnego pliku: " . htmlspecialchars($current['zawartosc']) . "</div>";
                        }
                    ?>
                </div>
            <?php elseif ($current['typ'] === 'link_zew'): ?>
                <div class="zew-render" style="height:100%;">
                    <iframe src="<?php echo htmlspecialchars($current['zawartosc']); ?>" style="width:100%; height:480px; border:1px solid #ccc; border-radius:4px;"></iframe>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>
