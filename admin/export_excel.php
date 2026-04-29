<?php
// File: admin/export_excel.php - Export Excel (Final - Tanpa Kawin)
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    die("Unauthorized");
}
include '../koneksi.php';

$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];
$jenis_filter = isset($_GET['jenis']) ? $_GET['jenis'] : 'semua';

$nama_bulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];

if($jenis_filter == 'semua') {
    $lahir = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan WHERE jenis_surat='Lahir' AND MONTH(tgl_pengajuan)='$bulan' AND YEAR(tgl_pengajuan)='$tahun'"))['total'];
    $kematian = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan WHERE jenis_surat='Kematian' AND MONTH(tgl_pengajuan)='$bulan' AND YEAR(tgl_pengajuan)='$tahun'"))['total'];
    $pindah_datang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan WHERE jenis_surat='Pindah Datang' AND MONTH(tgl_pengajuan)='$bulan' AND YEAR(tgl_pengajuan)='$tahun'"))['total'];
    $pindah_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan WHERE jenis_surat='Pindah Keluar' AND MONTH(tgl_pengajuan)='$bulan' AND YEAR(tgl_pengajuan)='$tahun'"))['total'];
    $total = $lahir + $kematian + $pindah_datang + $pindah_keluar;
    
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Bulanan_".$nama_bulan[$bulan]."_".$tahun.".xls");
    
    echo "LAPORAN BULANAN REKAPITULASI\n";
    echo "Dinas Kependudukan dan Pencatatan Sipil\n";
    echo "Bulan: ".$nama_bulan[$bulan]." ".$tahun."\n\n";
    echo "No\tJenis Layanan\tJumlah\n";
    echo "1\tKelahiran\t$lahir\n";
    echo "2\tKematian\t$kematian\n";
    echo "3\tPindah Datang\t$pindah_datang\n";
    echo "4\tPindah Keluar\t$pindah_keluar\n";
    echo "Total\tSemua Layanan\t$total\n";
    echo "\nDicetak pada: ".date('d/m/Y H:i:s');
} else {
    $jenis_nama = '';
    if($jenis_filter == 'Lahir') $jenis_nama = 'Kelahiran';
    elseif($jenis_filter == 'Kematian') $jenis_nama = 'Kematian';
    elseif($jenis_filter == 'Pindah Datang') $jenis_nama = 'Pindah Datang';
    elseif($jenis_filter == 'Pindah Keluar') $jenis_nama = 'Pindah Keluar';
    
    $query = "SELECT COUNT(*) as total FROM pengajuan WHERE jenis_surat='$jenis_filter' AND MONTH(tgl_pengajuan)='$bulan' AND YEAR(tgl_pengajuan)='$tahun'";
    $total = mysqli_fetch_assoc(mysqli_query($conn, $query))['total'];
    
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_".$jenis_filter."_".$nama_bulan[$bulan]."_".$tahun.".xls");
    
    echo "LAPORAN BULANAN - $jenis_nama\n";
    echo "Dinas Kependudukan dan Pencatatan Sipil\n";
    echo "Bulan: ".$nama_bulan[$bulan]." ".$tahun."\n\n";
    echo "Jenis Layanan: $jenis_nama\n";
    echo "Jumlah: $total\n";
    echo "\nDicetak pada: ".date('d/m/Y H:i:s');
}
?>