<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('../');

$rootPrefix = '../';
$activeMenu = 'pencarian';
$pageTitle = 'Pencarian Cerdas';

$query = trim($_GET['q'] ?? '');
$results = [];
$scannedTotal = 0;
$scanTruncated = false;
$totalScored = 0;

if ($query !== '') {
    // Hitung total data lebih dulu supaya tahu apakah perlu dibatasi (jaga performa untuk data besar)
    $totalMasuk = (int) $pdo->query("SELECT COUNT(*) AS t FROM surat_masuk")->fetch()['t'];
    $totalKeluar = (int) $pdo->query("SELECT COUNT(*) AS t FROM surat_keluar")->fetch()['t'];

    if ($totalMasuk > SEARCH_SCAN_LIMIT) $scanTruncated = true;
    if ($totalKeluar > SEARCH_SCAN_LIMIT) $scanTruncated = true;

    // Batasi jumlah baris yang dipindai per tabel (data terbaru diprioritaskan) agar
    // perhitungan Levenshtein tidak meloop seluruh tabel saat datanya ribuan/puluhan ribu.
    $suratMasuk = $pdo->query("SELECT id, judul, nomor_surat, file_path FROM surat_masuk ORDER BY created_at DESC LIMIT " . SEARCH_SCAN_LIMIT)->fetchAll();
    $suratKeluar = $pdo->query("SELECT id, judul, nomor_surat, file_path FROM surat_keluar ORDER BY created_at DESC LIMIT " . SEARCH_SCAN_LIMIT)->fetchAll();
    $scannedTotal = count($suratMasuk) + count($suratKeluar);

    foreach ($suratMasuk as $s) {
        $r = levenshtein_matrix($query, $s['judul']);
        $results[] = [
            'jenis' => 'Surat Masuk',
            'id' => $s['id'],
            'nomor' => $s['nomor_surat'],
            'judul' => $s['judul'],
            'file_path' => $s['file_path'],
            'link' => 'surat_masuk_detail.php?id=' . $s['id'],
            'similarity' => similarity_ratio($r['distance'], $query, $s['judul']),
        ];
    }
    foreach ($suratKeluar as $s) {
        $r = levenshtein_matrix($query, $s['judul']);
        $results[] = [
            'jenis' => 'Surat Keluar',
            'id' => $s['id'],
            'nomor' => $s['nomor_surat'],
            'judul' => $s['judul'],
            'file_path' => $s['file_path'],
            'link' => 'surat_keluar_detail.php?id=' . $s['id'],
            'similarity' => similarity_ratio($r['distance'], $query, $s['judul']),
        ];
    }

    usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

    // Jumlah yang berhasil dinilai (sebelum difilter threshold), dipakai untuk teks info di atas hasil.
    $totalScored = count($results);

    // Buang semua hasil yang similarity-nya di bawah SIMILARITY_THRESHOLD (60%) --
    // hasil yang tidak relevan tidak boleh tampil sama sekali, bukan sekadar diberi gaya pudar.
    $results = array_values(array_filter(
        $results,
        fn($r) => $r['similarity'] >= SIMILARITY_THRESHOLD
    ));
}

$relevanCount = count($results);
// Hanya render sebagian hasil teratas ke halaman -- cukup untuk kebutuhan pencarian
// dan menjaga DOM tetap ringan meski data yang dipindai jumlahnya besar.
$resultsToShow = array_slice($results, 0, SEARCH_RESULT_LIMIT);

include __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <p class="eyebrow">Fitur unggulan</p>
    <h2>Pencarian cerdas</h2>
    <p class="desc">Pencarian arsip yang tetap menemukan surat &amp; berkasnya meski kata kunci sedikit typo.</p>
</div>

<div class="card">
    <form method="get">
        <label class="field-label">Kata kunci pencarian</label>
        <div class="search-big">
            <?= icon('search', 18) ?>
            <input type="text" name="q" autofocus placeholder="ketik nama surat / nomor, boleh typo..." value="<?= h($query) ?>">
            <button type="submit" class="btn-primary">Cari</button>
        </div>
    </form>
</div>

<?php if ($query !== ''): ?>
    <div class="card">
        <div class="card-head">
            <h3>Hasil pencarian</h3>
            <span class="threshold-note"><?= $relevanCount ?> dari <?= $totalScored ?> arsip relevan<?= $relevanCount > SEARCH_RESULT_LIMIT ? ' (menampilkan ' . SEARCH_RESULT_LIMIT . ' teratas)' : '' ?></span>
        </div>
        <?php if ($scanTruncated): ?>
            <p class="threshold-note" style="margin-bottom:12px;">Data cukup besar &mdash; pencarian difokuskan pada <?= number_format(SEARCH_SCAN_LIMIT) ?> arsip terbaru per kategori agar tetap ringan. Persempit kata kunci untuk hasil yang lebih tepat.</p>
        <?php endif; ?>
        <?php if (empty($resultsToShow)): ?>
            <p class="empty-row">
                <?= $totalScored > 0
                    ? 'Tidak ada arsip yang cukup mirip (kemiripan di bawah ' . (int) SIMILARITY_THRESHOLD . '%).'
                    : 'Belum ada arsip surat untuk dicari.' ?>
            </p>
        <?php endif; ?>
        <?php foreach ($resultsToShow as $r): ?>
            <div class="result-row">
                <div class="file-card-icon" style="flex-shrink:0;"><?= icon('file', 18) ?></div>
                <div class="result-info">
                    <div class="result-title"><?= h($r['judul']) ?></div>
                    <div class="result-meta"><?= h($r['jenis']) ?> &middot; <?= h($r['nomor']) ?><?= $r['file_path'] ? ' &middot; berkas tersedia' : ' &middot; belum ada berkas' ?></div>
                </div>
                <div class="meter-wrap">
                    <div class="meter-track"><div class="meter-fill ok" style="width: <?= max(4, $r['similarity']) ?>%;"></div></div>
                    <div class="meter-pct"><?= $r['similarity'] ?>%</div>
                </div>
                <div class="file-card-actions">
                    <?php if ($r['file_path']): ?>
                        <a class="icon-btn" href="../<?= h($r['file_path']) ?>" download="<?= h($r['judul']) ?>.<?= h(pathinfo($r['file_path'], PATHINFO_EXTENSION)) ?>" title="Unduh berkas"><?= icon('download', 16) ?></a>
                    <?php endif; ?>
                    <a class="btn-view" href="<?= $r['link'] ?>"><?= icon('eye', 14) ?> Lihat</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>