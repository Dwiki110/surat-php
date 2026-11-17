<?php
// Variabel yang harus disiapkan sebelum include file ini:
// $pageTitle (string), $rootPrefix ('' atau '../'), $pdo (PDO connection)

$unreadCount = 0;
if (!empty($_SESSION['role'])) {
    $stmtUnread = $pdo->prepare("SELECT COUNT(*) AS total FROM notifikasi WHERE penerima_role = ? AND sudah_dibaca = 0");
    $stmtUnread->execute([$_SESSION['role']]);
    $unreadCount = (int) $stmtUnread->fetch()['total'];
}
$searchQ = trim($_GET['q'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title><?= h($pageTitle ?? 'SuratApp') ?> &mdash; Administrasi Surat Kecamatan Cibitung</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $rootPrefix ?>assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="main-col">
        <div class="topbar">
            <button type="button" class="hamburger-btn" id="hamburgerBtn" aria-label="Buka menu">
                <?= icon('menu', 18) ?>
            </button>
            <form class="topbar-search" action="pencarian.php" method="get">
                <?= icon('search', 16) ?>
                <input type="text" name="q" placeholder="Cari surat &amp; berkas..." value="<?= h($searchQ) ?>">
                <button type="submit">Cari</button>
            </form>
            <div class="topbar-right">
                <a class="icon-btn" href="notifikasi.php" aria-label="Notifikasi">
                    <?= icon('bell', 18) ?>
                    <?php if ($unreadCount > 0): ?><span class="badge-dot"><?= $unreadCount ?></span><?php endif; ?>
                </a>
                <div class="topbar-user">
                    <div class="avatar"><?= h(mb_substr(current_name() ?? '?', 0, 1)) ?></div>
                    <div>
                        <div class="user-name"><?= h(current_name()) ?></div>
                        <div class="user-title"><?= current_role() === 'sekretaris' ? 'Sekretaris Kecamatan' : 'Admin Resepsionis' ?></div>
                    </div>
                </div>
                <a class="icon-btn" href="<?= $rootPrefix ?>logout.php" aria-label="Keluar"><?= icon('logout', 17) ?></a>
            </div>
        </div>
        <div class="content">
