<?php

require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../config/supabase_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$error = '';
$success = '';

$nama = '';
$email = '';


// ========================================
// PROSES REGISTER PUBLIC
// ========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';


    // ========================================
    // VALIDASI FORM
    // ========================================

    if (
        $nama === '' ||
        $email === '' ||
        $password === '' ||
        $konfirmasi === ''
    ) {

        $error = 'Semua kolom wajib diisi.';

    } elseif (strlen($nama) > 100) {

        $error = 'Nama maksimal 100 karakter.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Format email tidak valid.';

    } elseif (strlen($password) < 8) {

        $error = 'Password minimal harus 8 karakter.';

    } elseif ($password !== $konfirmasi) {

        $error = 'Konfirmasi password tidak sama.';

    } else {


        // ========================================
        // 1. CEK EMAIL DI TABEL REGIS
        // ========================================

        $cekRegis = supabase_find_regis_by_email($email);


        if ($cekRegis['status'] === 0) {

            $error = 'Tidak dapat terhubung ke database Supabase.';

        } elseif ($cekRegis['status'] !== 200) {

            $error = 'Gagal memeriksa data anggota.';

        } else {

            $dataRegis = $cekRegis['data'] ?? [];


            // ========================================
            // 2. JIKA EMAIL SUDAH ADA DI REGIS
            // ========================================

            if (!empty($dataRegis)) {

                $anggota = $dataRegis[0];

                $role = $anggota['role'] ?? '';

                $userIdLama = $anggota['user_id'] ?? null;


                // Jangan mengizinkan akun admin/petugas
                // dibuat ulang melalui Register Public

                if ($role !== 'anggota') {

                    $error = 'Email tersebut sudah terdaftar sebagai ' . $role . '. Silakan gunakan halaman login.';

                } elseif (!empty($userIdLama)) {

                    // Sudah mempunyai akun

                    $error = 'Email sudah terdaftar sebagai anggota. Silakan langsung login.';

                } else {

                    // Data anggota sudah ada,
                    // tetapi belum mempunyai user_id.
                    //
                    // Untuk tahap ini kita hentikan dulu
                    // agar tidak membuat duplikasi.

                    $error = 'Data anggota dengan email tersebut sudah terdaftar. Silakan hubungi petugas untuk mengaktifkan akun.';
                }


            } else {


                // ========================================
                // 3. EMAIL BELUM ADA → BUAT AKUN AUTH
                // ========================================

                $result = supabase_signup(
                    $nama,
                    $email,
                    $password
                );


                // ========================================
                // 4. CEK HASIL SIGNUP
                // ========================================

                if ($result['status'] === 0) {

                    $error = 'Tidak dapat terhubung ke Supabase. Periksa koneksi internet.';

                } else {

                    $data = $result['data'] ?? [];


                    // Ambil user ID dari Supabase Auth

                    $userId = $data['user']['id']
                        ?? $data['id']
                        ?? null;


                    // ========================================
                    // 5. AKUN BERHASIL DIBUAT
                    // ========================================

                    if (
                        in_array($result['status'], [200, 201], true) &&
                        !empty($userId)
                    ) {


                        // ========================================
                        // 6. SIMPAN KE REGIS
                        // ROLE = ANGGOTA
                        // ========================================

                        $insertRegis = supabase_insert_regis(
                            $userId,
                            $nama,
                            $email,
                            'anggota'
                        );


                        if (
                            $insertRegis['status'] >= 200 &&
                            $insertRegis['status'] < 300
                        ) {


                            // ========================================
                            // 7. HASIL REGISTER
                            // ========================================

                            if (
                                empty($data['session']) &&
                                empty($data['access_token'])
                            ) {

                                $success =
                                    'Pendaftaran berhasil! Akun kamu sudah dibuat sebagai anggota. Silakan periksa email untuk konfirmasi sebelum login.';

                            } else {

                                $success =
                                    'Pendaftaran berhasil! Akun kamu sudah dibuat sebagai anggota. Silakan login.';
                            }


                            $nama = '';
                            $email = '';

                        } else {

                            $insertData = $insertRegis['data'] ?? [];

                            $pesanInsert =
                                $insertData['message']
                                ?? $insertData['msg']
                                ?? '';

                            $error = $pesanInsert !== ''
                                ? $pesanInsert
                                : 'Akun berhasil dibuat, tetapi data anggota gagal disimpan.';
                        }


                    } else {


                        // ========================================
                        // 8. ERROR DARI SUPABASE AUTH
                        // ========================================

                        $pesan =
                            $data['msg']
                            ?? $data['message']
                            ?? $data['error_description']
                            ?? $data['error']
                            ?? '';

                        if ($pesan !== '') {

                            $error = $pesan;

                        } else {

                            $error =
                                'Pendaftaran gagal. Silakan periksa data dan coba lagi.';
                        }
                    }
                }
            }
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

    <title>Daftar Akun | Perpustakaan</title>

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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #eaf0ff, #f5f7fc);
            font-family: Arial, sans-serif;
        }

        .register-card {
            width: 100%;
            max-width: 480px;
            padding: 40px;
            background: white;
            border-radius: 18px;
            box-shadow: 0 15px 45px rgba(30, 55, 100, .13);
        }

        .register-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 15px;
            border-radius: 15px;
            background: #eaf1ff;
            color: #1769e8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .register-card h3 {
            color: #1d3150;
            font-weight: 700;
        }

        .form-label {
            color: #42516a;
            font-weight: 600;
            font-size: 14px;
        }

        .form-control {
            height: 47px;
            border-radius: 9px;
            border: 1px solid #dce3ed;
        }

        .form-control:focus {
            border-color: #367df0;
            box-shadow: 0 0 0 3px rgba(54, 125, 240, .12);
        }

        .btn-register {
            height: 48px;
            width: 100%;
            border: none;
            border-radius: 9px;
            color: white;
            background: #1769e8;
            font-weight: 600;
            transition: .2s;
        }

        .btn-register:hover {
            background: #1057c5;
        }

        .login-link {
            color: #1769e8;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {

            .register-card {
                padding: 28px 22px;
            }

        }

    </style>

