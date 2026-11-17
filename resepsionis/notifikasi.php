<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('../');

$rootPrefix = '../';
$activeMenu = 'notifikasi';
$pageTitle = 'Notifikasi';

$perPage = 20;
$page = current_page();
$offset = ($page - 1) * $perPage;

$stmtCount = $pdo->prepare("SELECT COUNT(*) AS t FROM notifikasi WHERE penerima_role = ?");
$stmtCount->execute([current_role()]);
$totalRows = (int) $stmtCount->fetch()['t'];

// Tandai semua sudah dibaca setiap kali halaman ini dibuka
$pdo->prepare("UPDATE notifikasi SET sudah_dibaca = 1 WHERE penerima_role = ?")->execute([current_role()]);

$stmt = $pdo->prepare("SELECT * FROM notifikasi WHERE penerima_role = :role ORDER BY created_at DESC LIMIT :lim OFFSET :off");
$stmt->bindValue(':role', current_role());
$stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();
$list = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <p class="eyebrow">Pemberitahuan</p>
    <h2>Notifikasi</h2>
    <p class="desc">Riwayat pemberitahuan otomatis dari sistem.</p>
</div>

<div class="card">
    <?php if (empty($list)): ?>
        <p class="empty-row">Belum ada notifikasi.</p>
    <?php endif; ?>
    <?php foreach ($list as $n): ?>
        <div class="notif-row">
            <div class="notif-dot"></div>
            <div>
                <p><?= h($n['pesan']) ?></p>
                <span class="notif-time"><?= date('d M Y H:i', strtotime($n['created_at'])) ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?= render_pagination($page, $totalRows, $perPage) ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
