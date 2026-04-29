<?php
// File: admin/ajax_cek_status.php
header('Content-Type: application/json');
include '../koneksi.php';

$kode = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';

if(empty($kode)) {
    echo json_encode(['success' => false, 'message' => 'Kode unik tidak boleh kosong!']);
    exit();
}

$query = "SELECT * FROM pengajuan WHERE kode_unik = '$kode'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
    $data['tgl_pengajuan'] = date('d/m/Y', strtotime($data['tgl_pengajuan']));
    $data['success'] = true;
    echo json_encode($data);
} else {
    echo json_encode(['success' => false, 'message' => 'Kode unik tidak ditemukan!']);
}
?>