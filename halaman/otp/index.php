
<?php

require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../config/supabase_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$error = '';

$email = $_SESSION['otp_email'] ?? '';

if ($email === '') {
    header('Location: ' . BASE_URL . 'index.php?hal=forgot_password');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $otp = trim($_POST['otp'] ?? '');

    if ($otp === '') {

        $error = 'Kode OTP wajib diisi.';

    } elseif (!preg_match('/^[0-9]{6}$/', $otp)) {

        $error = 'Kode OTP harus terdiri dari 6 angka.';

    } else {

        // VERIFIKASI OTP RECOVERY
        $result = supabase_verify_recovery_otp(
            $email,
            $otp
        );

        if (
            $result['status'] === 200 &&
            !empty($result['data']['access_token'])
        ) {

            // SIMPAN TOKEN RECOVERY
            $_SESSION['reset_access_token'] =
                $result['data']['access_token'];

            $_SESSION['reset_refresh_token'] =
                $result['data']['refresh_token'] ?? '';

            $_SESSION['reset_password_verified'] = true;

            // HAPUS OTP EMAIL SETELAH VERIFIKASI
            unset($_SESSION['otp_email']);

            // PINDAH KE RESET PASSWORD
            header(
                'Location: ' .
                BASE_URL .
                'index.php?hal=reset_password'
            );
            exit;

        } else {

            $pesan =
                $result['data']['msg']
                ?? $result['data']['message']
                ?? $result['data']['error_description']
                ?? $result['error']
                ?? 'Kode OTP salah atau sudah kedaluwarsa.';

            $error = 'Verifikasi OTP gagal: ' . $pesan;
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

    <title>Verifikasi OTP | Perpustakaan</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>assets/css/adminlte.css">

</head>

<body class="login-page bg-body-secondary">

<div class="login-box">

    <div class="card card-outline card-primary">

        <div class="card-body login-card-body">

            <h3 class="text-center mb-3">
                Verifikasi OTP
            </h3>

            <p class="text-center text-muted">
                Masukkan kode OTP 6 digit yang dikirim ke:
            </p>

            <p class="text-center">
                <strong>
                    <?= htmlspecialchars(
                        $email,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>
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

            <form method="POST">

                <div class="mb-3">

                    <label for="otp">Kode OTP</label>

                    <input
                        type="text"
                        id="otp"
                        name="otp"
                        class="form-control text-center"
                        placeholder="Masukkan 6 digit OTP"
                        maxlength="6"
                        minlength="6"
                        pattern="[0-9]{6}"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        required
                    >

                </div>

                <button type="submit"
                        class="btn btn-primary w-100">
                    Verifikasi OTP
                </button>

            </form>

            <div class="text-center mt-3">

                <a href="<?= BASE_URL ?>index.php?hal=forgot_password">
                    Kirim OTP Baru
                </a>

            </div>

        </div>

    </div>

</div>

</body>
</html>