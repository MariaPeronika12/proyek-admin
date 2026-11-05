<?php

require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../config/supabase_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Periksa apakah Google mengembalikan error
if (!empty($_GET['error'])) {
    exit('Login Google gagal: ' .
        htmlspecialchars($_GET['error_description'] ?? $_GET['error'], ENT_QUOTES, 'UTF-8')
    );
}

// Ambil authorization code
$code = $_GET['code'] ?? '';

// Ambil PKCE verifier
$verifier = $_SESSION['oauth_code_verifier'] ?? '';

if ($code === '' || $verifier === '') {
    exit('Kode autentikasi tidak ditemukan. Silakan login kembali.');
}

// Tukarkan authorization code dengan token Supabase
$result = supabase_request(
    '/auth/v1/token?grant_type=pkce',
    'POST',
    [
        'auth_code' => $code,
        'code_verifier' => $verifier
    ]
);

// Hapus verifier setelah digunakan
unset($_SESSION['oauth_code_verifier']);

if (
    $result['status'] !== 200 ||
    empty($result['data']['access_token'])
) {
    exit('Gagal mendapatkan token dari Supabase. Silakan coba login kembali.');
}

$data = $result['data'];

// Verifikasi pengguna
$verification = supabase_get_user(
    $data['access_token']
);

if (
    $verification['status'] !== 200 ||
    empty($verification['data']['id'])
) {
    exit('Verifikasi pengguna gagal.');
}

// Perbarui session ID
session_regenerate_id(true);

// Simpan informasi pengguna
$_SESSION['user_id'] = $verification['data']['id'];

$_SESSION['user'] = $verification['data']['email'] ?? '';

$_SESSION['access_token'] = $data['access_token'];

$_SESSION['refresh_token'] = $data['refresh_token'] ?? null;

$_SESSION['token_expires_at'] =
    time() + (int) ($data['expires_in'] ?? 3600);

// Arahkan ke dashboard
header('Location: ' . BASE_URL . 'index.php?hal=dashboard');
exit;