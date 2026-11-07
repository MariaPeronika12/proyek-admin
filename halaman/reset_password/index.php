
<?php

require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../config/supabase_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$error = '';
$success = '';

// Pastikan OTP sudah berhasil diverifikasi
$accessToken = $_SESSION['access_token'] ?? '';

if (
    empty($_SESSION['reset_password_verified']) ||
    empty($accessToken)
) {
    header(
        'Location: ' .
        BASE_URL .
        'index.php?hal=forgot_password'
    );
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = $_POST['password'] ?? '';
    $konfirmasiPassword =
        $_POST['konfirmasi_password'] ?? '';

    if (
        $password === '' ||
        $konfirmasiPassword === ''
    ) {

        $error = 'Password wajib diisi.';

    } elseif (strlen($password) < 6) {

        $error = 'Password minimal 6 karakter.';

    } elseif ($password !== $konfirmasiPassword) {

        $error = 'Konfirmasi password tidak sama.';

    } else {

        // Simpan password baru ke Supabase
        $result = supabase_update_password(
            $accessToken,
            $password
        );

        if (
            $result['status'] >= 200 &&
            $result['status'] < 300
        ) {

            // Hapus session pemulihan password
            unset($_SESSION['access_token']);
            unset($_SESSION['refresh_token']);
            unset($_SESSION['otp_email']);
            unset($_SESSION['reset_password_verified']);

            $success =
                'Password berhasil diubah. Silakan login menggunakan password baru.';

        } else {

            $pesanError =
                $result['data']['msg']
                ?? $result['data']['message']
                ?? $result['error']
                ?? 'Terjadi kesalahan pada Supabase.';

            $error =
                'Gagal mengubah password: ' . $pesanError;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Reset Password | Perpustakaan</title>

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>assets/css/adminlte.css"
    >

</head>

<body class="login-page bg-body-secondary">

<div class="login-box">

    <div class="card card-outline card-primary">

        <div class="card-body login-card-body">

            <h3 class="text-center mb-3">
                Buat Password Baru
            </h3>

            <p class="text-center text-muted">
                Masukkan password baru untuk akun perpustakaan kamu.
            </p>

            <?php if ($error !== ''): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

            <?php endif; ?>

            <?php if ($success !== ''): ?>

                <div class="alert alert-success">
                    <?= htmlspecialchars(
                        $success,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

                <a
                    href="<?= BASE_URL ?>index.php?hal=login"
                    class="btn btn-primary w-100"
                >
                    Kembali ke Login
                </a>

            <?php else: ?>

                <form method="POST">

                    <div class="mb-3">

                        <label>Password Baru</label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Masukkan password baru"
                            minlength="6"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label>Konfirmasi Password</label>

                        <input
                            type="password"
                            name="konfirmasi_password"
                            class="form-control"
                            placeholder="Masukkan kembali password"
                            minlength="6"
                            required
                        >

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >
                        Simpan Password Baru
                    </button>

                </form>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>