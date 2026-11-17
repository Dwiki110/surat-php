<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('../');

$rootPrefix = '../';
$activeMenu = 'keluar';
$pageTitle = 'Surat Keluar';
$error = '';

// Tambah draf surat keluar (khusus Sekretaris)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'tambah' && current_role() === 'sekretaris') {
    $judul = trim($_POST['judul'] ?? '');
    $tujuan = trim($_POST['tujuan'] ?? '');
    $perihal = trim($_POST['perihal'] ?? '');
    if ($judul === '' || $tujuan === '') {
        $error = 'Judul surat dan tujuan wajib diisi.';
    } else {
        $filePath = null;
        if (!empty($_FILES['berkas']['name'])) {
            $ext = pathinfo($_FILES['berkas']['name'], PATHINFO_EXTENSION);
            $safeName = uniqid('keluar_') . '.' . $ext;
            $target = __DIR__ . '/../uploads/surat_keluar/' . $safeName;
            if (move_uploaded_file($_FILES['berkas']['tmp_name'], $target)) {
                $filePath = 'uploads/surat_keluar/' . $safeName;
            }
        }
        $nomor = generate_nomor_surat($pdo, 'surat_keluar', '.SK');
        $pdo->prepare("INSERT INTO surat_keluar (nomor_surat, judul, tujuan, perihal, file_path, status, created_by) VALUES (?, ?, ?, ?, ?, 'Draft', ?)")
            ->execute([$nomor, $judul, $tujuan, $perihal, $filePath, $_SESSION['user_id']]);
        $_SESSION['flash'] = 'Draf surat keluar tersimpan.';
        header('Location: surat_keluar.php');
        exit;
    }
}

// Tandai terkirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'kirim' && current_role() === 'sekretaris') {
    $id = (int) ($_POST['id'] ?? 0);
    $pdo->prepare("UPDATE surat_keluar SET status = 'Terkirim', tgl_keluar = CURDATE() WHERE id = ?")->execute([$id]);
    $_SESSION['flash'] = 'Surat keluar ditandai terkirim.';
    header('Location: surat_keluar.php');
    exit;
}

$perPage = 15;
$page = current_page();
$offset = ($page - 1) * $perPage;
$totalRows = (int) $pdo->query("SELECT COUNT(*) AS t FROM surat_keluar")->fetch()['t'];

$stmt = $pdo->prepare("SELECT * FROM surat_keluar ORDER BY created_at DESC LIMIT :lim OFFSET :off");
$stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();
$daftarKeluar = $stmt->fetchAll();
$showForm = isset($_GET['tambah']);

include __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <p class="eyebrow">Persuratan</p>
    <h2>Surat keluar</h2>
    <p class="desc">Daftar surat balasan / surat resmi yang diterbitkan kecamatan.</p>
</div>

<?php if (current_role() === 'sekretaris'): ?>
    <?php if ($showForm): ?>
        <form class="card" method="post" enctype="multipart/form-data">
            <input type="hidden" name="aksi" value="tambah">
            <div class="card-head"><h3>Buat draf surat keluar</h3><a class="icon-btn" href="surat_keluar.php"><?= icon('x', 16) ?></a></div>
            <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
            <label class="field-label">Judul surat</label>
            <input class="field" type="text" name="judul" placeholder="Contoh: Balasan Izin Penyelenggaraan Acara" value="<?= h($_POST['judul'] ?? '') ?>">
            <label class="field-label">Tujuan</label>
            <input class="field" type="text" name="tujuan" placeholder="Nama penerima / instansi tujuan" value="<?= h($_POST['tujuan'] ?? '') ?>">
            <label class="field-label">Perihal</label>
            <input class="field" type="text" name="perihal" placeholder="Ringkasan perihal surat" value="<?= h($_POST['perihal'] ?? '') ?>">
            <label class="field-label">Berkas surat (opsional)</label>
            <label class="file-drop" id="fileDropKeluar">
                <?= icon('upload', 18) ?>
                <span id="fileDropLabelKeluar">Pilih atau letakkan berkas di sini</span>
                <input type="file" name="berkas" accept=".pdf,.docx" style="display:none" onchange="
                    var lbl = document.getElementById('fileDropLabelKeluar');
                    var box = document.getElementById('fileDropKeluar');
                    if (this.files[0]) { lbl.textContent = this.files[0].name; box.classList.add('has-file'); }
                    else { lbl.textContent = 'Pilih atau letakkan berkas di sini'; box.classList.remove('has-file'); }
                ">
            </label>
            <div class="form-actions"><button type="submit" class="btn-primary"><?= icon('send', 15) ?> Simpan draf</button></div>
        </form>
    <?php else: ?>
        <a class="btn-secondary" style="margin-bottom:20px;" href="surat_keluar.php?tambah=1"><?= icon('plus', 15) ?> Buat surat keluar</a>
    <?php endif; ?>
<?php endif; ?>

<div class="card">
    <div class="card-head"><h3>Daftar surat keluar</h3></div>
    <div class="table-scroll">
    <table class="table">
        <thead><tr><th>No. surat</th><th>Judul</th><th>Tujuan</th><th>Tgl keluar</th><th>Status</th><th>Berkas</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($daftarKeluar as $s): ?>
            <tr>
                <td class="mono"><?= h($s['nomor_surat']) ?></td>
                <td><?= h($s['judul']) ?></td>
                <td><?= h($s['tujuan']) ?></td>
                <td><?= $s['tgl_keluar'] ? date('d M Y', strtotime($s['tgl_keluar'])) : '-' ?></td>
                <td><span class="status-badge <?= $s['status'] === 'Draft' ? 'muted' : 'success' ?>"><?= h($s['status']) ?></span></td>
                <td>
                    <?php if ($s['file_path']): ?>
                        <a class="icon-btn" href="../<?= h($s['file_path']) ?>" download="<?= h($s['judul']) ?>.<?= h(pathinfo($s['file_path'], PATHINFO_EXTENSION)) ?>" title="Unduh berkas"><?= icon('download', 16) ?></a>
                    <?php else: ?>
                        <span style="color:#C7CEDA;">&mdash;</span>
                    <?php endif; ?>
                </td>
                <td><a class="btn-view" href="surat_keluar_detail.php?id=<?= (int) $s['id'] ?>"><?= icon('eye', 14) ?> Lihat</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($daftarKeluar)): ?><tr><td colspan="7" class="empty-row">Belum ada surat keluar.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
    <?= render_pagination($page, $totalRows, $perPage) ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
