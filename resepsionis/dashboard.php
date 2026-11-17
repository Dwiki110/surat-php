<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_role(['resepsionis'], '../');

$rootPrefix = '../';
$activeMenu = 'dashboard';
$pageTitle = 'Dashboard Resepsionis';

$userId = $_SESSION['user_id'];

// Statistik hanya untuk surat yang diajukan oleh resepsionis yang sedang login
$stmt = $pdo->prepare("SELECT status, COUNT(*) AS total FROM surat_masuk WHERE uploaded_by = ? GROUP BY status");
$stmt->execute([$userId]);
$rekap = ['Baru' => 0, 'Diproses' => 0, 'Selesai' => 0];
foreach ($stmt->fetchAll() as $row) {
    $rekap[$row['status']] = (int) $row['total'];
}
$totalDiajukan = array_sum($rekap);

$stmt = $pdo->prepare(
    "SELECT sm.*, hs.file_path AS hasil_file_path
     FROM surat_masuk sm
     LEFT JOIN hasil_surat hs ON hs.surat_id = sm.id
     WHERE sm.uploaded_by = ?
     ORDER BY sm.created_at DESC LIMIT 6"
);
$stmt->execute([$userId]);
$suratSaya = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <p class="eyebrow">Ringkasan</p>
    <h2>Dashboard Resepsionis</h2>
    <p class="desc">Kelola surat masuk yang kamu ajukan dan pantau status penanganannya oleh Sekretaris.</p>
</div>

<div class="stat-grid">
    <div class="stat-card tone-muted"><div class="stat-icon"><?= icon('inbox', 19) ?></div><div><div class="stat-value"><?= $totalDiajukan ?></div><div class="stat-label">Surat Diajukan</div></div></div>
    <div class="stat-card tone-info"><div class="stat-icon"><?= icon('send', 19) ?></div><div><div class="stat-value"><?= $rekap['Baru'] ?></div><div class="stat-label">Menunggu Diproses</div></div></div>
    <div class="stat-card tone-warning"><div class="stat-icon"><?= icon('clock', 19) ?></div><div><div class="stat-value"><?= $rekap['Diproses'] ?></div><div class="stat-label">Sedang Diproses</div></div></div>
    <div class="stat-card tone-success"><div class="stat-icon"><?= icon('check', 19) ?></div><div><div class="stat-value"><?= $rekap['Selesai'] ?></div><div class="stat-label">Selesai Ditangani</div></div></div>
</div>

<a href="tambah_surat.php" class="btn-secondary" style="margin-bottom:20px;"><?= icon('plus', 15) ?> Upload surat baru</a>

<div class="card">
    <div class="card-head">
        <h3>Surat yang saya ajukan</h3>
        <a class="btn-link" href="surat_masuk.php">Lihat semua &rsaquo;</a>
    </div>
    <div class="table-scroll">
    <table class="table">
        <thead><tr><th>No. surat</th><th>Judul</th><th>Tgl masuk</th><th>Status</th><th>Berkas</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($suratSaya as $s): ?>
            <?php $downloadFile = $s['hasil_file_path'] ?: $s['file_path']; ?>
            <tr>
                <td class="mono"><?= h($s['nomor_surat']) ?></td>
                <td><?= h($s['judul']) ?></td>
                <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
                <td><span class="status-badge <?= $s['status'] === 'Baru' ? 'info' : ($s['status'] === 'Diproses' ? 'warning' : 'success') ?>"><?= h($s['status']) ?></span></td>
                <td>
                    <?php if ($downloadFile): ?>
                        <a class="icon-btn" href="../<?= h($downloadFile) ?>" download="<?= h($s['judul']) ?>.<?= h(pathinfo($downloadFile, PATHINFO_EXTENSION)) ?>" title="Unduh berkas"><?= icon('download', 16) ?></a>
                    <?php else: ?>
                        <span style="color:#C7CEDA;">&mdash;</span>
                    <?php endif; ?>
                </td>
                <td><a class="btn-view" href="surat_masuk_detail.php?id=<?= (int) $s['id'] ?>"><?= icon('eye', 14) ?> Lihat</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($suratSaya)): ?><tr><td colspan="6" class="empty-row">Kamu belum mengajukan surat apa pun. Klik "Upload surat baru" di atas.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
