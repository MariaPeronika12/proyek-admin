<?php
require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../koneksi.php';

$judulHalaman = 'Dashboard';

// Menghitung jumlah buku dari database
$resultBuku = $db->query("SELECT COUNT(*) AS total FROM buku");
$totalBuku = (int) $resultBuku->fetch_assoc()['total'];

require_once __DIR__ . '/../../template/header.php';
require_once __DIR__ . '/../../template/sidebar.php';
?>

<style>
.dashboard-content {
    background: #f4f6f9;
    min-height: 75vh;
}

.stat-card {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    color: white;
    min-height: 150px;
    box-shadow: 0 3px 8px rgba(0,0,0,.12);
    transition: transform .2s;
}

.stat-card:hover {
    transform: translateY(-4px);
}

.stat-card .stat-number {
    font-size: 36px;
    font-weight: 700;
    line-height: 1.2;
}

.stat-card .stat-label {
    font-size: 16px;
    margin-top: 10px;
}

.stat-card .stat-icon {
    position: absolute;
    right: 18px;
    top: 24px;
    font-size: 58px;
    opacity: .2;
}

.stat-card .stat-link {
    display: block;
    color: inherit;
    text-align: center;
    padding: 10px;
    margin-top: 15px;
    background: rgba(0,0,0,.12);
    text-decoration: none;
}

