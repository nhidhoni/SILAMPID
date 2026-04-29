<?php
// File: admin/cek_data_penduduk.php - Cek Apakah Data Ada Sebelum Cetak
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo json_encode(['found' => false, 'message' => 'Unauthorized']);
    exit();
}

include '../koneksi.php';

$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';
$nilai = isset($_GET['nilai']) ? trim($_GET['nilai']) : '';

if(empty($jenis) || empty($nilai)) {
    echo json_encode(['found' => false, 'message' => 'Parameter tidak lengkap']);
    exit();
}

if($jenis == 'nik') {
    $query = "SELECT COUNT(*) as total FROM penduduk WHERE nik = '$nilai'";
    $result = mysqli_query($conn, $query);
    $count = mysqli_fetch_assoc($result)['total'];
    
    if($count > 0) {
        echo json_encode(['found' => true]);
    } else {
        echo json_encode(['found' => false, 'message' => "NIK $nilai tidak ditemukan"]);
    }
} elseif($jenis == 'kk') {
    $query = "SELECT COUNT(*) as total FROM penduduk WHERE no_kk = '$nilai'";
    $result = mysqli_query($conn, $query);
    $count = mysqli_fetch_assoc($result)['total'];
    
    if($count > 0) {
        echo json_encode(['found' => true]);
    } else {
        echo json_encode(['found' => false, 'message' => "No KK $nilai tidak ditemukan"]);
    }
} else {
    echo json_encode(['found' => false, 'message' => 'Jenis tidak valid']);
}
?>