<?php
require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../koneksi.php';

$judulHalaman = 'Tambah Buku';
$pesanError = '';

$judul = '';
$penulis = '';
$tahun = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $tahunInput = trim($_POST['tahun'] ?? '');

    $tahun = filter_var($tahunInput, FILTER_VALIDATE_INT);

    if (
        $judul === '' ||
        $penulis === '' ||
        $tahun === false ||
        $tahun === null ||
        $tahun < 1000 ||
        $tahun > 2100
    ) {
        $pesanError = 'Judul, penulis, dan tahun wajib diisi dengan benar.';
    } else {
        try {
            $stmt = $db->prepare(
                "INSERT INTO buku (judul, penulis, tahun)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param('ssi', $judul, $penulis, $tahun);
            $stmt->execute();

            redirect('buku');
        } catch (mysqli_sql_exception $e) {
            $pesanError = 'Gagal menyimpan buku. Periksa struktur tabel database.';
        }
    }
}

require_once __DIR__ . '/../../template/header.php';
require_once __DIR__ . '/../../template/sidebar.php';
?>

<div class="app-main">
    <main class="app-content">
        <div class="container-fluid">
            <h3 class="mb-3">Tambah Buku</h3>

            <?php if ($pesanError !== ''): ?>
                <div class="alert alert-danger">
                    <?= e($pesanError) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST"
                          action="<?= e(BASE_URL) ?>index.php?hal=buku&aksi=create">

                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Buku</label>
                            <input
                                type="text"
                                class="form-control"
                                id="judul"
                                name="judul"
                                value="<?= e($judul) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="penulis" class="form-label">Penulis</label>
                            <input
                                type="text"
                                class="form-control"
                                id="penulis"
                                name="penulis"
                                value="<?= e($penulis) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun Terbit</label>
                            <input
                                type="number"
                                class="form-control"
                                id="tahun"
                                name="tahun"
                                min="1000"
                                max="2100"
                                value="<?= e((string) $tahun) ?>"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Buku
                        </button>

                        <a href="<?= e(BASE_URL) ?>index.php?hal=buku"
                           class="btn btn-secondary">
                            Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../template/footer.php'; ?>
