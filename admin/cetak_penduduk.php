<?php
// File: admin/cetak_penduduk.php - Cetak Data Penduduk (Tanpa Border Berlebih)
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    die("Silakan login terlebih dahulu");
}
include '../koneksi.php';

$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';
$nilai = isset($_GET['nilai']) ? trim($_GET['nilai']) : '';

// Jika tidak ada parameter jenis, cek parameter id
if(empty($jenis) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query_id = "SELECT * FROM penduduk WHERE id = '$id'";
    $result_id = mysqli_query($conn, $query_id);
    $data_id = mysqli_fetch_assoc($result_id);
    if($data_id) {
        $jenis = 'nik';
        $nilai = $data_id['nik'];
    } else {
        die("Data tidak ditemukan");
    }
}

if(empty($jenis) || empty($nilai)) {
    die("Parameter tidak lengkap. Gunakan jenis=nik&nilai=... atau jenis=kk&nilai=...");
}

if($jenis == 'nik') {
    $query = "SELECT * FROM penduduk WHERE nik = '$nilai'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    $title = "DATA PENDUDUK (NIK: $nilai)";
    
    if(!$data) {
        die("Data dengan NIK $nilai tidak ditemukan");
    }
} elseif($jenis == 'kk') {
    $query = "SELECT * FROM penduduk WHERE no_kk = '$nilai' ORDER BY 
              FIELD(status_hubungan, 'Kepala Keluarga', 'Istri', 'Anak'), 
              CASE WHEN status_hubungan = 'Anak' THEN anak_ke ELSE 0 END ASC";
    $result = mysqli_query($conn, $query);
    $data_list = [];
    while($row = mysqli_fetch_assoc($result)) {
        $data_list[] = $row;
    }
    
    if(count($data_list) == 0) {
        die("Data dengan No KK $nilai tidak ditemukan");
    }
    $title = "DATA KARTU KELUARGA (No KK: $nilai)";
} else {
    die("Jenis cetak tidak valid. Gunakan 'nik' atau 'kk'");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Data Penduduk - SILAMPID</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            padding: 20mm;
            margin: 0;
            background: white;
        }
        .kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop h1 {
            margin: 0;
            font-size: 24px;
        }
        .kop h2 {
            margin: 5px 0;
            font-size: 18px;
        }
        .kop p {
            margin: 0;
            font-size: 12px;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .info-table tr:last-child td {
            border-bottom: none;
        }
        .info-table td {
            padding: 8px;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 130px;
        }
        
        /* Tabel untuk KK */
        .kk-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .kk-table th, .kk-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .kk-table th {
            background: #2c3e50;
            color: white;
            text-align: center;
            font-weight: bold;
        }
        .kk-table td {
            text-align: center;
        }
        .header-kk {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        button {
            margin: 10px;
            padding: 10px 20px;
            background: #27ae60;
            color: white;
            border: none;
            cursor: pointer;
            font-family: Arial, sans-serif;
            border-radius: 5px;
        }
        button:hover {
            background: #219a52;
        }
        @media print {
            button {
                display: none;
            }
            body {
                padding: 0;
            }
            .info-table tr:last-child td {
                border-bottom: none;
            }
            .kk-table th {
                background: #2c3e50 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()"><i class="fas fa-print"></i> Cetak / Simpan PDF</button>
    <button onclick="window.close()">Tutup</button>
    
    <div class="kop">
        <h1>PEMERINTAH KABUPATEN PAKPAK BHARAT</h1>
        <h2>DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL</h2>
        <p>Alamat: Jl. Kantor Dukcapil, Kab. Pakpak Bharat</p>
        <p>Email: dukcapil@pakpakbharatkab.go.id | Telp: (0627) 123456</p>
    </div>
    
    <div class="title">
        <?php echo $title; ?>
    </div>
    
    <?php if($jenis == 'nik'): ?>
        <!-- CETAK PER NIK -->
        <table class="info-table">
            <tr><td>NIK</td><td>: <?php echo $data['nik']; ?></td></tr>
            <tr><td>No KK</td><td>: <?php echo $data['no_kk']; ?></td></tr>
            <tr><td>Nama Lengkap</td><td>: <?php echo htmlspecialchars($data['nama_lengkap']); ?></td></tr>
            <tr><td>Jenis Kelamin</td><td>: <?php echo $data['jenis_kelamin']; ?></td></tr>
            <tr><td>Status Hubungan</td><td>: <?php echo $data['status_hubungan'] ?: '-'; ?></td></tr>
            <?php if($data['anak_ke']): ?>
            <tr><td>Anak Ke-</td><td>: <?php echo $data['anak_ke']; ?></td></tr>
            <?php endif; ?>
            <tr><td>Tempat Lahir</td><td>: <?php echo $data['tempat_lahir']; ?></td></tr>
            <tr><td>Tanggal Lahir</td><td>: <?php echo date('d/m/Y', strtotime($data['tanggal_lahir'])); ?></td></tr>
            <tr><td>Alamat</td><td>: <?php echo nl2br(htmlspecialchars($data['alamat'])); ?></td></tr>
            <tr><td>RT / RW</td><td>: <?php echo $data['rt'] . ' / ' . $data['rw']; ?></td></tr>
            <tr><td>Desa/Kelurahan</td><td>: <?php echo $data['desa_kelurahan']; ?></td></tr>
            <tr><td>Kecamatan</td><td>: <?php echo $data['kecamatan']; ?></td></tr>
            <tr><td>Kabupaten/Kota</td><td>: <?php echo $data['kabupaten_kota']; ?></td></tr>
        </table>
        
    <?php else: ?>
        <!-- CETAK PER KK -->
        <?php 
        $kepala_keluarga = [];
        foreach($data_list as $row) {
            if($row['status_hubungan'] == 'Kepala Keluarga') {
                $kepala_keluarga = $row;
                break;
            }
        }
        ?>
        
        <?php if(!empty($kepala_keluarga)): ?>
        <div class="header-kk">
            <strong>KEPALA KELUARGA:</strong> <?php echo htmlspecialchars($kepala_keluarga['nama_lengkap']); ?><br>
            <strong>Alamat:</strong> <?php echo nl2br(htmlspecialchars($kepala_keluarga['alamat'])); ?> RT <?php echo $kepala_keluarga['rt']; ?>/RW <?php echo $kepala_keluarga['rw']; ?><br>
            <strong>Desa/Kelurahan:</strong> <?php echo $kepala_keluarga['desa_kelurahan']; ?><br>
            <strong>Kecamatan:</strong> <?php echo $kepala_keluarga['kecamatan']; ?>
        </div>
        <?php endif; ?>
        
        <table class="kk-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama Lengkap</th>
                    <th>Jenis Kelamin</th>
                    <th>Tempat Lahir</th>
                    <th>Tanggal Lahir</th>
                    <th>Status Hubungan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($data_list as $row): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['nik']; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                    <td><?php echo $row['jenis_kelamin']; ?></td>
                    <td><?php echo $row['tempat_lahir']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_lahir'])); ?></td>
                    <td><?php echo $row['status_hubungan']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 15px;">
            <strong>Jumlah Anggota Keluarga:</strong> <?php echo count($data_list); ?> orang
        </div>
    <?php endif; ?>
    
    <div class="footer">
        <p>Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?></p>
        <p>Dicetak oleh: <?php echo $_SESSION['nama_lengkap']; ?> (<?php echo $_SESSION['role']; ?>)</p>
    </div>
</body>
</html>