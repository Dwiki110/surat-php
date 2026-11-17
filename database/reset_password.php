<?php
// Jalankan file ini lewat browser jika kamu SUDAH pernah menjalankan seed.php
// sebelumnya (dengan password lama) dan ingin mengganti password akun
// sekretaris01 & resepsionis01 menjadi 123456 tanpa menghapus data lain.
require_once __DIR__ . '/../config/database.php';

$passHash = password_hash('123456', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE pengguna SET password_hash = ? WHERE username IN ('sekretaris01', 'resepsionis01')");
$stmt->execute([$passHash]);

echo "Password akun sekretaris01 & resepsionis01 berhasil diubah menjadi: <b>123456</b><br><br>";
echo '<a href="../login.php">Lanjut ke halaman login &rarr;</a>';
