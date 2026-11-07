
<?php

require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../config/supabase_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// ========================================
// 1. CEK ERROR GOOGLE
// ========================================

if (!empty($_GET['error'])) {
    exit(
        'Login Google gagal: ' .
        htmlspecialchars(
            $_GET['error_description'] ?? $_GET['error'],
            ENT_QUOTES,
            'UTF-8'
        )
    );
}

// ========================================
// 2. VALIDASI OAUTH STATE
// ========================================

$state = $_GET['state'] ?? '';
$sessionState = $_SESSION['supabase_oauth_state'] ?? '';

if (
    $state === '' ||
    $sessionState === '' ||
    !hash_equals($sessionState, $state)
) {
    exit('Validasi keamanan OAuth gagal. Silakan login kembali.');
}

// ========================================
// 3. AMBIL AUTHORIZATION CODE
// ========================================

$code = $_GET['code'] ?? '';
$verifier = $_SESSION['supabase_pkce_verifier'] ?? '';

if ($code === '' || $verifier === '') {
    exit('Kode autentikasi tidak ditemukan. Silakan login kembali.');
}

// ========================================
// 4. TUKAR CODE DENGAN TOKEN
// ========================================

$result = supabase_google_exchange_code(
    $code,
    $verifier
);

unset(
    $_SESSION['supabase_pkce_verifier'],
    $_SESSION['supabase_oauth_state']
);

if (
    $result['status'] !== 200 ||
    empty($result['data']['access_token'])
) {
    exit('Gagal mendapatkan token dari Supabase.');
}

$data = $result['data'];

// ========================================
// 5. VERIFIKASI USER
// ========================================

$verification = supabase_get_user(
    $data['access_token']
);

if (
    $verification['status'] !== 200 ||
    empty($verification['data']['id'])
) {
    exit('Verifikasi pengguna gagal.');
}

$userId = $verification['data']['id'];
$email = strtolower(trim(
    $verification['data']['email'] ?? ''
));

if ($email === '') {
    exit('Email Google tidak ditemukan.');
}

$nama = $verification['data']['user_metadata']['full_name']
    ?? $verification['data']['user_metadata']['name']
    ?? 'Pengguna';

// ========================================
// 6. CARI DATA PENGGUNA DI TABEL REGIS
// ========================================

$regisResult = supabase_request(
    '/rest/v1/regis?user_id=eq.' .
    rawurlencode($userId) .
    '&select=id,user_id,nama,email,role',
    'GET',
    null,
    $data['access_token']
);

if ($regisResult['status'] !== 200) {
    exit(
        'Gagal mengambil data pengguna. Status: ' .
        $regisResult['status']
    );
}

// ========================================
// 7. CEK DATA ANGGOTA
// ========================================

if (
    empty($regisResult['data']) ||
    !isset($regisResult['data'][0])
) {

    // Periksa apakah email sudah terdaftar
    $cekEmail = supabase_find_regis_by_email($email);

    if ($cekEmail['status'] !== 200) {
        exit('Gagal memeriksa email di database.');
    }

    if (!empty($cekEmail['data'])) {
        exit(
            'Email sudah terdaftar dengan identitas anggota lain. ' .
            'Silakan hubungi admin untuk menghubungkan akun.'
        );
    }

    // Pengguna baru otomatis menjadi anggota
    $insertResult = supabase_insert_regis(
        $userId,
        $nama,
        $email,
        'anggota'
    );

    if (
        $insertResult['status'] < 200 ||
        $insertResult['status'] >= 300
    ) {
        exit(
            'Gagal membuat data anggota. Status: ' .
            $insertResult['status']
        );
    }

    $regis = [
        'user_id' => $userId,
        'nama' => $nama,
        'email' => $email,
        'role' => 'anggota'
    ];

} else {

    $regis = $regisResult['data'][0];

    // Pastikan identitas email sesuai
    if (
        strtolower(trim($regis['email'] ?? '')) !== $email
    ) {
        exit('Email akun tidak sesuai dengan data anggota.');
    }
}

// ========================================
// 8. AMBIL ROLE
// ========================================

$role = strtolower(trim($regis['role'] ?? ''));

$allowedRoles = [
    'admin',
    'petugas',
    'anggota'
];

if (!in_array($role, $allowedRoles, true)) {
    exit('Role pengguna tidak valid.');
}

// ========================================
// 9. REGENERATE SESSION
// ========================================

session_regenerate_id(true);

// ========================================
// 10. SIMPAN SESSION DAN JWT
// ========================================

$_SESSION['user_id'] = $userId;
$_SESSION['user'] = $email;
$_SESSION['nama'] = $regis['nama'] ?? '';
$_SESSION['role'] = $role;

$_SESSION['access_token'] = $data['access_token'];
$_SESSION['refresh_token'] = $data['refresh_token'] ?? '';

$_SESSION['token_expires_at'] =
    time() + (int) ($data['expires_in'] ?? 3600);

// ========================================
// 11. ARAHKAN SESUAI ROLE
// ========================================

if ($role === 'admin' || $role === 'petugas') {

    header(
        'Location: ' .
        BASE_URL .
        'index.php?hal=dashboard'
    );
    exit;
}

if ($role === 'anggota') {

    header(
        'Location: ' .
        BASE_URL .
        'index.php?hal=anggota'
    );
    exit;
}
