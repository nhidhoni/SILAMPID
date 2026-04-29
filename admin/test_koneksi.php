<?php
// File: admin/test_koneksi.php
include '../koneksi.php';
echo "<h2>Status Koneksi Database</h2>";
echo "Host: localhost<br>";
echo "Database: db_silampid_desa<br>";
echo "<hr>";
if($conn) {
    echo "✅ Koneksi database BERHASIL!";
} else {
    echo "❌ Koneksi database GAGAL!";
}
?>