
<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../koneksi.php';

$hasil = $db->query(
    "SELECT id, nama, email FROM anggota ORDER BY id DESC"
);
?>

<?php include __DIR__ . '/../../template/header.php'; ?>
<?php include __DIR__ . '/../../template/sidebar.php'; ?>

<div class="app-main">
    <!-- Judul halaman -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Data Anggota</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="/proyek-admin/index.php?hal=dashboard">
                                Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Data Anggota
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Isi halaman -->
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Anggota Perpustakaan</h3>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">No.</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($hasil && $hasil->num_rows > 0): ?>
                                    <?php $no = 1; ?>

                                    <?php while ($anggota = $hasil->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= e($anggota['nama']) ?></td>
                                            <td><?= e($anggota['email']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            Belum ada data anggota.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../template/footer.php'; ?>
