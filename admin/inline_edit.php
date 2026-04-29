<?php
// File: admin/inline_edit.php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

include '../koneksi.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$field = isset($_POST['field']) ? $_POST['field'] : '';
$value = isset($_POST['value']) ? $_POST['value'] : '';

$allowed_fields = ['kode_unik', 'tgl_pengajuan', 'nik_warga', 'nama_warga', 'jenis_surat', 'status', 'catatan_admin'];
if(!in_array($field, $allowed_fields)) {
    echo json_encode(['success' => false, 'error' => 'Field tidak diizinkan']);
    exit();
}

if($field == 'nik_warga' && strlen($value) != 16 && !empty($value)) {
    echo json_encode(['success' => false, 'error' => 'NIK harus 16 digit']);
    exit();
}

if($field == 'status') {
    $query_lama = "SELECT status FROM pengajuan WHERE id = $id";
    $result_lama = mysqli_query($conn, $query_lama);
    $status_lama = mysqli_fetch_assoc($result_lama)['status'];
}

$value_escaped = mysqli_real_escape_string($conn, $value);
$query = "UPDATE pengajuan SET $field = '$value_escaped' WHERE id = $id";

if(mysqli_query($conn, $query)) {
    if($field == 'status' && isset($status_lama) && $status_lama != $value) {
        $log = "INSERT INTO log_status (id_pengajuan, status_lama, status_baru, diubah_oleh) VALUES ($id, '$status_lama', '$value', '".$_SESSION['username']."')";
        mysqli_query($conn, $log);
        if($value == 'Selesai') {
            mysqli_query($conn, "UPDATE pengajuan SET tgl_selesai = CURDATE() WHERE id = $id");
        }
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}
?>