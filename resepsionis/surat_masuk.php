<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('../');

$rootPrefix = '../';
$activeMenu = 'masuk';
$pageTitle = 'Surat Masuk';

$perPage = 15;
$page = current_page();
$offset = ($page - 1) * $perPage;

$filter = $_GET['status'] ?? 'Semua';
if (in_array($filter, ['Baru', 'Diproses', 'Selesai'], true)) {
    $stmtCount = $pdo->prepare("SELECT COUNT(*) AS t FROM surat_masuk WHERE status = ?");
    $stmtCount->execute([$filter]);
    $totalRows = (int) $stmtCount->fetch()['t'];

    $stmt = $pdo->prepare("SELECT * FROM surat_masuk WHERE status = :status ORDER BY created_at DESC LIMIT :lim OFFSET :off");
    $stmt->bindValue(':status', $filter);
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $filter = 'Semua';
    $totalRows = (int) $pdo->query("SELECT COUNT(*) AS t FROM surat_masuk")->fetch()['t'];

    $stmt = $pdo->prepare("SELECT * FROM surat_masuk ORDER BY created_at DESC LIMIT :lim OFFSET :off");
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
}
$daftarSurat = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <p class="eyebrow">Persuratan</p>
    <h2>Surat masuk</h2>
    <p class="desc">Daftar seluruh arsip surat masuk kecamatan.</p>
</div>

<?php if (current_role() === 'resepsionis'): ?>
    <a href="tambah_surat.php" class="btn-secondary" style="margin-bottom:20px;"><?= icon('plus', 15) ?> Upload surat baru</a>
<?php endif; ?>

<div class="card">
    <div class="card-head">
        <h3>Daftar surat masuk</h3>
        <div class="filter-row">
            <?php foreach (['Semua', 'Baru', 'Diproses', 'Selesai'] as $f): ?>
                <a class="chip<?= $filter === $f ? ' active' : '' ?>" href="surat_masuk.php?status=<?= urlencode($f) ?>"><?= $f ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="table-scroll">
    <table class="table">
        <thead><tr><th>No. surat</th><th>Judul</th><th>Pengirim</th><th>Tgl masuk</th><th>Status</th><th>Berkas</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($daftarSurat as $s): ?>
            <tr>
                <td class="mono"><?= h($s['nomor_surat']) ?></td>
                <td><?= h($s['judul']) ?></td>
                <td><?= h($s['pengirim']) ?></td>
                <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
                <td><span class="status-badge <?= $s['status'] === 'Baru' ? 'info' : ($s['status'] === 'Diproses' ? 'warning' : 'success') ?>"><?= h($s['status']) ?></span></td>
                <td>
                    <?php if ($s['file_path']): ?>
                        <a class="icon-btn" href="../<?= h($s['file_path']) ?>" download="<?= h($s['judul']) ?>.<?= h(pathinfo($s['file_path'], PATHINFO_EXTENSION)) ?>" title="Unduh berkas"><?= icon('download', 16) ?></a>
                    <?php else: ?>
                        <span style="color:#C7CEDA;">&mdash;</span>
                    <?php endif; ?>
                </td>
                <td><a class="btn-view" href="surat_masuk_detail.php?id=<?= (int) $s['id'] ?>"><?= icon('eye', 14) ?> Lihat</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($daftarSurat)): ?><tr><td colspan="7" class="empty-row">Tidak ada surat pada status ini.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
    <?= render_pagination($page, $totalRows, $perPage, ['status' => $filter]) ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
