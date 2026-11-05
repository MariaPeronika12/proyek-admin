<?php

require_once __DIR__ . '/../../env.php';
require_once __DIR__ . '/../../config/supabase_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Pastikan konfigurasi Supabase tersedia
if (
    !defined('SUPABASE_URL') ||
    !defined('SUPABASE_PUBLISHABLE_KEY') ||
    empty(SUPABASE_URL) ||
    empty(SUPABASE_PUBLISHABLE_KEY)
) {
    exit('Konfigurasi Supabase belum lengkap.');
}

// Buat PKCE verifier
$verifier = rtrim(
    strtr(base64_encode(random_bytes(32)), '+/', '-_'),
    '='
);

// Buat challenge SHA256
$challenge = rtrim(
    strtr(
        base64_encode(hash('sha256', $verifier, true)),
        '+/',
        '-_'
    ),
    '='
);

// Simpan verifier ke session
$_SESSION['oauth_code_verifier'] = $verifier;

// Alamat callback aplikasi
$callbackUrl = BASE_URL . 'index.php?hal=login&aksi=callback';

// URL autentikasi Supabase
$params = [
    'provider' => 'google',
    'redirect_to' => $callbackUrl,
    'code_challenge' => $challenge,
    'code_challenge_method' => 's256'
];

$authUrl = rtrim(SUPABASE_URL, '/') .
    '/auth/v1/authorize?' .
    http_build_query($params);

// Arahkan pengguna ke Google melalui Supabase
header('Location: ' . $authUrl);
exit;