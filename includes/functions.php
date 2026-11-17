<?php
/**
 * Menghitung Levenshtein Distance antara dua string sekaligus
 * mengembalikan matriks DP lengkap (untuk visualisasi ledger).
 * Sesuai rumus rekursif D(i,j) pada BAB 2 Tugas Akhir.
 */
function levenshtein_matrix(string $a, string $b): array
{
    $m = mb_strlen($a);
    $n = mb_strlen($b);
    $dp = [];
    for ($i = 0; $i <= $m; $i++) {
        $dp[$i] = array_fill(0, $n + 1, 0);
        $dp[$i][0] = $i;
    }
    for ($j = 0; $j <= $n; $j++) {
        $dp[0][$j] = $j;
    }
    for ($i = 1; $i <= $m; $i++) {
        for ($j = 1; $j <= $n; $j++) {
            $charA = mb_strtolower(mb_substr($a, $i - 1, 1));
            $charB = mb_strtolower(mb_substr($b, $j - 1, 1));
            $cost = ($charA === $charB) ? 0 : 1;
            $dp[$i][$j] = min(
                $dp[$i - 1][$j] + 1,     // hapus (deletion)
                $dp[$i][$j - 1] + 1,     // sisip (insertion)
                $dp[$i - 1][$j - 1] + $cost // ganti (substitution)
            );
        }
    }
    return ['distance' => $dp[$m][$n], 'matrix' => $dp];
}

/**
 * Menormalisasi jarak Levenshtein menjadi persentase kemiripan
 * (Similarity Ratio) sesuai rumus BAB 2.4 Tugas Akhir.
 */
function similarity_ratio(int $distance, string $a, string $b): float
{
    $maxLen = max(mb_strlen($a), mb_strlen($b));
    if ($maxLen === 0) return 100.0;
    return round((1 - $distance / $maxLen) * 100, 1);
}

/**
 * Menelusuri jalur operasi edit optimal (traceback) pada matriks DP,
 * dipakai untuk menyorot jalur pada visualisasi ledger.
 * Mengembalikan array set koordinat "i-j" yang dilalui.
 */
function levenshtein_path(array $matrix, string $a, string $b): array
{
    $i = mb_strlen($a);
    $j = mb_strlen($b);
    $path = ["$i-$j" => true];
    while ($i > 0 || $j > 0) {
        $diagMatch = $i > 0 && $j > 0
            && mb_strtolower(mb_substr($a, $i - 1, 1)) === mb_strtolower(mb_substr($b, $j - 1, 1))
            && $matrix[$i][$j] === $matrix[$i - 1][$j - 1];
        if ($diagMatch) {
            $i--; $j--;
        } else {
            $diag = ($i > 0 && $j > 0) ? $matrix[$i - 1][$j - 1] : PHP_INT_MAX;
            $up   = ($i > 0) ? $matrix[$i - 1][$j] : PHP_INT_MAX;
            $left = ($j > 0) ? $matrix[$i][$j - 1] : PHP_INT_MAX;
            $min = min($diag, $up, $left);
            if ($min === $diag) { $i--; $j--; }
            elseif ($min === $up) { $i--; }
            else { $j--; }
        }
        $path["$i-$j"] = true;
    }
    return $path;
}

/** Ambang batas kemiripan (threshold) pencarian cerdas */
define('SIMILARITY_THRESHOLD', 60.0);

/** Batas aman jumlah baris yang diproses Levenshtein sekali pencarian, dan jumlah hasil yang ditampilkan */
define('SEARCH_SCAN_LIMIT', 4000);
define('SEARCH_RESULT_LIMIT', 30);

/** Menyimpan notifikasi baru untuk peran tertentu */
function tambah_notifikasi(PDO $pdo, ?int $suratId, string $role, string $tipe, string $pesan): void
{
    $stmt = $pdo->prepare("INSERT INTO notifikasi (surat_id, penerima_role, tipe, pesan) VALUES (?, ?, ?, ?)");
    $stmt->execute([$suratId, $role, $tipe, $pesan]);
}

/** Membuat nomor surat berurutan otomatis */
function generate_nomor_surat(PDO $pdo, string $tabel, string $suffix = ''): string
{
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM {$tabel}");
    $total = (int) $stmt->fetch()['total'] + 1;
    $bulanRomawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
    $romawi = $bulanRomawi[date('n') - 1];
    $tahun = date('Y');
    return sprintf('%03d/Kec.Cbt%s/%s/%s', $total, $suffix, $romawi, $tahun);
}

