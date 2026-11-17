<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('../');

$rootPrefix = '../';
$activeMenu = 'masuk';
$pageTitle = 'Detail Surat';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM surat_masuk WHERE id = ?");
$stmt->execute([$id]);
$surat = $stmt->fetch();
if (!$surat) {
    die('Surat tidak ditemukan.');
}

$stmtHasil = $pdo->prepare("SELECT * FROM hasil_surat WHERE surat_id = ? ORDER BY uploaded_at DESC LIMIT 1");
$stmtHasil->execute([$id]);
$hasil = $stmtHasil->fetch();

$error = '';
$previewScore = null;

// ---- Aksi: Sekretaris klik "Proses" ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'proses' && current_role() === 'sekretaris') {
    $pdo->prepare("UPDATE surat_masuk SET status = 'Diproses' WHERE id = ?")->execute([$id]);
    tambah_notifikasi($pdo, $id, 'resepsionis', 'diproses', "Surat \"{$surat['judul']}\" sedang diproses oleh Sekretaris.");
    $_SESSION['flash'] = 'Surat ditandai sedang diproses.';
    header('Location: surat_masuk_detail.php?id=' . $id);
    exit;
}

// ---- Aksi: Sekretaris klik "Selesai" langsung dari status Baru ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'selesai_langsung' && current_role() === 'sekretaris') {
    $pdo->prepare("UPDATE surat_masuk SET status = 'Selesai' WHERE id = ?")->execute([$id]);
    $pdo->prepare("INSERT INTO hasil_surat (surat_id, judul_konfirmasi, levenshtein_distance, similarity_pct, uploaded_by) VALUES (?, ?, 0, 100, ?)")
        ->execute([$id, $surat['judul'], $_SESSION['user_id']]);
    tambah_notifikasi($pdo, $id, 'resepsionis', 'selesai', "Surat \"{$surat['judul']}\" telah selesai diperiksa.");
    $_SESSION['flash'] = 'Surat ditandai selesai.';
    header('Location: surat_masuk_detail.php?id=' . $id);
    exit;
}

// ---- Aksi: Sekretaris upload hasil surat (status Diproses -> Selesai) ----
// Menjalankan Algoritma Levenshtein untuk verifikasi kesamaan judul konfirmasi vs judul asli
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'upload_hasil' && current_role() === 'sekretaris') {
    $judulKonfirmasi = trim($_POST['judul_konfirmasi'] ?? '');
    if ($judulKonfirmasi === '') {
        $error = 'Judul konfirmasi wajib diisi.';
    } else {
        $filePath = null;
        if (!empty($_FILES['berkas_hasil']['name'])) {
            $ext = pathinfo($_FILES['berkas_hasil']['name'], PATHINFO_EXTENSION);
            $safeName = uniqid('hasil_') . '.' . $ext;
            $target = __DIR__ . '/../uploads/hasil_surat/' . $safeName;
            if (move_uploaded_file($_FILES['berkas_hasil']['tmp_name'], $target)) {
                $filePath = 'uploads/hasil_surat/' . $safeName;
            }
        }

        $result = levenshtein_matrix($surat['judul'], $judulKonfirmasi);
        $score = similarity_ratio($result['distance'], $surat['judul'], $judulKonfirmasi);

        $pdo->prepare("UPDATE surat_masuk SET status = 'Selesai' WHERE id = ?")->execute([$id]);
        $pdo->prepare("INSERT INTO hasil_surat (surat_id, judul_konfirmasi, file_path, levenshtein_distance, similarity_pct, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)")
            ->execute([$id, $judulKonfirmasi, $filePath, $result['distance'], $score, $_SESSION['user_id']]);
        tambah_notifikasi($pdo, $id, 'resepsionis', 'selesai', "Surat \"{$surat['judul']}\" telah selesai diperiksa. File tersedia untuk diunduh.");

        $_SESSION['flash'] = 'Hasil surat terverifikasi & dikirim ke Resepsionis.';
        header('Location: surat_masuk_detail.php?id=' . $id);
        exit;
    }
}

// Preview skor live (tanpa submit) dipakai untuk menampilkan estimasi saat mengetik - opsional, dihitung ulang via JS sederhana tidak diperlukan di server render awal
if (($_GET['preview_judul'] ?? '') !== '') {
    $result = levenshtein_matrix($surat['judul'], $_GET['preview_judul']);
    $previewScore = similarity_ratio($result['distance'], $surat['judul'], $_GET['preview_judul']);
}

include __DIR__ . '/../includes/header.php';
?>
<a class="btn-back" href="surat_masuk.php"><?= icon('arrow-left', 15) ?> Kembali ke daftar</a>

