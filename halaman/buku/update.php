<?php
require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../koneksi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit('ID buku tidak valid.');
}

$pesanError = '';

$stmt = $db->prepare(
    "SELECT id, judul, penulis, tahun FROM buku WHERE id = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();

$buku = $stmt->get_result()->fetch_assoc();

if (!$buku) {
    exit('Data buku tidak ditemukan.');
}

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
        $pesanError = 'Semua kolom wajib diisi dengan benar.';
        $buku['judul'] = $judul;
        $buku['penulis'] = $penulis;
        $buku['tahun'] = $tahunInput;
    } else {
        try {
            $stmt = $db->prepare(
                "UPDATE buku
                 SET judul = ?, penulis = ?, tahun = ?
                 WHERE id = ?"
            );
            $stmt->bind_param('ssii', $judul, $penulis, $tahun, $id);
            $stmt->execute();

            redirect('buku');
        } catch (mysqli_sql_exception $e) {
            $pesanError = 'Gagal mengubah data buku.';
        }
    }
}

$judulHalaman = 'Ubah Buku';
require_once __DIR__ . '/../../template/header.php';
require_once __DIR__ . '/../../template/sidebar.php';
?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid">
            <h3 class="mb-3">Ubah Buku</h3>

            <?php if ($pesanError !== ''): ?>
                <div class="alert alert-danger">
                    <?= e($pesanError) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST"
                          action="<?= e(BASE_URL) ?>index.php?hal=buku&aksi=update&id=<?= (int) $id ?>">

                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Buku</label>
                            <input
                                type="text"
                                id="judul"
                                name="judul"
                                class="form-control"
                                value="<?= e((string) $buku['judul']) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="penulis" class="form-label">Penulis</label>
                            <input
                                type="text"
                                id="penulis"
                                name="penulis"
                                class="form-control"
                                value="<?= e((string) $buku['penulis']) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun Terbit</label>
                            <input
                                type="number"
                                id="tahun"
                                name="tahun"
                                class="form-control"
                                min="1000"
                                max="2100"
                                value="<?= e((string) $buku['tahun']) ?>"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                        <a href="<?= e(BASE_URL) ?>index.php?hal=buku"
                           class="btn btn-secondary">
                            Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../template/footer.php'; ?>
