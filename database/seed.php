<?php
// Jalankan file ini SEKALI lewat browser (http://localhost/surat-app/database/seed.php)
// setelah schema.sql berhasil diimport, untuk mengisi akun & data contoh.
require_once __DIR__ . '/../config/database.php';

try {
    // Cegah pengisian dobel
    $cek = $pdo->query("SELECT COUNT(*) AS total FROM pengguna")->fetch();
    if ($cek['total'] > 0) {
        die("Seed dibatalkan: tabel pengguna sudah berisi data. Kosongkan dulu jika ingin mengulang.");
    }

    $pdo->beginTransaction();

    // Akun pengguna
    $passHash = password_hash('123456', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO pengguna (username, password_hash, nama_lengkap, role) VALUES (?, ?, ?, ?)")
        ->execute(['sekretaris01', $passHash, 'Ahmad Fauzi, S.Sos., M.Si', 'sekretaris']);
    $sekretarisId = $pdo->lastInsertId();

    $pdo->prepare("INSERT INTO pengguna (username, password_hash, nama_lengkap, role) VALUES (?, ?, ?, ?)")
        ->execute(['resepsionis01', $passHash, 'Siti Nurjannah', 'resepsionis']);
    $resepsionisId = $pdo->lastInsertId();

    // Surat masuk contoh
    $suratMasuk = [
        ['005/Kec.Cbt/V/2026', 'Surat Permohonan Izin Usaha', 'Permohonan izin usaha mikro kecil di wilayah RW 02', 'Mochamad Ridwan', 'Baru'],
        ['004/Kec.Cbt/V/2026', 'Surat Izin Acara', 'Permohonan izin penyelenggaraan acara warga RW 04', 'Muhammad Dwiki', 'Baru'],
        ['003/Kec.Cbt/V/2026', 'Permohonan Dana Bantuan', 'Permohonan dana bantuan sosial untuk kegiatan posyandu', 'Siti Rahma', 'Diproses'],
        ['002/Kec.Cbt/V/2026', 'Surat Keterangan Domisili', 'Permohonan surat keterangan domisili usaha', 'Dwiki Wicaksono', 'Selesai'],
        ['001/Kec.Cbt/V/2026', 'Undangan Rapat Koordinasi', 'Undangan rapat koordinasi RT/RW se-Kecamatan Cibitung', 'Sekretariat Kecamatan', 'Selesai'],
    ];
    $stmtSurat = $pdo->prepare("INSERT INTO surat_masuk (nomor_surat, judul, keterangan, pengirim, status, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
    $suratIds = [];
    foreach ($suratMasuk as $s) {
        $stmtSurat->execute([$s[0], $s[1], $s[2], $s[3], $s[4], $resepsionisId]);
        $suratIds[$s[1]] = $pdo->lastInsertId();
    }

    // Hasil surat untuk yang sudah Selesai
    function hitungLevenshtein($a, $b) {
        $m = strlen($a); $n = strlen($b);
        $dp = array_fill(0, $m + 1, array_fill(0, $n + 1, 0));
        for ($i = 0; $i <= $m; $i++) $dp[$i][0] = $i;
        for ($j = 0; $j <= $n; $j++) $dp[0][$j] = $j;
        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                $cost = (strtolower($a[$i - 1]) === strtolower($b[$j - 1])) ? 0 : 1;
                $dp[$i][$j] = min($dp[$i - 1][$j] + 1, $dp[$i][$j - 1] + 1, $dp[$i - 1][$j - 1] + $cost);
            }
        }
        return $dp[$m][$n];
    }

    $hasilData = [
        ['Surat Keterangan Domisili', 'Surat Keterngan Domisili'],
        ['Undangan Rapat Koordinasi', 'Undangan Rapat Koordinasi'],
    ];
    $stmtHasil = $pdo->prepare("INSERT INTO hasil_surat (surat_id, judul_konfirmasi, levenshtein_distance, similarity_pct, uploaded_by) VALUES (?, ?, ?, ?, ?)");
    foreach ($hasilData as $h) {
        $judulAsli = $h[0]; $judulKonfirmasi = $h[1];
        $distance = hitungLevenshtein($judulAsli, $judulKonfirmasi);
        $maxLen = max(strlen($judulAsli), strlen($judulKonfirmasi));
        $similarity = round((1 - $distance / $maxLen) * 100, 1);
        $stmtHasil->execute([$suratIds[$judulAsli], $judulKonfirmasi, $distance, $similarity, $sekretarisId]);
    }

    // Surat keluar contoh
    $suratKeluar = [
        ['101/Kec.Cbt.SK/V/2026', 'Balasan Izin Penyelenggaraan Acara', 'Ketua RW 04', 'Persetujuan izin acara warga', 'Draft', null],
        ['102/Kec.Cbt.SK/V/2026', 'Surat Keterangan Domisili Usaha', 'Dwiki Wicaksono', 'Penerbitan surat keterangan domisili', 'Terkirim', date('Y-m-d')],
    ];
    $stmtKeluar = $pdo->prepare("INSERT INTO surat_keluar (nomor_surat, judul, tujuan, perihal, status, tgl_keluar, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($suratKeluar as $k) {
        $stmtKeluar->execute([$k[0], $k[1], $k[2], $k[3], $k[4], $k[5], $sekretarisId]);
    }

    // Notifikasi contoh
    $stmtNotif = $pdo->prepare("INSERT INTO notifikasi (surat_id, penerima_role, tipe, pesan, sudah_dibaca) VALUES (?, ?, ?, ?, ?)");
    $stmtNotif->execute([$suratIds['Permohonan Dana Bantuan'], 'resepsionis', 'diproses', 'Surat "Permohonan Dana Bantuan" sedang diproses oleh Sekretaris.', 0]);
    $stmtNotif->execute([$suratIds['Surat Keterangan Domisili'], 'resepsionis', 'selesai', 'Surat "Surat Keterangan Domisili" telah selesai diperiksa. File tersedia untuk diunduh.', 0]);
    $stmtNotif->execute([$suratIds['Surat Permohonan Izin Usaha'], 'sekretaris', 'baru', 'Surat baru masuk: "Surat Permohonan Izin Usaha" dari Resepsionis.', 0]);
    $stmtNotif->execute([$suratIds['Surat Izin Acara'], 'sekretaris', 'baru', 'Surat baru masuk: "Surat Izin Acara" dari Resepsionis.', 0]);

    $pdo->commit();
    echo "Seed data berhasil dimasukkan.<br>";
    echo "Login sekretaris &rarr; username: <b>sekretaris01</b>, password: <b>123456</b><br>";
    echo "Login resepsionis &rarr; username: <b>resepsionis01</b>, password: <b>123456</b><br><br>";
    echo '<a href="../login.php">Lanjut ke halaman login &rarr;</a>';
} catch (Exception $e) {
    $pdo->rollBack();
    die("Gagal seed data: " . $e->getMessage());
}
