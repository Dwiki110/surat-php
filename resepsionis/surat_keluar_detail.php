<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('../');

$rootPrefix = '../';
$activeMenu = 'keluar';
$pageTitle = 'Detail Surat Keluar';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM surat_keluar WHERE id = ?");
$stmt->execute([$id]);
$surat = $stmt->fetch();
if (!$surat) {
    die('Surat tidak ditemukan.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'kirim' && current_role() === 'sekretaris') {
    $pdo->prepare("UPDATE surat_keluar SET status = 'Terkirim', tgl_keluar = CURDATE() WHERE id = ?")->execute([$id]);
    $_SESSION['flash'] = 'Surat keluar ditandai terkirim.';
    header('Location: surat_keluar_detail.php?id=' . $id);
    exit;
}

include __DIR__ . '/../includes/header.php';
?>
<a class="btn-back" href="surat_keluar.php"><?= icon('arrow-left', 15) ?> Kembali ke daftar</a>

<div class="card detail-card">
    <div class="detail-top">
        <div>
            <p class="eyebrow"><?= h($surat['nomor_surat']) ?></p>
            <h2><?= h($surat['judul']) ?></h2>
            <p class="desc">Tujuan: <?= h($surat['tujuan']) ?> &middot; Dibuat: <?= date('d M Y', strtotime($surat['created_at'])) ?></p>
        </div>
        <span class="status-badge <?= $surat['status'] === 'Draft' ? 'muted' : 'success' ?>"><?= h($surat['status']) ?></span>
    </div>
    <p class="detail-keterangan"><?= h($surat['perihal']) ?: '<span style="color:#97A1B0">Tidak ada perihal tambahan.</span>' ?></p>

    <?php if ($surat['file_path']): ?>
        <div class="file-card">
            <div class="file-card-icon"><?= icon('file', 20) ?></div>
            <div class="file-card-info">
                <div class="file-card-name">Berkas surat keluar</div>
                <div class="file-card-sub"><?= h(basename($surat['file_path'])) ?></div>
            </div>
            <div class="file-card-actions">
                <a class="btn-view" href="../<?= h($surat['file_path']) ?>" target="_blank"><?= icon('eye', 15) ?> Lihat</a>
                <a class="btn-view" href="../<?= h($surat['file_path']) ?>" download="<?= h($surat['judul']) ?>.<?= h(pathinfo($surat['file_path'], PATHINFO_EXTENSION)) ?>"><?= icon('download', 15) ?> Unduh</a>
            </div>
        </div>
    <?php else: ?>
        <div class="file-card">
            <div class="file-card-icon"><?= icon('file', 20) ?></div>
            <div class="file-card-info file-card-empty">Belum ada berkas terlampir untuk surat ini.</div>
        </div>
    <?php endif; ?>

    <?php if (current_role() === 'sekretaris' && $surat['status'] === 'Draft'): ?>
        <div class="action-row">
            <form method="post"><input type="hidden" name="aksi" value="kirim"><button type="submit" class="btn-success"><?= icon('send', 15) ?> Tandai terkirim</button></form>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
