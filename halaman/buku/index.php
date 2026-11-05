<?php

require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../koneksi.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Mengambil data buku
$sql = "SELECT id, judul, penulis, tahun FROM buku ORDER BY id DESC";
$result = $db->query($sql);

if (!$result) {
    error_log('Query buku gagal: ' . $db->error);
    exit('Data buku gagal dimuat. Periksa query database.');
}
?>

<?php require_once __DIR__ . '/../../template/header.php'; ?>
<?php require_once __DIR__ . '/../../template/sidebar.php'; ?>

<div class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <!-- Judul dan tombol -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h1 class="h3 mb-0">Data Buku</h1>

                <div class="d-flex flex-wrap gap-2">

                    <!-- Tombol tambah buku -->
                    <a
                        href="<?= BASE_URL ?>index.php?hal=buku&aksi=create"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-plus-circle"></i>
                        Tambah Buku
                    </a>

                    <!-- Tombol cetak seluruh daftar -->
                    <a
                        href="<?= BASE_URL ?>index.php?hal=buku&aksi=print"
                        class="btn btn-success"
                        target="_blank"
                        rel="noopener"
                    >
                        <i class="bi bi-printer"></i>
                        Cetak
                    </a>

                </div>
            </div>

            <!-- Tabel buku -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Buku</h3>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;">No.</th>
                                <th>Judul Buku</th>
                                <th>Penulis</th>
                                <th style="width: 100px;">Tahun</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php $no = 1; ?>

                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>

                                        <td>
                                            <?= e($row['judul'] ?? '') ?>
                                        </td>

                                        <td>
                                            <?= e($row['penulis'] ?? '') ?>
                                        </td>

                                        <td>
                                            <?= e($row['tahun'] ?? '') ?>
                                        </td>

                                        <td>
                                            <a
                                                href="<?= BASE_URL ?>index.php?hal=buku&aksi=update&id=<?= (int) $row['id'] ?>"
                                                class="btn btn-warning btn-sm"
                                            >
                                                <i class="bi bi-pencil"></i>
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Belum ada data buku.
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

<?php
$result->free();
require_once __DIR__ . '/../../template/footer.php';
?>
