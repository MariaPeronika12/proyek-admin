
<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$role = $_SESSION['role'] ?? '';

?>

<!-- SIDEBAR -->
<aside class="app-sidebar bg-body-secondary shadow"
       data-bs-theme="dark">

    <!-- BRAND -->
    <div class="sidebar-brand">
        <a href="<?= BASE_URL ?>index.php" class="brand-link">
            <span class="brand-text fw-light">
                Perpustakaan Digital
            </span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">

            <ul class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                role="navigation"
                aria-label="Menu utama"
                data-accordion="false">

                <?php if ($role === 'admin' || $role === 'petugas'): ?>

                    <!-- MENU ADMIN DAN PETUGAS -->

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?hal=dashboard"
                           class="nav-link">
                            <i class="nav-icon bi bi-speedometer"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <?php if ($role === 'admin'): ?>

                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>index.php?hal=login"
                               class="nav-link">
                                <i class="nav-icon bi bi-people"></i>
                                <p>Data Pengguna</p>
                            </a>
                        </li>

                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?hal=buku"
                           class="nav-link">
                            <i class="nav-icon bi bi-book"></i>
                            <p>Data Buku</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?hal=anggota"
                           class="nav-link">
                            <i class="nav-icon bi bi-person-vcard"></i>
                            <p>Data Anggota</p>
                        </a>
                    </li>

                    <?php if ($role === 'admin'): ?>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-gear"></i>
                                <p>Pengaturan</p>
                            </a>
                        </li>

                    <?php endif; ?>

                <?php elseif ($role === 'anggota'): ?>

                    <!-- MENU KHUSUS ANGGOTA -->

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?hal=anggota&aksi=dashboard"
                           class="nav-link">
                            <i class="nav-icon bi bi-speedometer"></i>
                            <p>Dashboard Saya</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?hal=buku"
                           class="nav-link">
                            <i class="nav-icon bi bi-book"></i>
                            <p>Katalog Buku</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-journal-check"></i>
                            <p>Peminjaman Saya</p>
                        </a>
                    </li>

                <?php endif; ?>

                <!-- LOGOUT -->

                <li class="nav-item mt-3">
                    <a href="<?= BASE_URL ?>index.php?hal=login&aksi=logout"
                       class="nav-link">
                        <i class="nav-icon bi bi-box-arrow-right"></i>
                        <p>Keluar</p>
                    </a>
                </li>

            </ul>

        </nav>
    </div>

</aside>