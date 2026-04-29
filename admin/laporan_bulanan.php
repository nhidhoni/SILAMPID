<?php
// File: admin/laporan_bulanan.php - Halaman Laporan & Arsip
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: index.php?error=2");
    exit();
}
include '../koneksi.php';

// Ambil parameter filter
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$jenis_surat = isset($_GET['jenis_surat']) ? mysqli_real_escape_string($conn, $_GET['jenis_surat']) : '';
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'laporan'; // laporan atau arsip

// Untuk filter status di laporan
$status_laporan = isset($_GET['status_laporan']) ? mysqli_real_escape_string($conn, $_GET['status_laporan']) : '';
$status_arsip = isset($_GET['status_arsip']) ? mysqli_real_escape_string($conn, $_GET['status_arsip']) : '';

// Ambil daftar tahun yang tersedia di database (semua tahun)
$query_all_tahun = "SELECT DISTINCT YEAR(tgl_pengajuan) as tahun FROM pengajuan ORDER BY tahun DESC";
$result_all_tahun = mysqli_query($conn, $query_all_tahun);

$daftar_tahun = [];
if($result_all_tahun && mysqli_num_rows($result_all_tahun) > 0) {
    while($row = mysqli_fetch_assoc($result_all_tahun)) {
        $daftar_tahun[] = $row['tahun'];
    }
}

// Tambahkan tahun berjalan jika belum ada
$current_year = date('Y');
if(!in_array($current_year, $daftar_tahun)) {
    $daftar_tahun[] = $current_year;
}

// Urutkan dari terbaru ke terlama
rsort($daftar_tahun);

// Ambil daftar jenis surat untuk filter
$query_jenis = "SELECT DISTINCT jenis_surat FROM pengajuan ORDER BY jenis_surat";
$result_jenis = mysqli_query($conn, $query_jenis);

// =====================================================
// LAPORAN BULANAN (SEMUA STATUS)
// =====================================================
$sql_laporan = "SELECT p.*, 
                DATE_FORMAT(p.tgl_pengajuan, '%d/%m/%Y') as tgl_pengajuan,
                DATE_FORMAT(p.tgl_selesai, '%d/%m/%Y') as tgl_selesai,
                (SELECT no_kk FROM penduduk WHERE nik = p.nik_warga LIMIT 1) as no_kk
                FROM pengajuan p 
                WHERE 1=1";

if(!empty($bulan)) {
    $sql_laporan .= " AND MONTH(p.tgl_pengajuan) = '$bulan'";
}
if(!empty($tahun)) {
    $sql_laporan .= " AND YEAR(p.tgl_pengajuan) = '$tahun'";
}
if(!empty($jenis_surat)) {
    $sql_laporan .= " AND p.jenis_surat = '$jenis_surat'";
}
if(!empty($status_laporan)) {
    $sql_laporan .= " AND p.status = '$status_laporan'";
}

$sql_laporan .= " ORDER BY p.tgl_pengajuan DESC";

// =====================================================
// ARSIP (Selesai & Ditolak)
// =====================================================
$sql_arsip = "SELECT p.*, 
              DATE_FORMAT(p.tgl_pengajuan, '%d/%m/%Y') as tgl_pengajuan,
              DATE_FORMAT(p.tgl_selesai, '%d/%m/%Y') as tgl_selesai,
              (SELECT no_kk FROM penduduk WHERE nik = p.nik_warga LIMIT 1) as no_kk
              FROM pengajuan p 
              WHERE p.status IN ('Selesai', 'Ditolak')";

if(!empty($bulan)) {
    $sql_arsip .= " AND MONTH(p.tgl_pengajuan) = '$bulan'";
}
if(!empty($tahun)) {
    $sql_arsip .= " AND YEAR(p.tgl_pengajuan) = '$tahun'";
}
if(!empty($jenis_surat)) {
    $sql_arsip .= " AND p.jenis_surat = '$jenis_surat'";
}
if(!empty($status_arsip)) {
    $sql_arsip .= " AND p.status = '$status_arsip'";
}

