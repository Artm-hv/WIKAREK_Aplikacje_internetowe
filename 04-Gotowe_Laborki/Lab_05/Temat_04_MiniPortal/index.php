<?php
include "konfig.php";
$menu_items = $conn ? $conn->query("SELECT * FROM menu ORDER BY kolejnosc ASC") : null;
$selected_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$active_content = null;
if ($conn) {
    $res = $conn->query("SELECT * FROM menu WHERE id = $selected_id");
    if ($res && $res->num_rows > 0) $active_content = $res->fetch_assoc();
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Mini Portal</title><link rel="stylesheet" href="style.css">
<style>
.portal-layout { display: flex; gap: 20px; min-height: 400px; }
.portal-menu { width: 200px; border-right: 2px solid #eee; padding-right: 15px; }
.portal-menu ul { list-style: none; }
.portal-menu li { margin-bottom: 10px; }
.portal-menu a { display: block; padding: 8px 12px; background: #eef2f7; text-decoration: none; border-radius: 4px; color: #333; }
.portal-menu a.active, .portal-menu a:hover { background: #3498db; color: white; }
.portal-body { flex: 1; padding: 10px; }
</style></head>
<body><div class="container"><header><h1>Mini Portal - Politechnika Świętokrzyska</h1><nav><a href="admin.php">Zarządzaj Menu</a><a href="install.php">Instalator</a></nav></header>
<div class="portal-layout">
<div class="portal-menu"><ul>
<?php if ($menu_items) while($m = $menu_items->fetch_assoc()): ?>
    <li><a href="index.php?id=<?php echo $m['id']; ?>" class="<?php echo ($m['id'] == $selected_id)?'active':''; ?>"><?php echo htmlspecialchars($m['nazwa']); ?></a></li>
<?php endwhile; ?>
</ul></div>
<div class="portal-body">
<?php
if ($active_content) {
    if ($active_content['typ'] === 'html') {
        echo $active_content['zawartosc'];
    } elseif ($active_content['typ'] === 'link_zew') {
        echo "<p>Link do serwisu zewnętrznego: <a href='" . htmlspecialchars($active_content['zawartosc']) . "' target='_blank'>" . htmlspecialchars($active_content['zawartosc']) . "</a></p>";
        echo "<iframe src='" . htmlspecialchars($active_content['zawartosc']) . "' style='width:100%; height:350px; border:1px solid #ccc;'></iframe>";
    } elseif ($active_content['typ'] === 'link_wew') {
        echo "<p>Link wewnętrzny: <strong>" . htmlspecialchars($active_content['zawartosc']) . "</strong></p>";
    }
} else { echo "<p>Wybierz pozycję z menu.</p>"; }
?>
</div></div></div></body></html>
