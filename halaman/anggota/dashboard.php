
<?php

require_once __DIR__ . '/../../env.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Pastikan hanya anggota yang dapat mengakses
if (($_SESSION['role'] ?? '') !== 'anggota') {
    header('Location: ' . BASE_URL . 'index.php?hal=login');
    exit;
}

$nama = $_SESSION['nama'] ?? 'Anggota';
$email = $_SESSION['user'] ?? '';

?>

<?php include __DIR__ . '/../../template/header.php'; ?>
<?php include __DIR__ . '/../../template/sidebar.php'; ?>

<div class="app-main">

    <!-- HEADER -->
    <div class="app-content-header">
        <div class="container-fluid">
            <h3>Dashboard Anggota</h3>
            <p class="text-muted">
                Selamat datang di Perpustakaan Digital.
            </p>
        </div>
    </div>

    <!-- KONTEN -->
    <div class="app-content">
        <div class="container-fluid">

            <!-- SAMBUTAN -->
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <h4>
                        Halo, <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>!
                    </h4>

                    <p class="mb-1">
                        Selamat datang di sistem informasi
                        Perpustakaan Digital.
                    </p>

                    <small class="text-muted">
                        <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>
                    </small>
                </div>
            </div>

            <!-- MENU ANGGOTA -->
            <div class="row">

                <div class="col-md-6">
                    <div class="card text-bg-primary">
                        <div class="card-body">
                            <h4>
                                <i class="bi bi-book"></i>
                                Katalog Buku
                            </h4>

                            <p>
                                Lihat koleksi buku yang tersedia
                                di perpustakaan.
                            </p>

                            <a
                                href="<?= BASE_URL ?>index.php?hal=buku"
                                class="btn btn-light"
                            >
                                Lihat Buku
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card text-bg-success">
                        <div class="card-body">
                            <h4>
                                <i class="bi bi-journal-check"></i>
                                Peminjaman Buku
                            </h4>

                            <p>
                                Informasi peminjaman buku anggota.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- INFORMASI -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Informasi Perpustakaan
                    </h3>
                </div>

                <div class="card-body">
                    <p>
                        Selamat menggunakan layanan perpustakaan.
                        Silakan jelajahi katalog untuk menemukan
                        buku yang ingin kamu baca.
                    </p>
                </div>
            </div>

        </div>
    </div>

</div>

<?php include __DIR__ . '/../../template/footer.php'; ?>