
<?php

// ========================================
// 1. PENGATURAN SESSION PHP
// ========================================

$sessionPath = __DIR__ . '/storage/sessions';

if (!is_dir($sessionPath)) {
    if (!mkdir($sessionPath, 0775, true) && !is_dir($sessionPath)) {
        die('Gagal membuat folder penyimpanan session.');
    }
}

if (!is_writable($sessionPath)) {
    die('Folder storage/sessions tidak memiliki izin tulis.');
}

ini_set('session.save_handler', 'files');
ini_set('session.save_path', $sessionPath);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========================================
// 2. FILE KONFIGURASI
// ========================================

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/config/supabase_auth.php';

// ========================================
// 3. ROUTING
// ========================================

$page = $_GET['hal'] ?? 'dashboard';
$aksi = $_GET['aksi'] ?? 'index';

// ========================================
// 4. DAFTAR ROUTE HALAMAN
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

    'forgot_password' => [
        'index' => __DIR__ . '/halaman/forgot_password/index.php',
    ],

    'reset_password' => [
        'index' => __DIR__ . '/halaman/reset_password/index.php',
    ],

    'otp' => [
        'index' => __DIR__ . '/halaman/otp/index.php',
    ],

    'buku' => [
        'index'  => __DIR__ . '/halaman/buku/index.php',
        'create' => __DIR__ . '/halaman/buku/create.php',
        'update' => __DIR__ . '/halaman/buku/update.php',
        'print'  => __DIR__ . '/halaman/buku/print.php',
    ],

    'anggota' => [
        'index'     => __DIR__ . '/halaman/anggota/index.php',
        'dashboard' => __DIR__ . '/halaman/anggota/dashboard.php',
    ],

];

// ========================================
// 5. PERIKSA ROUTE
// ========================================

if (!isset($routes[$page][$aksi])) {
    http_response_code(404);
    exit('404 - Halaman tidak ditemukan.');
}

// ========================================
// 6. HALAMAN PUBLIK
// ========================================

$halamanPublik = [
    'login',
    'forgot_password',
    'reset_password',
    'otp'
];

if (in_array($page, $halamanPublik, true)) {

    $file = $routes[$page][$aksi];

    if (!is_file($file)) {
        http_response_code(404);
        exit('File halaman tidak ditemukan.');
    }

    require $file;
    exit;
}

// ========================================
// 7. PERIKSA TOKEN LOGIN
// ========================================

if (
    empty($_SESSION['access_token']) ||
    empty($_SESSION['refresh_token'])
) {

    header(
        'Location: ' . BASE_URL . 'index.php?hal=login'
    );

    exit;
}

// ========================================
// 8. VERIFIKASI ACCESS TOKEN SUPABASE
// ========================================

$hasilVerifikasi = supabase_get_user(
    $_SESSION['access_token']
);

// ========================================
// 9. REFRESH TOKEN JIKA KEDALUWARSA
// ========================================

if ($hasilVerifikasi['status'] !== 200) {

    $hasilRefresh = supabase_refresh_token(
        $_SESSION['refresh_token']
    );

    if (
        $hasilRefresh['status'] === 200 &&
        !empty($hasilRefresh['data']['access_token']) &&
        !empty($hasilRefresh['data']['refresh_token'])
    ) {

        $_SESSION['access_token'] =
            $hasilRefresh['data']['access_token'];

        $_SESSION['refresh_token'] =
            $hasilRefresh['data']['refresh_token'];

        $_SESSION['token_expires_at'] =
            time() + (int) (
                $hasilRefresh['data']['expires_in'] ?? 3600
            );

        $hasilVerifikasi = supabase_get_user(
            $_SESSION['access_token']
        );
    }
}

// ========================================
// 10. JIKA TOKEN TIDAK VALID
// ========================================

if (
    $hasilVerifikasi['status'] !== 200 ||
    empty($hasilVerifikasi['data']['id'])
) {

    $_SESSION = [];

    session_regenerate_id(true);
    session_destroy();

    header(
        'Location: ' . BASE_URL . 'index.php?hal=login'
    );

    exit;
}

// ========================================
// 11. IDENTITAS PENGGUNA
// ========================================

$userId = $hasilVerifikasi['data']['id'];

$_SESSION['user_id'] = $userId;

$_SESSION['user'] =
    $hasilVerifikasi['data']['email'] ?? '';

// ========================================
// 12. AMBIL DATA ROLE DARI TABEL REGIS
// ========================================

$regis = supabase_request(
    '/rest/v1/regis?user_id=eq.' .
    rawurlencode($userId) .
    '&select=nama,email,role',
    'GET',
    null,
    $_SESSION['access_token']
);

if (
    $regis['status'] !== 200 ||
    empty($regis['data'][0]['role'])
) {

    http_response_code(403);
    exit('Role pengguna tidak ditemukan di database.');
}

// ========================================
// 13. SIMPAN ROLE KE SESSION
// ========================================

$role = strtolower(
    trim($regis['data'][0]['role'])
);

$_SESSION['role'] = $role;

$_SESSION['nama'] =
    $regis['data'][0]['nama'] ?? '';

// ========================================
// 14. VALIDASI ROLE
// ========================================

$allowedRoles = [
    'admin',
    'petugas',
    'anggota'
];

if (!in_array($role, $allowedRoles, true)) {

    http_response_code(403);
    exit('Role pengguna tidak valid.');
}

// ========================================
// 15. HAK AKSES BERDASARKAN ROLE
// ========================================

if ($role === 'admin') {

    $aksesDiizinkan = [
        'dashboard',
        'buku',
        'anggota'
    ];

} elseif ($role === 'petugas') {

    $aksesDiizinkan = [
        'dashboard',
        'buku',
        'anggota'
    ];

} else {

    $aksesDiizinkan = [
        'anggota',
        'buku'
    ];
}

// ========================================
// 16. PERIKSA HAK AKSES HALAMAN
// ========================================

if (!in_array($page, $aksesDiizinkan, true)) {

    http_response_code(403);

    exit(
        '403 - Kamu tidak memiliki hak akses ke halaman ini.'
    );
}

// ========================================
// 17. BATASAN KHUSUS ANGGOTA
// ========================================

// Anggota hanya boleh melihat katalog buku

if (
    $role === 'anggota' &&
    $page === 'buku' &&
    $aksi !== 'index'
) {

    http_response_code(403);

    exit('Anggota hanya dapat melihat katalog buku.');
}

// Anggota hanya boleh membuka dashboard anggota

if (
    $role === 'anggota' &&
    $page === 'anggota' &&
    $aksi !== 'dashboard'
) {

    http_response_code(403);

    exit('Halaman ini tidak dapat diakses.');
}

// ========================================
// 18. PERIKSA FILE HALAMAN
// ========================================

$file = $routes[$page][$aksi];

if (!is_file($file)) {

    http_response_code(404);

    exit('File halaman tidak ditemukan.');
}

// ========================================
// 19. TAMPILKAN HALAMAN
// ========================================

require $file;
