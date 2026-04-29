<?php
// File: admin/export_filtered.php
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    die("Unauthorized");
}
include '../koneksi.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$jenis_filter = isset($_GET['jenis']) ? mysqli_real_escape_string($conn, $_GET['jenis']) : '';
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

$where = [];
if(!empty($search)) $where[] = "(nik_warga LIKE '%$search%' OR nama_warga LIKE '%$search%' OR kode_unik LIKE '%$search%')";
if(!empty($status_filter)) $where[] = "status = '$status_filter'";
if(!empty($jenis_filter)) $where[] = "jenis_surat = '$jenis_filter'";
if(!empty($tanggal_awal) && !empty($tanggal_akhir)) $where[] = "tgl_pengajuan BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$query = "SELECT * FROM pengajuan $where_sql ORDER BY tgl_pengajuan DESC";
$result = mysqli_query($conn, $query);

$filename = "daftar_pengajuan_" . date('Ymd_His') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");

echo "LAPORAN DAFTAR PENGAJUAN\n";
echo "Dicetak: " . date('d/m/Y H:i:s') . "\n";
echo "User: " . $_SESSION['nama_lengkap'] . "\n";
if(!empty($search)) echo "Filter Pencarian: $search\n";
if(!empty($status_filter)) echo "Filter Status: $status_filter\n";
if(!empty($jenis_filter)) echo "Filter Jenis: $jenis_filter\n";
if(!empty($tanggal_awal) && !empty($tanggal_akhir)) echo "Filter Tanggal: $tanggal_awal s/d $tanggal_akhir\n";
echo "\nNo\tKODE UNIK\tTGL PENGAJUAN\tNIK\tNAMA\tJENIS SURAT\tSTATUS\tCATATAN ADMIN\n";

$no = 1;
while($row = mysqli_fetch_assoc($result)) {
    echo $no . "\t";
    echo $row['kode_unik'] . "\t";
    echo $row['tgl_pengajuan'] . "\t";
    echo $row['nik_warga'] . "\t";
    echo $row['nama_warga'] . "\t";
    echo $row['jenis_surat'] . "\t";
    echo $row['status'] . "\t";
    echo str_replace(["\n","\r"], " ", $row['catatan_admin']) . "\n";
    $no++;
}
echo "\nTotal Data: " . ($no - 1) . " baris";
?>