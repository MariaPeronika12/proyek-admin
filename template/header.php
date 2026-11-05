<?php

require_once __DIR__ . '/../env.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$namaPengguna = $_SESSION['user'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e(APP_NAME) ?></title>

    <!-- AdminLTE CSS -->
    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>assets/css/adminlte.css"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">

<div class="app-wrapper">

    <!-- Navbar -->
    <nav class="app-header navbar navbar-expand bg-body">

        <!-- Navbar kiri -->
        <div class="container-fluid">

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a
                        class="nav-link"
                        data-lte-toggle="sidebar"
                        href="#"
                        role="button"
                        aria-label="Buka atau tutup sidebar"
                    >
                        <i class="bi bi-list"></i>
                    </a>
                </li>

                <li class="nav-item d-none d-md-block">
                    <a
                        href="<?= BASE_URL ?>index.php?hal=dashboard"
                        class="nav-link"
                    >
                        Home
                    </a>
                </li>
            </ul>

            <!-- Navbar kanan -->
            <ul class="navbar-nav ms-auto">

                <!-- Notifikasi -->
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#"
                        aria-label="Notifikasi"
                    >
                        <i class="bi bi-bell"></i>
                    </a>
                </li>

                <!-- Menu pengguna -->
                <li class="nav-item dropdown">

                    <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        id="menuPengguna"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <i class="bi bi-person-circle"></i>
                        <?= e($namaPengguna) ?>
                    </a>

                    <ul
                        class="dropdown-menu dropdown-menu-end"
                        aria-labelledby="menuPengguna"
                    >
                        <li>
                            <span class="dropdown-item-text">
                                Login sebagai
                                <strong><?= e($namaPengguna) ?></strong>
                            </span>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a
                                class="dropdown-item"
                                href="<?= BASE_URL ?>index.php?hal=login&aksi=logout"
                                onclick="return confirm('Yakin ingin logout?')"
                            >
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>

                </li>

            </ul>
        </div>
    </nav>
    <!-- Akhir Navbar -->
