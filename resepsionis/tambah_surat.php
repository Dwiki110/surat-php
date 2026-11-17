<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_role(['resepsionis'], '../');

$rootPrefix = '../';
$activeMenu = 'masuk';
$pageTitle = 'Upload Surat Masuk';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $pengirim = trim($_POST['pengirim'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($judul === '' || $pengirim === '') {
        $error = 'Judul surat dan nama pengirim wajib diisi.';
    } else {
        $filePath = null;
        if (!empty($_FILES['berkas']['name'])) {
            $ext = pathinfo($_FILES['berkas']['name'], PATHINFO_EXTENSION);
            $safeName = uniqid('surat_') . '.' . $ext;
            $target = __DIR__ . '/../uploads/surat_masuk/' . $safeName;
            if (move_uploaded_file($_FILES['berkas']['tmp_name'], $target)) {
                $filePath = 'uploads/surat_masuk/' . $safeName;
            }
        }

        $nomor = generate_nomor_surat($pdo, 'surat_masuk');
        $stmt = $pdo->prepare("INSERT INTO surat_masuk (nomor_surat, judul, keterangan, pengirim, file_path, status, uploaded_by) VALUES (?, ?, ?, ?, ?, 'Baru', ?)");
        $stmt->execute([$nomor, $judul, $keterangan, $pengirim, $filePath, $_SESSION['user_id']]);
        $suratId = $pdo->lastInsertId();

        tambah_notifikasi($pdo, $suratId, 'sekretaris', 'baru', "Surat baru masuk: \"$judul\" dari Resepsionis.");

        $_SESSION['flash'] = 'Surat berhasil dikirim ke Sekretaris.';
        header('Location: surat_masuk.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>
<a class="btn-back" href="surat_masuk.php"><?= icon('arrow-left', 15) ?> Kembali ke daftar</a>
<div class="page-header">
    <p class="eyebrow">Persuratan</p>
    <h2>Upload surat masuk baru</h2>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>

<form class="card" method="post" enctype="multipart/form-data">
    <label class="field-label">Judul surat</label>
    <input class="field" type="text" name="judul" placeholder="Contoh: Surat Permohonan Izin" value="<?= h($_POST['judul'] ?? '') ?>">

    <label class="field-label">Nama pengirim</label>
    <input class="field" type="text" name="pengirim" placeholder="Nama warga / instansi pengirim" value="<?= h($_POST['pengirim'] ?? '') ?>">

    <label class="field-label">Keterangan (opsional)</label>
    <textarea class="field" name="keterangan" rows="3" placeholder="Deskripsi singkat"><?= h($_POST['keterangan'] ?? '') ?></textarea>

    <label class="field-label">Berkas surat (.pdf / .docx)</label>
    <label class="file-drop" id="fileDropMasuk">
        <?= icon('upload', 18) ?>
        <span id="fileDropLabelMasuk">Pilih atau letakkan berkas di sini</span>
        <input type="file" name="berkas" accept=".pdf,.docx" style="display:none" onchange="
            var lbl = document.getElementById('fileDropLabelMasuk');
            var box = document.getElementById('fileDropMasuk');
            if (this.files[0]) { lbl.textContent = this.files[0].name; box.classList.add('has-file'); }
            else { lbl.textContent = 'Pilih atau letakkan berkas di sini'; box.classList.remove('has-file'); }
        ">
    </label>

    <div class="form-actions">
        <button type="submit" class="btn-primary"><?= icon('send', 15) ?> Kirim ke Sekretaris</button>
    </div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
