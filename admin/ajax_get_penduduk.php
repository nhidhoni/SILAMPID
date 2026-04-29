<?php
// File: admin/ajax_get_penduduk.php
header('Content-Type: application/json');
include '../koneksi.php';

$nik = isset($_GET['nik']) ? mysqli_real_escape_string($conn, $_GET['nik']) : '';

if (empty($nik)) {
    echo json_encode(['success' => false, 'message' => 'NIK tidak boleh kosong']);
    exit();
}

$query = "SELECT * FROM penduduk WHERE nik = '$nik'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
    echo json_encode([
        'success' => true,
        'no_kk' => $data['no_kk'],
        'nama_lengkap' => $data['nama_lengkap'],
        'jenis_kelamin' => $data['jenis_kelamin'],
        'status_hubungan' => $data['status_hubungan'],
        'tempat_lahir' => $data['tempat_lahir'],
        'tanggal_lahir' => $data['tanggal_lahir'],
        'alamat' => $data['alamat']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'NIK tidak ditemukan dalam database penduduk']);
}
?>