</head>


<body>

<div class="register-card">


    <!-- JUDUL -->

    <div class="text-center mb-4">

        <div class="register-icon">

            <i class="bi bi-book-half"></i>

        </div>

        <h3>Buat Akun Baru</h3>

        <p class="text-secondary">
            Daftar untuk menggunakan Perpustakaan.
        </p>

    </div>


    <!-- PESAN ERROR -->

    <?php if ($error !== ''): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>

    <?php endif; ?>


    <!-- PESAN BERHASIL -->

    <?php if ($success !== ''): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars(
                $success,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>

    <?php endif; ?>


    <!-- FORM REGISTER -->

    <form
        method="POST"
        action="<?= BASE_URL ?>index.php?hal=login&aksi=daftar"
    >


        <div class="mb-3">

            <label
                for="nama"
                class="form-label"
            >
                Nama Lengkap
            </label>

            <input
                type="text"
                name="nama"
                id="nama"
                class="form-control"
                placeholder="Masukkan nama lengkap"
                value="<?= htmlspecialchars(
                    $nama,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                maxlength="100"
                required
            >

        </div>


        <div class="mb-3">

            <label
                for="email"
                class="form-label"
            >
                Email
            </label>

            <input
                type="email"
                name="email"
                id="email"
                class="form-control"
                placeholder="Masukkan email"
                value="<?= htmlspecialchars(
                    $email,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                required
            >

        </div>


        <div class="mb-3">

            <label
                for="password"
                class="form-label"
            >
                Password
            </label>

            <input
                type="password"
                name="password"
                id="password"
                class="form-control"
                placeholder="Minimal 8 karakter"
                minlength="8"
                autocomplete="new-password"
                required
            >

        </div>


        <div class="mb-4">

            <label
                for="konfirmasi_password"
                class="form-label"
            >
                Konfirmasi Password
            </label>

            <input
                type="password"
                name="konfirmasi_password"
                id="konfirmasi_password"
                class="form-control"
                placeholder="Ulangi password"
                minlength="8"
                autocomplete="new-password"
                required
            >

        </div>


        <button
            type="submit"
            class="btn-register"
        >

            <i class="bi bi-person-plus me-2"></i>

            Daftar Sekarang

        </button>

    </form>


    <!-- LINK LOGIN -->

    <div class="text-center mt-4">

        <span class="text-secondary">
            Sudah punya akun?
        </span>

        <a
            href="<?= BASE_URL ?>index.php?hal=login"
            class="login-link"
        >
            Login
        </a>

    </div>


</div>

</body>

</html>