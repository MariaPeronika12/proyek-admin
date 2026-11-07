
<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/config/supabase_auth.php';

echo "<h3>Tes Koneksi Supabase</h3>";

echo "URL Supabase: ";
echo htmlspecialchars(SUPABASE_URL);
echo "<br><br>";

$result = supabase_request(
    '/auth/v1/health',
    'GET'
);

echo "HTTP Status: " . $result['status'] . "<br>";

if ($result['status'] === 200) {
    echo "<h3 style='color:green'>Koneksi berhasil!</h3>";
} elseif ($result['status'] === 0) {
    echo "<h3 style='color:red'>Koneksi gagal!</h3>";
    echo "Error: " . htmlspecialchars(
        $result['error'] ?? 'Tidak diketahui'
    );
} else {
    echo "<h3>Server memberikan respons.</h3>";
    echo "<pre>";
    print_r($result['data']);
    echo "</pre>";
}
