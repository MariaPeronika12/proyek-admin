<?php
require_once __DIR__ . '/koneksi.php';

$username = 'admin';
$password = password_hash('rahasia123', PASSWORD_DEFAULT);

$stmt = $db->prepare(
    'INSERT INTO pengguna (username, password) VALUES (?, ?)'
);

$stmt->bind_param('ss', $username, $password);

if ($stmt->execute()) {
    echo 'Pengguna admin berhasil ditambahkan!';
} else {
    echo 'Gagal menambahkan pengguna: ' . $stmt->error;
}

$stmt->close();
$db->close();