/** Helper singkat untuk escape output HTML */
function h(?string $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/** Ambil nomor halaman dari query string dengan aman (minimal 1) */
function current_page(): int
{
    $p = (int) ($_GET['page'] ?? 1);
    return $p < 1 ? 1 : $p;
}

/**
 * Render navigasi paginasi sederhana.
 * $baseParams = parameter GET yang perlu dipertahankan (selain 'page'), misal ['status' => 'Baru'].
 */
function render_pagination(int $page, int $totalRows, int $perPage, array $baseParams = []): string
{
    $totalPages = max(1, (int) ceil($totalRows / $perPage));
    if ($totalPages <= 1) return '';

    $makeUrl = function (int $p) use ($baseParams): string {
        $params = array_merge($baseParams, ['page' => $p]);
        return '?' . http_build_query($params);
    };

    $start = min($totalRows, ($page - 1) * $perPage + 1);
    $end = min($totalRows, $page * $perPage);

    $html = '<div class="pagination">';
    $html .= '<div class="pagination-info">Menampilkan ' . $start . '&ndash;' . $end . ' dari ' . $totalRows . ' data</div>';
    $html .= '<div class="pagination-nav">';

    $html .= $page > 1
        ? '<a href="' . h($makeUrl($page - 1)) . '">&lsaquo;</a>'
        : '<span class="disabled">&lsaquo;</span>';

    $windowStart = max(1, $page - 2);
    $windowEnd = min($totalPages, $page + 2);
    if ($windowStart > 1) $html .= '<a href="' . h($makeUrl(1)) . '">1</a>' . ($windowStart > 2 ? '<span>&hellip;</span>' : '');
    for ($i = $windowStart; $i <= $windowEnd; $i++) {
        $html .= $i === $page ? '<span class="current">' . $i . '</span>' : '<a href="' . h($makeUrl($i)) . '">' . $i . '</a>';
    }
    if ($windowEnd < $totalPages) $html .= ($windowEnd < $totalPages - 1 ? '<span>&hellip;</span>' : '') . '<a href="' . h($makeUrl($totalPages)) . '">' . $totalPages . '</a>';

    $html .= $page < $totalPages
        ? '<a href="' . h($makeUrl($page + 1)) . '">&rsaquo;</a>'
        : '<span class="disabled">&rsaquo;</span>';

    $html .= '</div></div>';
    return $html;
}
function icon(string $name, int $size = 18): string
{
    $c = 'width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"';
    $icons = [
        'dashboard'  => "<rect x='3' y='3' width='7' height='7' rx='1.5'/><rect x='14' y='3' width='7' height='7' rx='1.5'/><rect x='3' y='14' width='7' height='7' rx='1.5'/><rect x='14' y='14' width='7' height='7' rx='1.5'/>",
        'inbox'      => "<path d='M3 12h4l2 3h6l2-3h4'/><path d='M5 12 3 6a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1l-2 6'/><path d='M3 12v6a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-6'/>",
        'send'       => "<path d='m3 11 18-8-8 18-2-8-8-2z'/>",
        'search'     => "<circle cx='11' cy='11' r='7'/><line x1='21' y1='21' x2='16.65' y2='16.65'/>",
        'bell'       => "<path d='M6 8a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6'/><path d='M10 21a2 2 0 0 0 4 0'/>",
        'logout'     => "<path d='M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4'/><polyline points='16 17 21 12 16 7'/><line x1='21' y1='12' x2='9' y2='12'/>",
        'check'      => "<circle cx='12' cy='12' r='9'/><path d='m8.5 12.5 2.5 2.5 5-5'/>",
        'clock'      => "<circle cx='12' cy='12' r='9'/><path d='M12 7v5l3 3'/>",
        'upload'     => "<path d='M12 16V4'/><path d='m6 10 6-6 6 6'/><path d='M4 20h16'/>",
        'file'       => "<path d='M14 3v5a1 1 0 0 0 1 1h5'/><path d='M6 3h8l6 6v11a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z'/>",
        'download'   => "<path d='M12 4v12'/><path d='m6 12 6 6 6-6'/><path d='M4 20h16'/>",
        'plus'       => "<line x1='12' y1='5' x2='12' y2='19'/><line x1='5' y1='12' x2='19' y2='12'/>",
        'arrow-left' => "<line x1='19' y1='12' x2='5' y2='12'/><polyline points='11 6 5 12 11 18'/>",
        'chevron-right' => "<polyline points='9 6 15 12 9 18'/>",
        'eye'        => "<path d='M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z'/><circle cx='12' cy='12' r='3'/>",
        'landmark'   => "<line x1='3' y1='22' x2='21' y2='22'/><line x1='6' y1='18' x2='6' y2='11'/><line x1='10' y1='18' x2='10' y2='11'/><line x1='14' y1='18' x2='14' y2='11'/><line x1='18' y1='18' x2='18' y2='11'/><polygon points='12 2 21 8 3 8'/>",
        'x'          => "<line x1='18' y1='6' x2='6' y2='18'/><line x1='6' y1='6' x2='18' y2='18'/>",
        'filter'     => "<polygon points='4 4 20 4 14 12.5 14 19 10 21 10 12.5 4 4'/>",
        'sparkles'   => "<path d='M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M18.4 5.6l-2.8 2.8M8.4 15.6l-2.8 2.8'/>",
        'menu'       => "<line x1='4' y1='7' x2='20' y2='7'/><line x1='4' y1='12' x2='20' y2='12'/><line x1='4' y1='17' x2='20' y2='17'/>",
    ];
    $inner = $icons[$name] ?? '';
    return "<svg {$c}>{$inner}</svg>";
}
