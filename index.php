
<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/config/supabase_auth.php';

$page = $_GET['hal'] ?? 'dashboard';
$aksi = $_GET['aksi'] ?? 'index';

// ========================================
// DAFTAR ROUTE HALAMAN
// ========================================

$routes = [

    'dashboard' => [
        'index' => __DIR__ . '/halaman/dashboard/index.php',
    ],

    'login' => [
    'index'    => __DIR__ . '/halaman/login/index.php',
    'logout'   => __DIR__ . '/halaman/login/logout.php',
    'daftar'   => __DIR__ . '/halaman/login/daftar.php',
    'google'   => __DIR__ . '/halaman/login/google.php',
    'callback' => __DIR__ . '/halaman/login/callback.php',

    ],

    'buku' => [
        'index'  => __DIR__ . '/halaman/buku/index.php',
        'create' => __DIR__ . '/halaman/buku/create.php',
        'update' => __DIR__ . '/halaman/buku/update.php',
        'print'  => __DIR__ . '/halaman/buku/print.php',
    ],

    'anggota' => [
        'index' => __DIR__ . '/halaman/anggota/index.php',
    ],

];

// ========================================
// PERIKSA ROUTE
// ========================================

if (!isset($routes[$page][$aksi])) {

    http_response_code(404);
    exit('404 - Halaman tidak ditemukan');

}

// ========================================
// LOGIN, DAFTAR, DAN LOGOUT
// TIDAK MEMERLUKAN JWT DI ROUTER
// ========================================

if ($page === 'login') {

    require $routes[$page][$aksi];
    exit;

}

// ========================================
// PERIKSA TOKEN LOGIN
// ========================================

if (
    empty($_SESSION['access_token']) ||
    empty($_SESSION['refresh_token'])
) {

    header('Location: ' . BASE_URL . 'index.php?hal=login');
    exit;

}

// ========================================
// VERIFIKASI ACCESS TOKEN KE SUPABASE
// ========================================

$hasilVerifikasi = supabase_get_user(
    $_SESSION['access_token']
);

// ========================================
// JIKA ACCESS TOKEN TIDAK VALID,
// COBA REFRESH TOKEN
// ========================================

if ($hasilVerifikasi['status'] !== 200) {

    $hasilRefresh = supabase_refresh_token(
        $_SESSION['refresh_token']
    );

    // Jika refresh berhasil
    if (
        $hasilRefresh['status'] === 200 &&
        !empty($hasilRefresh['data']['access_token']) &&
        !empty($hasilRefresh['data']['refresh_token'])
    ) {

        // Simpan token terbaru
        $_SESSION['access_token'] =
            $hasilRefresh['data']['access_token'];

        $_SESSION['refresh_token'] =
            $hasilRefresh['data']['refresh_token'];

        $_SESSION['token_expires_at'] =
            time() + (int) ($hasilRefresh['data']['expires_in'] ?? 3600);

        // Verifikasi ulang menggunakan token baru
        $hasilVerifikasi = supabase_get_user(
            $_SESSION['access_token']
        );

    }

}

// ========================================
// JIKA VERIFIKASI DAN REFRESH GAGAL
// HAPUS SESSION DAN KEMBALI KE LOGIN
// ========================================

if (
    $hasilVerifikasi['status'] !== 200 ||
    empty($hasilVerifikasi['data']['id'])
) {

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );

    }

    session_destroy();

    header('Location: ' . BASE_URL . 'index.php?hal=login');
    exit;

}

// ========================================
// PERBARUI IDENTITAS PENGGUNA
// ========================================

$_SESSION['user_id'] =
    $hasilVerifikasi['data']['id'];

$_SESSION['user'] =
    $hasilVerifikasi['data']['email'] ?? '';

// ========================================
// PASTIKAN FILE HALAMAN TERSEDIA
// ========================================

if (!is_file($routes[$page][$aksi])) {

    http_response_code(404);
    exit('File halaman tidak ditemukan');

}

// ========================================
// TAMPILKAN HALAMAN
// ========================================

require $routes[$page][$aksi];

