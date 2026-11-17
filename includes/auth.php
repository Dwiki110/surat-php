<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Memastikan pengguna sudah login, jika belum redirect ke login.
 * $rootPrefix = "" jika dipanggil dari root, "../" jika dari sub-folder (surat_masuk/, dst).
 */
function require_login(string $rootPrefix = ''): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . $rootPrefix . 'login.php');
        exit;
    }
}

/** Membatasi akses hanya untuk role tertentu, misal require_role(['sekretaris'], '../') */
function require_role(array $roles, string $rootPrefix = ''): void
{
    require_login($rootPrefix);
    if (!in_array($_SESSION['role'], $roles, true)) {
        http_response_code(403);
        die('Akses ditolak: halaman ini khusus untuk peran ' . implode(' / ', $roles) . '.');
    }
}

function current_role(): ?string
{
    return $_SESSION['role'] ?? null;
}

function current_name(): ?string
{
    return $_SESSION['nama_lengkap'] ?? null;
}
