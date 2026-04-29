<?php
// File: admin/get_detail_pengajuan.php
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    die(json_encode(['error' => 'Unauthorized']));
}
include '../koneksi.php';

$id = $_GET['id'];
$query = "SELECT * FROM pengajuan WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if($data) {
    $data['tgl_pengajuan'] = date('d/m/Y', strtotime($data['tgl_pengajuan']));
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Data tidak ditemukan']);
}
?>