<?php

// ========================================
// KONFIGURASI APLIKASI
// ========================================

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Perpustakaan Admin');
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/proyek-admin/');
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}


// ========================================
// DATABASE
// ========================================

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'perpustakaan');
}


// ========================================
// SUPABASE
// ========================================

if (!defined('SUPABASE_URL')) {
    define(
        'SUPABASE_URL',
        'https://ousbeqkdnwnsaomeihsb.supabase.co'
    );
}

if (!defined('SUPABASE_PUBLISHABLE_KEY')) {
    define(
        'SUPABASE_PUBLISHABLE_KEY',
        'sb_publishable_7kgKBZjwljdWQkSMQJdAuw_Sg9-pRiM'
    );
}


// ========================================
// GOOGLE OAUTH
// ========================================

if (!defined('GOOGLE_CLIENT_ID')) {
    define(
        'GOOGLE_CLIENT_ID',
        '151359590467-34glllo13lnkfiuqg31o0a9h6319mna5.apps.googleusercontent.com'
    );
}

if (!defined('GOOGLE_CLIENT_SECRET')) {
    define(
        'GOOGLE_CLIENT_SECRET',
        'ISI_GOOGLE_CLIENT_SECRET_KAMU'
    );
}


// ========================================
// KEAMANAN OUTPUT
// ========================================

if (!function_exists('e')) {
    function e(?string $teks): string
    {
        return htmlspecialchars(
            $teks ?? '',
            ENT_QUOTES,
            'UTF-8'
        );
    }
}


// ========================================
// REDIRECT
// ========================================

if (!function_exists('redirect')) {
    function redirect(
        string $hal,
        string $aksi = 'index'
    ): never {
        header(
            'Location: ' . BASE_URL .
            'index.php?hal=' . urlencode($hal) .
            '&aksi=' . urlencode($aksi)
        );
        exit;
    }
}