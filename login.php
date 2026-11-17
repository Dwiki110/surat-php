<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Masuk &mdash; Sistem Administrasi Surat Kecamatan Cibitung</title>
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-brand">
        <div class="login-seal"><?= icon('landmark', 24) ?></div>
        <h1>Sistem Administrasi Surat</h1>
        <p>Kecamatan Cibitung &mdash; Kabupaten Bekasi</p>
        <ul class="login-points">
            <li><?= icon('search', 16) ?> Pencarian arsip cerdas yang toleran typo</li>
            <li><?= icon('inbox', 16) ?> Pengelolaan surat masuk &amp; surat keluar terpusat</li>
            <li><?= icon('file', 16) ?> Lihat &amp; unduh berkas surat kapan saja</li>
        </ul>
    </div>
    <div class="login-card">
        <p class="login-eyebrow">Masuk ke sistem</p>
        <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
        <form method="post">
            <label class="field-label">Username</label>
            <input class="field" type="text" name="username" placeholder="sekretaris01 / resepsionis01" required>
            <label class="field-label">Password</label>
            <input class="field" type="password" name="password" placeholder="Masukkan password" required>
            <button type="submit" class="btn-primary login-btn">Masuk ke Sistem <?= icon('chevron-right', 16) ?></button>
        </form>
    </div>
</div>
</body>
</html>
