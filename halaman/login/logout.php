<?php

require_once __DIR__ . '/../../env.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Kosongkan semua data session
$_SESSION = [];

// Hapus cookie session dari browser
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

// Hancurkan session
session_destroy();

// Kembali ke halaman login
header('Location: ' . BASE_URL . 'index.php?hal=login');
exit;
