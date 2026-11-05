<?php

require_once __DIR__ . '/env.php';

$db = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);

if ($db->connect_error) {
    error_log('Koneksi database gagal: ' . $db->connect_error);
    exit('Koneksi database gagal. Periksa konfigurasi database.');
}

$db->set_charset('utf8mb4');