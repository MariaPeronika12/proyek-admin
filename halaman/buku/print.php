<?php
require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../koneksi.php';

$judul = 'Laporan Data Buku';

// Jika ada ID, cetak satu buku.
// Jika tidak ada ID, cetak semua buku.
$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $db->prepare(
        'SELECT id, judul, penu44lis, tahun FROM buku WHERE id = ?'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $result = $db->query(
        'SELECT id, judul, penulis, tahun FROM buku ORDER BY id ASC'
    );
    $data = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul) ?></title>
    <link rel="stylesheet"
          href="<?= BASE_URL ?>assets/css/adminlte.css">

    <style>
        body {
            padding: 25px;
            font-family: Arial, sans-serif;
            color: #000;
        }

        h2, p {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        .aksi {
            margin-bottom: 20px;
        }

        @media print {
            .aksi {
                display: none;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="aksi">
        <button type="button" onclick="window.print()">
            Cetak Laporan
        </button>
        <button type="button" onclick="window.close()">
            Tutup
        </button>
    </div>

    <h2>LAPORAN DATA BUKU</h2>
    <p>Perpustakaan</p>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Judul Buku</th>
                <th>Penulis</th>
                <th>Tahun</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data) > 0): ?>
                <?php foreach ($data as $i => $buku): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($buku['judul']) ?></td>
                        <td><?= e($buku['penulis']) ?></td>
                        <td><?= e((string) $buku['tahun']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Data buku tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
