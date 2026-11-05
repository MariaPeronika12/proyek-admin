<?php
session_start();

require_once __DIR__ . '/env.php';

$verifier = bin2hex(random_bytes(32));
$challenge = rtrim(strtr(
    base64_encode(hash('sha256', $verifier, true)),
    '+/',
    '-_'
), '=');

$_SESSION['github_code_verifier'] = $verifier;

$callbackUrl = rtrim(BASE_URL, '/') . '/oauth-callback.php';

$params = http_build_query([
    'provider' => 'github',
    'redirect_to' => $callbackUrl,
    'code_challenge' => $challenge,
    'code_challenge_method' => 's256'
]);

header(
    'Location: ' . rtrim(SUPABASE_URL, '/') .
    '/auth/v1/authorize?' . $params
);
exit;