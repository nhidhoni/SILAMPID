<?php
// File: koneksi.php
// Konfigurasi database untuk SILAMPID

$host = 'localhost';              // Server database (biasanya localhost)
$user = 'root';                   // Username MySQL (default: root)
$password = '';                   // Password MySQL (default: kosong untuk XAMPP)
$database = 'db_silampid_desa';   // Nama database

// Buat koneksi
$conn = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8 untuk mendukung karakter khusus
mysqli_set_charset($conn, "utf8mb4");

// Catatan: 
// - Untuk XAMPP: user = 'root', password = ''
// - Untuk Laragon: user = 'root', password = ''
// - Untuk MySQL terinstal sendiri: sesuaikan dengan konfigurasi Anda
?>