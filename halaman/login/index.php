
<?php

require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../config/supabase_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$error = '';


// ========================================
// MULAI LOGIN GOOGLE
// ========================================

if (
    isset($_GET['aksi']) &&
    $_GET['aksi'] === 'google'
) {
    $callbackUrl = BASE_URL . 'index.php?hal=login';

    $googleUrl = supabase_google_authorize_url(
        $callbackUrl
    );

    header('Location: ' . $googleUrl);
    exit;
}


// ========================================
// CALLBACK GOOGLE
// ========================================

if (isset($_GET['code'])) {

    $returnedState = $_GET['state'] ?? '';
    $savedState = $_SESSION['supabase_oauth_state'] ?? '';

    if (
        empty($savedState) ||
        !hash_equals($savedState, $returnedState)
    ) {
        $error = 'Verifikasi keamanan OAuth gagal. Silakan coba lagi.';

    } elseif (empty($_SESSION['supabase_pkce_verifier'])) {

        $error = 'Sesi OAuth kedaluwarsa. Silakan login kembali.';

    } else {

        $verifier = $_SESSION['supabase_pkce_verifier'];

        unset(
            $_SESSION['supabase_pkce_verifier'],
            $_SESSION['supabase_oauth_state']
        );

        $result = supabase_google_exchange_code(
            $_GET['code'],
            $verifier
        );

        if (
            $result['status'] === 200 &&
            !empty($result['data']['access_token'])
        ) {

            $data = $result['data'];

            $verification = supabase_get_user(
                $data['access_token']
            );

            if (
                $verification['status'] === 200 &&
                !empty($verification['data']['id'])
            ) {

                session_regenerate_id(true);

                $_SESSION['user_id'] =
                    $verification['data']['id'];

                $_SESSION['user'] =
                    $verification['data']['email'] ?? '';

                $_SESSION['access_token'] =
                    $data['access_token'];

                $_SESSION['refresh_token'] =
                    $data['refresh_token'] ?? null;

                $_SESSION['token_expires_at'] =
                    time() + ($data['expires_in'] ?? 3600);

                header(
                    'Location: ' . BASE_URL .
                    'index.php?hal=dashboard'
                );
                exit;

            } else {

                $error = 'Token Google berhasil diterima, tetapi verifikasi pengguna gagal.';
            }

        } else {

            $error = 'Login Google gagal. Periksa konfigurasi Supabase dan coba kembali.';
        }
    }
}


// ========================================
// JIKA GOOGLE MENGIRIM ERROR
// ========================================

if (isset($_GET['error'])) {
    $error = 'Login Google dibatalkan atau gagal: ' .
        htmlspecialchars(
            $_GET['error_description'] ??
            $_GET['error'],
            ENT_QUOTES,
            'UTF-8'
        );
}


