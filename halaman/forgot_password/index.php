
<?php

require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../config/supabase_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = strtolower(trim($_POST['email'] ?? ''));

    if ($email === '') {

        $error = 'Email wajib diisi.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Format email tidak valid.';

    } else {

        // CEK EMAIL DI TABEL REGIS
        $cek = supabase_find_regis_by_email($email);

        if ($cek['status'] !== 200) {

            $error = 'Gagal mengecek anggota. Status: '
                . $cek['status']
                . ' | Detail: '
                . ($cek['error'] ?? json_encode($cek['data']));

        } elseif (empty($cek['data'])) {

            $error = 'Email belum terdaftar sebagai anggota.';

        } else {

            // SIMPAN EMAIL KE SESSION
            $_SESSION['otp_email'] = $email;

            // HAPUS STATUS RESET LAMA
            unset($_SESSION['reset_password_verified']);
            unset($_SESSION['reset_access_token']);
            unset($_SESSION['access_token']);
            unset($_SESSION['refresh_token']);

            // KIRIM OTP RECOVERY
            $result = supabase_send_recovery_otp(
                $email,
                BASE_URL . 'index.php?hal=otp'
            );

            if (
                $result['status'] >= 200 &&
                $result['status'] < 300
            ) {

                header(
                    'Location: ' .
                    BASE_URL .
                    'index.php?hal=otp'
                );
                exit;

            } else {

                $detail = $result['data']['msg']
                    ?? $result['data']['message']
                    ?? $result['data']['error_description']
                    ?? $result['error']
                    ?? json_encode($result['data']);

                $error = 'Gagal mengirim OTP. Status: '
                    . $result['status']
                    . ' | Detail: '
                    . $detail;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Lupa Password | Perpustakaan</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>assets/css/adminlte.css">

</head>

<body class="login-page bg-body-secondary">

<div class="login-box">

    <div class="card card-outline card-primary">

        <div class="card-body login-card-body">

            <h3 class="text-center mb-3">
                Lupa Password
            </h3>

            <p class="text-center text-muted">
                Masukkan email terdaftar untuk menerima
                kode OTP 6 digit.
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

            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">

                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan email"
                        value="<?= htmlspecialchars(
                            $_POST['email'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100">
                    Kirim OTP
                </button>

            </form>

            <div class="text-center mt-3">

                <a href="<?= BASE_URL ?>index.php?hal=login">
                    Kembali ke Login
                </a>

            </div>

        </div>

    </div>

</div>

</body>
</html>