.stat-blue { background: #0d6efd; }
.stat-green { background: #198754; }
.stat-yellow { background: #ffc107; color: #212529; }
.stat-red { background: #dc3545; }

.dashboard-panel {
    border: 0;
    border-radius: 10px;
    box-shadow: 0 2px 7px rgba(0,0,0,.10);
    height: 100%;
}

.dashboard-panel .card-header {
    background: white;
    padding: 18px 22px;
    border-bottom: 1px solid #eee;
}

.dashboard-panel .card-title {
    font-size: 19px;
    font-weight: 600;
}

.chart-area {
    width: 100%;
    overflow: hidden;
}

.chart-area svg {
    width: 100%;
    height: auto;
    display: block;
}

.quick-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    margin-bottom: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    color: #343a40;
    border: 1px solid #eee;
}

.quick-link:hover {
    background: #e9f2ff;
    color: #0d6efd;
}

.quick-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    border-radius: 9px;
    background: #e5efff;
    color: #0d6efd;
    font-size: 21px;
}

@media (max-width: 768px) {
    .stat-card .stat-number { font-size: 30px; }
    .stat-card .stat-icon { font-size: 44px; }
}
</style>

<main class="app-main">

    <!-- JUDUL DASHBOARD -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold">Dashboard</h3>
                    <p class="text-secondary mb-0 mt-1">
                        Ringkasan informasi perpustakaan
                    </p>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>index.php?hal=dashboard">
                                Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Dashboard
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content dashboard-content">
        <div class="container-fluid">

            <!-- 4 KARTU STATISTIK -->
            <div class="row g-3 mb-4">

                <!-- TOTAL PENGGUNA -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card stat-blue">
                        <div class="p-3">
                            <div class="stat-number">150</div>
                            <div class="stat-label">Total Pengguna</div>
                            <i class="bi bi-people-fill stat-icon"></i>
                        </div>

                        <a class="stat-link"
                           href="<?= BASE_URL ?>index.php?hal=dashboard">
                            Lihat detail
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- TOTAL BUKU OTOMATIS -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card stat-green">
                        <div class="p-3">
                            <div class="stat-number">
                                <?= $totalBuku ?>
                            </div>
                            <div class="stat-label">Total Buku</div>
                            <i class="bi bi-book-half stat-icon"></i>
                        </div>

                        <a class="stat-link"
                           href="<?= BASE_URL ?>index.php?hal=buku">
                            Lihat detail
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- PEMINJAMAN: ANGKA CONTOH -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card stat-yellow">
                        <div class="p-3">
                            <div class="stat-number">25</div>
                            <div class="stat-label">Peminjaman</div>
                            <i class="bi bi-journal-check stat-icon"></i>
                        </div>

                        <a class="stat-link"
                           href="<?= BASE_URL ?>index.php?hal=buku">
                            Lihat detail
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- PENGEMBALIAN: ANGKA CONTOH -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card stat-red">
                        <div class="p-3">
                            <div class="stat-number">8</div>
                            <div class="stat-label">
                                Pengembalian Terlambat
                            </div>
                            <i class="bi bi-clock-history stat-icon"></i>
                        </div>

                        <a class="stat-link"
                           href="<?= BASE_URL ?>index.php?hal=buku">
                            Lihat detail
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>

            <!-- GRAFIK DAN AKSES CEPAT -->
            <div class="row g-3 mb-4">

                <!-- GRAFIK -->
                <div class="col-12 col-xl-7">
                    <div class="card dashboard-panel">

                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-bar-chart-line me-2 text-primary"></i>
                                Statistik Perpustakaan
                            </h3>
                        </div>

                        <div class="card-body">
                            <p class="text-secondary small">
                                Ilustrasi aktivitas bulanan
                            </p>

                            <div class="chart-area">
                                <svg viewBox="0 0 650 300"
                                     role="img"
                                     aria-label="Grafik ilustrasi aktivitas perpustakaan">

                                    <defs>
                                        <linearGradient id="areaBlue"
                                            x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%"
                                                stop-color="#0d6efd"
                                                stop-opacity=".30"/>
                                            <stop offset="100%"
                                                stop-color="#0d6efd"
                                                stop-opacity=".02"/>
                                        </linearGradient>

                                        <linearGradient id="areaGreen"
                                            x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%"
                                                stop-color="#20c997"
                                                stop-opacity=".25"/>
                                            <stop offset="100%"
                                                stop-color="#20c997"
                                                stop-opacity=".02"/>
                                        </linearGradient>
                                    </defs>

                                    <g stroke="#e5e7eb" stroke-width="1">
                                        <line x1="55" y1="35" x2="625" y2="35"/>
                                        <line x1="55" y1="85" x2="625" y2="85"/>
                                        <line x1="55" y1="135" x2="625" y2="135"/>
                                        <line x1="55" y1="185" x2="625" y2="185"/>
                                        <line x1="55" y1="235" x2="625" y2="235"/>
                                    </g>

                                    <g fill="#6c757d" font-size="12">
                                        <text x="15" y="39">100</text>
                                        <text x="23" y="89">80</text>
                                        <text x="23" y="139">60</text>
                                        <text x="23" y="189">40</text>
                                        <text x="23" y="239">20</text>
                                    </g>

                                    <path
                                        d="M55 160
                                           C90 145 105 195 150 175
                                           S220 80 265 95
                                           S340 155 380 130
                                           S450 90 490 125
                                           S565 175 625 105
                                           L625 235 L55 235 Z"
                                        fill="url(#areaGreen)"/>

                                    <path
                                        d="M55 200
                                           C95 180 115 125 150 145
                                           S225 205 265 220
                                           S330 220 380 80
                                           S455 205 490 195
                                           S575 125 625 55
                                           L625 235 L55 235 Z"
                                        fill="url(#areaBlue)"/>

                                    <path
                                        d="M55 160
                                           C90 145 105 195 150 175
                                           S220 80 265 95
                                           S340 155 380 130
                                           S450 90 490 125
                                           S565 175 625 105"
                                        fill="none"
                                        stroke="#20c997"
                                        stroke-width="3"
                                        stroke-linecap="round"/>

                                    <path
                                        d="M55 200
                                           C95 180 115 125 150 145
                                           S225 205 265 220
                                           S330 220 380 80
                                           S455 205 490 195
                                           S575 125 625 55"
                                        fill="none"
                                        stroke="#0d6efd"
                                        stroke-width="3"
                                        stroke-linecap="round"/>

                                    <g fill="#6c757d"
                                       font-size="12"
                                       text-anchor="middle">
                                        <text x="55" y="265">Jan</text>
                                        <text x="168" y="265">Feb</text>
                                        <text x="282" y="265">Mar</text>
                                        <text x="396" y="265">Apr</text>
                                        <text x="510" y="265">Mei</text>
                                        <text x="625" y="265">Jun</text>
                                    </g>
                                </svg>
                            </div>

                            <div class="d-flex flex-wrap gap-4 mt-3 small">
                                <span>
                                    <span class="badge rounded-pill"
                                          style="background:#0d6efd">&nbsp;</span>
                                    Aktivitas peminjaman
                                </span>

                                <span>
                                    <span class="badge rounded-pill"
                                          style="background:#20c997">&nbsp;</span>
                                    Aktivitas pengunjung
                                </span>
                            </div>

                            <p class="text-secondary small mt-3 mb-0">
                                Grafik ini masih ilustrasi, belum mengambil
                                data transaksi dari database.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- AKSES CEPAT -->
                <div class="col-12 col-xl-5">
                    <div class="card dashboard-panel">

                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-lightning-charge me-2 text-warning"></i>
                                Akses Cepat
                            </h3>
                        </div>

                        <div class="card-body">

                            <a class="quick-link"
                               href="<?= BASE_URL ?>index.php?hal=buku&aksi=create">

                                <span class="quick-icon">
                                    <i class="bi bi-plus-lg"></i>
                                </span>

                                <span>
                                    <strong>Tambah Buku</strong>
                                    <br>
                                    <small class="text-secondary">
                                        Masukkan data buku baru
                                    </small>
                                </span>

                                <i class="bi bi-chevron-right ms-auto"></i>
                            </a>

                            <a class="quick-link"
                               href="<?= BASE_URL ?>index.php?hal=buku">

                                <span class="quick-icon">
                                    <i class="bi bi-journal-bookmark"></i>
                                </span>

                                <span>
                                    <strong>Data Buku</strong>
                                    <br>
                                    <small class="text-secondary">
                                        Lihat dan kelola koleksi buku
                                    </small>
                                </span>

                                <i class="bi bi-chevron-right ms-auto"></i>
                            </a>

                            <div class="p-3 mt-3 rounded-3"
                                 style="background:#e8f3ff">

                                <div class="d-flex gap-3">
                                    <i class="bi bi-info-circle-fill text-primary fs-4"></i>

                                    <div>
                                        <strong>Selamat Datang!</strong>
                                        <p class="mb-0 mt-1 small">
                                            Kelola data perpustakaan melalui
                                            menu yang tersedia di samping.
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- PANEL INFORMASI -->
            <div class="card dashboard-panel mb-4">
                <div class="card-body p-4">

                    <div class="d-flex flex-wrap align-items-center gap-3">

                        <div class="quick-icon"
                             style="width:55px;height:55px;font-size:26px">
                            <i class="bi bi-book"></i>
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="mb-1 fw-bold">
                                Sistem Informasi Perpustakaan
                            </h5>

                            <p class="mb-0 text-secondary">
                                Dashboard administrasi untuk membantu
                                pengelolaan koleksi buku.
                            </p>
                        </div>

                        <a href="<?= BASE_URL ?>index.php?hal=buku"
                           class="btn btn-primary">
                            <i class="bi bi-arrow-right-circle me-1"></i>
                            Buka Data Buku
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../template/footer.php'; ?>