// Jika sudah memiliki JWT, verifikasi dahulu ke Supabase
if (!empty($_SESSION['access_token'])) {

    $verification = supabase_get_user(
        $_SESSION['access_token']
    );

    if (
        $verification['status'] === 200 &&
        !empty($verification['data']['id'])
    ) {
        header('Location: ' . BASE_URL . 'index.php?hal=dashboard');
        exit;
    }

    $_SESSION = [];
    session_regenerate_id(true);
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {

        $error = 'Email dan password wajib diisi.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Format email tidak valid.';

    } else {

        $result = supabase_login($email, $password);

        if (
            $result['status'] === 200 &&
            !empty($result['data']['access_token'])
        ) {

            $data = $result['data'];

            $verification = supabase_get_user(
                $data['access_token']
            );

            if (
                $verification['status'] === 200 &&
                !empty($verification['data']['id'])
            ) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $verification['data']['id'];
                $_SESSION['user'] = $verification['data']['email'];
                $_SESSION['access_token'] = $data['access_token'];
                $_SESSION['refresh_token'] = $data['refresh_token'] ?? null;
                $_SESSION['token_expires_at'] = time() + ($data['expires_in'] ?? 3600);

                header(
                    'Location: ' . BASE_URL . 'index.php?hal=dashboard'
                );
                exit;

            } else {

                $error = 'Verifikasi JWT gagal. Silakan coba lagi.';
            }

        } elseif ($result['status'] === 0) {

            $error = 'Tidak dapat terhubung ke Supabase. Periksa koneksi internet.';

        } else {

            $error = 'Email atau password salah.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | Perpustakaan Admin</title>

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>assets/css/adminlte.css"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #eaf0ff, #f5f7fc);
        }

        .login-wrapper {
            width: 100%;
            max-width: 1050px;
            min-height: 570px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(30, 55, 100, .13);
        }

        /* PANEL KIRI */

        .login-left {
            background: linear-gradient(145deg, #dceaff, #edf5ff);
            padding: 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,.35);
            top: -100px;
            left: -100px;
        }

        .login-left::after {
            content: "";
            position: absolute;
            width: 230px;
            height: 230px;
            border-radius: 50%;
            background: rgba(255,255,255,.3);
            bottom: -100px;
            right: -80px;
        }

        .illustration {
            position: relative;
            z-index: 1;
            width: 250px;
            height: 230px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .book-art {
            font-size: 130px;
            color: #2876e8;
            filter: drop-shadow(0 12px 10px rgba(30,80,150,.18));
        }

        .floating-icon {
            position: absolute;
            background: white;
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2876e8;
            font-size: 25px;
            box-shadow: 0 8px 20px rgba(30,80,150,.12);
        }

        .icon-one {
            top: 15px;
            left: 5px;
        }

        .icon-two {
            right: 0;
            bottom: 15px;
            color: #f19a38;
        }

        .login-left h2 {
            position: relative;
            z-index: 1;
            color: #193b70;
            font-size: 27px;
            font-weight: 700;
            margin-top: 15px;
        }

        .login-left p {
            position: relative;
            z-index: 1;
            color: #62748d;
            line-height: 1.8;
            max-width: 340px;
        }

        /* PANEL KANAN */

        .login-right {
            padding: 55px 65px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: #eaf1ff;
            color: #1769e8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 20px;
        }

        .login-right h3 {
            color: #1d3150;
            font-size: 27px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #8995a7;
            margin-bottom: 27px;
            font-size: 14px;
        }

        .form-label {
            color: #42516a;
            font-weight: 600;
            font-size: 14px;
        }

        .form-control {
            height: 48px;
            border-radius: 9px;
            border: 1px solid #dce3ed;
            padding: 12px 15px;
        }

        .form-control:focus {
            border-color: #367df0;
            box-shadow: 0 0 0 3px rgba(54,125,240,.12);
        }

        .btn-login {
            height: 48px;
            width: 100%;
            background: #1769e8;
            border: none;
            border-radius: 9px;
            color: white;
            font-weight: 600;
            margin-top: 8px;
        }

        .btn-login:hover {
            background: #1057c5;
            color: white;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #9aa5b5;
            font-size: 13px;
            margin: 25px 0 18px;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e9ef;
        }

        .social-btn {
            height: 45px;
            border: 1px solid #dce3ed;
            background: white;
            border-radius: 9px;
            color: #34445c;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 9px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .social-btn:hover {
            background: #f5f8fc;
            color: #1769e8;
        }

        .register-text {
            margin-top: 25px;
            text-align: center;
            color: #8995a7;
            font-size: 14px;
        }

        .register-text a {
            color: #1769e8;
            text-decoration: none;
            font-weight: 600;
        }

        .register-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {

            body {
                padding: 15px;
            }

            .login-wrapper {
                grid-template-columns: 1fr;
                max-width: 470px;
            }

            .login-left {
                padding: 25px;
            }

            .illustration {
                height: 140px;
            }

            .book-art {
                font-size: 90px;
            }

            .login-left h2 {
                font-size: 22px;
                margin-top: 5px;
            }

            .login-left p {
                margin-bottom: 0;
            }

            .login-right {
                padding: 35px 25px;
            }
        }

    </style>

</head>

<body>

<div class="login-wrapper">

    <!-- BAGIAN KIRI -->
    <div class="login-left">

        <div class="illustration">

            <div class="floating-icon icon-one">
                <i class="bi bi-book"></i>
            </div>

            <i class="bi bi-journal-bookmark-fill book-art"></i>

            <div class="floating-icon icon-two">
                <i class="bi bi-pencil"></i>
            </div>

        </div>

        <h2>Selamat Datang!</h2>

        <p>
            Jelajahi dunia pengetahuan bersama
            Perpustakaan Admin. Kelola koleksi buku
            dan data anggota dengan lebih mudah.
        </p>

    </div>

    <!-- BAGIAN KANAN -->
    <div class="login-right">

        <div class="brand-icon">
            <i class="bi bi-book-half"></i>
        </div>

        <h3>Login Perpustakaan</h3>

        <p class="subtitle">
            Hai, selamat datang kembali! Silakan masuk ke akunmu.
        </p>

        <?php if ($error !== ''): ?>

            <div class="alert alert-danger">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>

        <form
            method="POST"
            action="<?= BASE_URL ?>index.php?hal=login"
        >

            <div class="mb-3">

                <label for="email" class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    placeholder="Masukkan email"
                    autocomplete="username"
                    value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >

            </div>

            <div class="mb-3">

                <label for="password" class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >

            </div>

            <button type="submit" class="btn btn-login">

                <i class="bi bi-box-arrow-in-right me-2"></i>
                Masuk

            </button>

        </form>

        <div class="divider">
            Atau masuk menggunakan
        </div>

        <div class="row g-2">

            <div class="col-6">                
                <a href="<?= BASE_URL ?>index.php?hal=login&aksi=google"
                class="social-btn">

                    <i class="bi bi-google"></i>
                    Google

                </a>
            </div>

            <div class="col-6">
                <a href="<?= BASE_URL ?>github-login.php" class="social-btn">
                    <i class="bi bi-github"></i>
                    GitHub
                </a>
            </div>

        </div>

        <div class="register-text">

            Belum punya akun?

            <a href="<?= BASE_URL ?>index.php?hal=login&aksi=daftar">
                Daftar sekarang
            </a>

        </div>

    </div>

</div>

</body>
</html>
