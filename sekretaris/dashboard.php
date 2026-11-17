<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_role(['sekretaris'], '../');

$rootPrefix = '../';
$activeMenu = 'dashboard';
$pageTitle = 'Dashboard Sekretaris';

// Tandai terkirim langsung dari dashboard (aksi cepat)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'kirim') {
    $id = (int) ($_POST['id'] ?? 0);
    $pdo->prepare("UPDATE surat_keluar SET status = 'Terkirim', tgl_keluar = CURDATE() WHERE id = ?")->execute([$id]);
    $_SESSION['flash'] = 'Surat keluar ditandai terkirim.';
    header('Location: dashboard.php');
    exit;
}

$totalMasuk = (int) $pdo->query("SELECT COUNT(*) AS t FROM surat_masuk")->fetch()['t'];
$totalPerluTindak = (int) $pdo->query("SELECT COUNT(*) AS t FROM surat_masuk WHERE status IN ('Baru','Diproses')")->fetch()['t'];
$totalSelesai = (int) $pdo->query("SELECT COUNT(*) AS t FROM surat_masuk WHERE status = 'Selesai'")->fetch()['t'];
$totalDraftKeluar = (int) $pdo->query("SELECT COUNT(*) AS t FROM surat_keluar WHERE status = 'Draft'")->fetch()['t'];

// Surat masuk yang perlu ditindaklanjuti, yang paling lama menunggu di atas
$perluTindak = $pdo->query("SELECT * FROM surat_masuk WHERE status IN ('Baru','Diproses') ORDER BY created_at ASC LIMIT 6")->fetchAll();

// Draf surat keluar yang belum dikirim
$draftKeluar = $pdo->query("SELECT * FROM surat_keluar WHERE status = 'Draft' ORDER BY created_at DESC LIMIT 5")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <p class="eyebrow">Ringkasan</p>
    <h2>Dashboard Sekretaris</h2>
    <p class="desc">Pantau surat yang perlu ditindaklanjuti dan draf surat keluar hari ini.</p>
</div>

<div class="stat-grid">
    <div class="stat-card tone-muted"><div class="stat-icon"><?= icon('inbox', 19) ?></div><div><div class="stat-value"><?= $totalMasuk ?></div><div class="stat-label">Total Surat Masuk</div></div></div>
    <div class="stat-card tone-warning"><div class="stat-icon"><?= icon('clock', 19) ?></div><div><div class="stat-value"><?= $totalPerluTindak ?></div><div class="stat-label">Perlu Ditindaklanjuti</div></div></div>
    <div class="stat-card tone-success"><div class="stat-icon"><?= icon('check', 19) ?></div><div><div class="stat-value"><?= $totalSelesai ?></div><div class="stat-label">Selesai Ditangani</div></div></div>
    <div class="stat-card tone-info"><div class="stat-icon"><?= icon('send', 19) ?></div><div><div class="stat-value"><?= $totalDraftKeluar ?></div><div class="stat-label">Draf Surat Keluar</div></div></div>
</div>

<div class="card">
    <div class="card-head">
        <h3>Surat perlu ditindaklanjuti</h3>
        <a class="btn-link" href="surat_masuk.php">Lihat semua &rsaquo;</a>
    </div>
    <div class="table-scroll">
    <table class="table">
        <thead><tr><th>No. surat</th><th>Judul</th><th>Pengirim</th><th>Tgl masuk</th><th>Status</th><th>Berkas</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($perluTindak as $s): ?>
            <tr>
                <td class="mono"><?= h($s['nomor_surat']) ?></td>
                <td><?= h($s['judul']) ?></td>
                <td><?= h($s['pengirim']) ?></td>
                <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
                <td><span class="status-badge <?= $s['status'] === 'Baru' ? 'info' : 'warning' ?>"><?= h($s['status']) ?></span></td>
                <td>
                    <?php if ($s['file_path']): ?>
                        <a class="icon-btn" href="../<?= h($s['file_path']) ?>" download="<?= h($s['judul']) ?>.<?= h(pathinfo($s['file_path'], PATHINFO_EXTENSION)) ?>" title="Unduh berkas"><?= icon('download', 16) ?></a>
                    <?php else: ?>
                        <span style="color:#C7CEDA;">&mdash;</span>
                    <?php endif; ?>
                </td>
                <td><a class="btn-view" href="surat_masuk_detail.php?id=<?= (int) $s['id'] ?>"><?= icon('eye', 14) ?> Tindak lanjut</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($perluTindak)): ?><tr><td colspan="7" class="empty-row">Semua surat sudah ditangani. Kerja bagus!</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <h3>Draf surat keluar menunggu dikirim</h3>
        <a class="btn-link" href="surat_keluar.php">Lihat semua &rsaquo;</a>
    </div>
    <div class="table-scroll">
    <table class="table">
        <thead><tr><th>No. surat</th><th>Judul</th><th>Tujuan</th><th>Perihal</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($draftKeluar as $s): ?>
            <tr>
                <td class="mono"><?= h($s['nomor_surat']) ?></td>
                <td><?= h($s['judul']) ?></td>
                <td><?= h($s['tujuan']) ?></td>
                <td><?= h($s['perihal']) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="aksi" value="kirim">
                        <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                        <button type="submit" class="btn-view"><?= icon('send', 13) ?> Tandai terkirim</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($draftKeluar)): ?><tr><td colspan="5" class="empty-row">Tidak ada draf surat keluar yang menunggu.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
