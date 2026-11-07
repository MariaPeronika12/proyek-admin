
<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: text/plain');

echo "1. PHP BERJALAN\n";

require_once __DIR__ . '/env.php';
echo "2. ENV BERHASIL\n";

require_once __DIR__ . '/config/supabase_auth.php';
echo "3. SUPABASE AUTH BERHASIL\n";

echo "4. SESSION BERHASIL\n";

echo "5. SEMUA PEMERIKSAAN SELESAI\n";
