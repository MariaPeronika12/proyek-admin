<?php
session_start();

require_once __DIR__ . '/env.php';

$baseUrl = rtrim(BASE_URL, '/');

function tampilkanError($pesan) {
    echo '<h3>Login GitHub gagal</h3>';
    echo '<p>' . htmlspecialchars($pesan) . '</p>';
    echo '<a href="' . htmlspecialchars(BASE_URL) .
         'views/auth/login.php">Kembali ke Login</a>';
    exit;
}

if (isset($_GET['error'])) {
    tampilkanError($_GET['error_description'] ?? $_GET['error']);
}

$code = $_GET['code'] ?? '';
$verifier = $_SESSION['github_code_verifier'] ?? '';

if (!$code || !$verifier) {
    tampilkanError('Kode login atau verifikasi tidak ditemukan. Silakan coba lagi.');
}

unset($_SESSION['github_code_verifier']);

$url = rtrim(SUPABASE_URL, '/') . '/auth/v1/token?grant_type=pkce';

$payload = json_encode([
    'auth_code' => $code,
    'code_verifier' => $verifier
]);

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'apikey: ' . SUPABASE_PUBLISHABLE_KEY
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

if ($response === false) {
    tampilkanError('Koneksi ke Supabase gagal: ' . $curlError);
}

$data = json_decode($response, true);

if ($httpCode < 200 || $httpCode >= 300 || empty($data['access_token'])) {
    tampilkanError($data['msg'] ?? $data['message'] ?? 'Gagal mendapatkan token dari Supabase.');
}

$user = $data['user'] ?? [];

session_regenerate_id(true);

$_SESSION['access_token'] = $data['access_token'];
$_SESSION['refresh_token'] = $data['refresh_token'] ?? '';
$_SESSION['user_id'] = $user['id'] ?? '';
$_SESSION['user_email'] = $user['email'] ?? '';
$_SESSION['user'] = $user;

// Sesuaikan alamat ini jika lokasi dashboard berbeda.
header('Location: ' . $baseUrl . '/views/dashboard/index.php');
exit;