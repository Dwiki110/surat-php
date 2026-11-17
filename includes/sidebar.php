<?php
// Membutuhkan $rootPrefix dan $activeMenu dari file pemanggil
$menu = [
    'dashboard'   => ['label' => 'Dashboard', 'href' => 'dashboard.php', 'icon' => 'dashboard'],
    'masuk'       => ['label' => 'Surat Masuk', 'href' => 'surat_masuk.php', 'icon' => 'inbox'],
    'keluar'      => ['label' => 'Surat Keluar', 'href' => 'surat_keluar.php', 'icon' => 'send'],
    'pencarian'   => ['label' => 'Pencarian Cerdas', 'href' => 'pencarian.php', 'icon' => 'search'],
    'notifikasi'  => ['label' => 'Notifikasi', 'href' => 'notifikasi.php', 'icon' => 'bell'],
];
?>
<div class="sidebar" id="sidebarNav">
    <div class="sidebar-brand">
        <div class="sidebar-seal"><?= icon('landmark', 18) ?></div>
        <div>
            <div class="sidebar-title">SuratApp</div>
            <div class="sidebar-sub">Kec. Cibitung</div>
        </div>
        <button type="button" class="icon-btn sidebar-close" id="sidebarCloseBtn" aria-label="Tutup menu" style="margin-left:auto; color:#C6CEDB;"><?= icon('x', 18) ?></button>
    </div>
    <nav>
        <?php foreach ($menu as $key => $item): ?>
            <a class="nav-item<?= ($activeMenu ?? '') === $key ? ' active' : '' ?>" href="<?= $item['href'] ?>">
                <?= icon($item['icon'], 17) ?> <?= h($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">
        <div class="sidebar-role-badge"><?= current_role() === 'sekretaris' ? 'Sekretaris' : 'Resepsionis' ?></div>
    </div>
</div>