<div class="card detail-card">
    <div class="detail-top">
        <div>
            <p class="eyebrow"><?= h($surat['nomor_surat']) ?></p>
            <h2><?= h($surat['judul']) ?></h2>
            <p class="desc">Dari: <?= h($surat['pengirim']) ?> &middot; Masuk: <?= date('d M Y', strtotime($surat['created_at'])) ?></p>
        </div>
        <span class="status-badge <?= $surat['status'] === 'Baru' ? 'info' : ($surat['status'] === 'Diproses' ? 'warning' : 'success') ?>"><?= h($surat['status']) ?></span>
    </div>
    <p class="detail-keterangan"><?= h($surat['keterangan']) ?: '<span style="color:#97A1B0">Tidak ada keterangan tambahan.</span>' ?></p>

    <?php if ($surat['file_path']): ?>
        <div class="file-card">
            <div class="file-card-icon"><?= icon('file', 20) ?></div>
            <div class="file-card-info">
                <div class="file-card-name">Berkas surat masuk</div>
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
            <div class="file-card-info file-card-empty">Tidak ada berkas terlampir untuk surat ini.</div>
        </div>
    <?php endif; ?>

    <?php if (current_role() === 'sekretaris' && $surat['status'] === 'Baru'): ?>
        <div class="action-row">
            <form method="post"><input type="hidden" name="aksi" value="proses"><button type="submit" class="btn-primary"><?= icon('clock', 15) ?> Proses</button></form>
            <form method="post"><input type="hidden" name="aksi" value="selesai_langsung"><button type="submit" class="btn-success"><?= icon('check', 15) ?> Selesai</button></form>
        </div>
    <?php endif; ?>

    <?php if (current_role() === 'sekretaris' && $surat['status'] === 'Diproses'): ?>
        <div class="upload-hasil">
            <h4>Upload hasil surat</h4>
            <p class="desc">Ketik ulang judul surat sesuai dokumen balasan, lalu unggah berkasnya. Sistem akan memverifikasi kecocokannya secara otomatis.</p>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="aksi" value="upload_hasil">
                <label class="field-label">Judul konfirmasi</label>
                <input class="field" type="text" name="judul_konfirmasi" value="<?= h($surat['judul']) ?>">
                <label class="field-label">Berkas hasil (.docx / .pdf)</label>
                <label class="file-drop" id="fileDropHasil">
                    <?= icon('upload', 18) ?>
                    <span id="fileDropLabelHasil">Pilih file hasil pembuatan surat</span>
                    <input type="file" name="berkas_hasil" accept=".docx,.pdf" style="display:none" onchange="
                        var lbl = document.getElementById('fileDropLabelHasil');
                        var box = document.getElementById('fileDropHasil');
                        if (this.files[0]) { lbl.textContent = this.files[0].name; box.classList.add('has-file'); }
                        else { lbl.textContent = 'Pilih file hasil pembuatan surat'; box.classList.remove('has-file'); }
                    ">
                </label>
                <?php if ($error): ?><div class="alert alert-error" style="margin-top:12px;"><?= h($error) ?></div><?php endif; ?>
                <div class="form-actions">
                    <button type="submit" class="btn-success"><?= icon('check', 15) ?> Selesai &amp; kirim ke Resepsionis</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($surat['status'] === 'Selesai' && $hasil): ?>
        <div class="hasil-box">
            <h4>Hasil verifikasi</h4>
            <table class="mini-table">
                <tr><td>Judul konfirmasi</td><td><?= h($hasil['judul_konfirmasi']) ?></td></tr>
                <tr><td>Tanggal upload</td><td><?= date('d M Y', strtotime($hasil['uploaded_at'])) ?></td></tr>
                <tr><td>Tingkat kecocokan</td><td><b class="<?= $hasil['similarity_pct'] >= SIMILARITY_THRESHOLD ? 'pos' : 'neg' ?>"><?= $hasil['similarity_pct'] ?>% <?= $hasil['similarity_pct'] >= SIMILARITY_THRESHOLD ? 'cocok' : 'perlu dicek ulang' ?></b></td></tr>
            </table>
            <?php if ($hasil['file_path']): ?>
                <div class="file-card">
                    <div class="file-card-icon"><?= icon('file', 20) ?></div>
                    <div class="file-card-info">
                        <div class="file-card-name">Berkas hasil / balasan</div>
                        <div class="file-card-sub"><?= h(basename($hasil['file_path'])) ?></div>
                    </div>
                    <div class="file-card-actions">
                        <a class="btn-view" href="../<?= h($hasil['file_path']) ?>" target="_blank"><?= icon('eye', 15) ?> Lihat</a>
                        <a class="btn-view" href="../<?= h($hasil['file_path']) ?>" download="<?= h($hasil['judul_konfirmasi']) ?>.<?= h(pathinfo($hasil['file_path'], PATHINFO_EXTENSION)) ?>"><?= icon('download', 15) ?> Unduh</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
