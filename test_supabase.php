<?php

require_once __DIR__ . '/config/supabase_auth.php';

$result = supabase_request('/auth/v1/settings');

echo "<h2>Pengujian Koneksi Supabase</h2>";

if ($result['status'] === 200) {
    echo "<p style='color:green'>Koneksi Supabase BERHASIL!</p>";
} else {
    echo "<p style='color:red'>Koneksi gagal!</p>";
    echo "HTTP Status: " . $result['status'];
    echo "<pre>";
    print_r($result);
    echo "</pre>";
}