$sql_arsip .= " ORDER BY p.tgl_pengajuan DESC";

// Eksekusi query
$result_laporan = mysqli_query($conn, $sql_laporan);
if (!$result_laporan) {
    $error_laporan = "Error SQL: " . mysqli_error($conn);
    $result_laporan = null;
}

$result_arsip = mysqli_query($conn, $sql_arsip);
if (!$result_arsip) {
    $error_arsip = "Error SQL: " . mysqli_error($conn);
    $result_arsip = null;
}

// Nama bulan
$nama_bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan & Arsip - SILAMPID</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f7fa; 
        }
        .navbar {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .logo { display: flex; align-items: center; gap: 10px; font-size: 20px; font-weight: bold; }
        .logo i { font-size: 28px; color: #3498db; }
        .nav-menu { display: flex; gap: 20px; flex-wrap: wrap; align-items: center; }
        .nav-menu a { 
            color: white; 
            text-decoration: none; 
            padding: 8px 15px; 
            border-radius: 5px; 
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-menu a:hover, .nav-menu a.active { background: #3498db; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .logout-btn { background: #e74c3c; padding: 6px 12px; border-radius: 5px; }
        .logout-btn:hover { background: #c0392b; }
        
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        
        .page-header {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .page-header h1 { font-size: 24px; margin-bottom: 8px; }
        .page-header p { opacity: 0.9; font-size: 14px; }
        
        .tabs {
            display: flex;
            gap: 5px;
            background: white;
            border-radius: 12px;
            padding: 5px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .tab-btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            background: transparent;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .tab-btn i { font-size: 18px; }
        .tab-btn.active {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }
        .tab-btn:not(.active):hover {
            background: #ecf0f1;
        }
        
        .filter-panel {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        .filter-group select, .filter-group input {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            min-width: 150px;
            background: white;
        }
        .btn-filter {
            background: #27ae60;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-filter:hover { background: #229954; }
        .btn-print {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }
        .btn-print:hover { background: #2980b9; }
        .btn-reset {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-reset:hover { background: #7f8c8d; }
        
        .table-container {
            background: white;
            border-radius: 12px;
            overflow-x: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
            font-size: 13px;
            text-transform: uppercase;
        }
        tr:hover { background: #f8f9fa; }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-pending { background: #fef3c7; color: #d97706; }
        .badge-diproses { background: #dbeafe; color: #2563eb; }
        .badge-selesai { background: #d1fae5; color: #059669; }
        .badge-ditolak { background: #fee2e2; color: #dc2626; }
        
        .btn-print-row {
            background: #3498db;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-print-row:hover { background: #2980b9; }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
        
        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .info-stats {
            background: #e8f4f8;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 13px;
        }
        .info-stats span {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            background: white;
            margin-top: 30px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .filter-form { flex-direction: column; align-items: stretch; }
            .btn-print, .btn-reset { margin-left: 0; margin-top: 10px; }
            .tabs { flex-direction: column; }
        }
        
        @media print {
            .navbar, .tabs, .filter-panel, .footer, .btn-print-row, .btn-print, .btn-filter, .btn-reset {
                display: none;
            }
            .table-container {
                box-shadow: none;
            }
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .container {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-id-card"></i><span>SILAMPID</span></div>
        <div class="nav-menu">
            <a href="dashboard_admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="laporan_bulanan.php" class="active"><i class="fas fa-chart-line"></i> Laporan</a>
            <div class="user-info">
                <span><i class="fas fa-user-circle"></i> <?php echo $_SESSION['nama_lengkap']; ?> (<?php echo $_SESSION['role']; ?>)</span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-chart-line"></i> Laporan & Arsip</h1>
            <p>Kelola laporan bulanan dan arsip </p>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn <?php echo $tab == 'laporan' ? 'active' : ''; ?>" onclick="setTab('laporan')">
                <i class="fas fa-calendar-alt"></i> Laporan Bulanan
            </button>
            <button class="tab-btn <?php echo $tab == 'arsip' ? 'active' : ''; ?>" onclick="setTab('arsip')">
                <i class="fas fa-archive"></i> Arsip
            </button>
        </div>

        <!-- Filter Panel -->
        <div class="filter-panel">
            <form method="GET" action="" id="filterForm" class="filter-form">
                <input type="hidden" name="tab" id="tabInput" value="<?php echo $tab; ?>">
                
                <div class="filter-group">
                    <label><i class="fas fa-calendar"></i> Bulan</label>
                    <select name="bulan">
                        <option value="">Semua Bulan</option>
                        <?php for($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $bulan == $i ? 'selected' : ''; ?>>
                                <?php echo $nama_bulan[$i]; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-calendar-year"></i> Tahun</label>
                    <select name="tahun">
                        <option value="">Semua Tahun</option>
                        <option value="2023" <?php echo $tahun == '2023' ? 'selected' : ''; ?>>2023</option>
                        <option value="2024" <?php echo $tahun == '2024' ? 'selected' : ''; ?>>2024</option>
                        <option value="2025" <?php echo $tahun == '2025' ? 'selected' : ''; ?>>2025</option>
                        <option value="2026" <?php echo $tahun == '2026' ? 'selected' : ''; ?>>2026</option>
                        <option value="2027" <?php echo $tahun == '2027' ? 'selected' : ''; ?>>2027</option>
                        <option value="2028" <?php echo $tahun == '2028' ? 'selected' : ''; ?>>2028</option>
                        <option value="2029" <?php echo $tahun == '2029' ? 'selected' : ''; ?>>2029</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-file-alt"></i> Jenis Layanan</label>
                    <select name="jenis_surat">
                        <option value="">Semua Jenis</option>
                        <?php if($result_jenis && mysqli_num_rows($result_jenis) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result_jenis)): ?>
                                <option value="<?php echo $row['jenis_surat']; ?>" <?php echo $jenis_surat == $row['jenis_surat'] ? 'selected' : ''; ?>>
                                    <?php echo $row['jenis_surat']; ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <!-- Filter Status untuk Laporan Bulanan -->
                <?php if($tab == 'laporan'): ?>
                <div class="filter-group">
                    <label><i class="fas fa-tag"></i> Status</label>
                    <select name="status_laporan">
                        <option value="">Semua Status</option>
                        <option value="Pending" <?php echo $status_laporan == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Diproses" <?php echo $status_laporan == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
                        <option value="Selesai" <?php echo $status_laporan == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="Ditolak" <?php echo $status_laporan == 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                </div>
                <?php endif; ?>
                
                <!-- Filter Status untuk Arsip -->
                <?php if($tab == 'arsip'): ?>
                <div class="filter-group">
                    <label><i class="fas fa-tag"></i> Status</label>
                    <select name="status_arsip">
                        <option value="">Semua Status</option>
                        <option value="Selesai" <?php echo $status_arsip == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="Ditolak" <?php echo $status_arsip == 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                </div>
                <?php endif; ?>
                
                <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filter</button>
                <button type="button" class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Cetak Laporan</button>
                <!-- <a href="laporan_bulanan.php?tab=<?php echo $tab; ?>" class="btn-reset"><i class="fas fa-undo"></i> Reset</a> -->
            </form>
        </div>

        <!-- Tampilkan error jika ada -->
        <?php if(isset($error_laporan) && $tab == 'laporan'): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error_laporan; ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error_arsip) && $tab == 'arsip'): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error_arsip; ?>
            </div>
        <?php endif; ?>

        <!-- Tabel Laporan Bulanan (SEMUA STATUS) -->
        <div id="tabLaporan" class="table-container" style="<?php echo $tab != 'laporan' ? 'display:none;' : ''; ?>">
            <?php if($result_laporan && mysqli_num_rows($result_laporan) > 0): ?>
            <div class="info-stats">
                <span><i class="fas fa-chart-bar"></i> Total Data: <?php echo mysqli_num_rows($result_laporan); ?> pengajuan</span>
                <span><i class="fas fa-calendar"></i> Periode: <?php echo !empty($bulan) ? $nama_bulan[$bulan] : 'Semua Bulan'; ?> <?php echo !empty($tahun) ? $tahun : 'Semua Tahun'; ?></span>
            </div>
            <?php endif; ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Tgl Pengajuan</th>
                        <th>NIK</th>
                        <th>No KK</th>
                        <th>Nama</th>
                        <th>Jenis Surat</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result_laporan && mysqli_num_rows($result_laporan) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result_laporan)): ?>
                        <tr>
                            <td><?php echo $row['tgl_pengajuan']; ?></td>
                            <td><?php echo $row['nik_warga']; ?></td>
                            <td><?php echo $row['no_kk'] ? $row['no_kk'] : '-'; ?></td>
                            <td><?php echo $row['nama_warga']; ?></td>
                            <td><?php echo $row['jenis_surat']; ?></td>
                            <td>
                                <?php if($row['status'] == 'Pending'): ?>
                                    <span class="badge badge-pending">⏳ Pending</span>
                                <?php elseif($row['status'] == 'Diproses'): ?>
                                    <span class="badge badge-diproses">🔄 Diproses</span>
                                <?php elseif($row['status'] == 'Selesai'): ?>
                                    <span class="badge badge-selesai">✅ Selesai</span>
                                <?php elseif($row['status'] == 'Ditolak'): ?>
                                    <span class="badge badge-ditolak">❌ Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn-print-row" onclick="cetakSurat(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-print"></i> Cetak
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-data">
                                <i class="fas fa-folder-open"></i> Tidak ada data laporan untuk periode ini
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tabel Arsip (KHUSUS Selesai & Ditolak) -->
        <div id="tabArsip" class="table-container" style="<?php echo $tab != 'arsip' ? 'display:none;' : ''; ?>">
            <?php if($result_arsip && mysqli_num_rows($result_arsip) > 0): ?>
            <div class="info-stats">
                <span><i class="fas fa-chart-bar"></i> Total Arsip: <?php echo mysqli_num_rows($result_arsip); ?> pengajuan</span>
                <span><i class="fas fa-calendar"></i> Periode: <?php echo !empty($bulan) ? $nama_bulan[$bulan] : 'Semua Bulan'; ?> <?php echo !empty($tahun) ? $tahun : 'Semua Tahun'; ?></span>
            </div>
            <?php endif; ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Tgl Pengajuan</th>
                        <th>NIK</th>
                        <th>No KK</th>
                        <th>Nama</th>
                        <th>Jenis Surat</th>
                        <th>Status</th>
                        <th>Tgl Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result_arsip && mysqli_num_rows($result_arsip) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result_arsip)): ?>
                        <tr>
                            <td><?php echo $row['tgl_pengajuan']; ?></td>
                            <td><?php echo $row['nik_warga']; ?></td>
                            <td><?php echo $row['no_kk'] ? $row['no_kk'] : '-'; ?></td>
                            <td><?php echo $row['nama_warga']; ?></td>
                            <td><?php echo $row['jenis_surat']; ?></td>
                            <td>
                                <?php if($row['status'] == 'Selesai'): ?>
                                    <span class="badge badge-selesai">✅ Selesai</span>
                                <?php elseif($row['status'] == 'Ditolak'): ?>
                                    <span class="badge badge-ditolak">❌ Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['updated_at'] ?? '-'; ?></td>
                            <td>
                                <button class="btn-print-row" onclick="cetakSurat(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-print"></i> Cetak
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-data">
                                <i class="fas fa-folder-open"></i> Tidak ada data arsip untuk periode ini
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 SILAMPID - Dinas Dukcapil Kabupaten Pakpak Bharat</p>
    </div>

    <script>
        function setTab(tab) {
            document.getElementById('tabInput').value = tab;
            document.getElementById('filterForm').submit();
        }
        
        function cetakSurat(id) {
            window.open('cetak_surat.php?id=' + id, '_blank');
        }
    </script>
</body>
</html>