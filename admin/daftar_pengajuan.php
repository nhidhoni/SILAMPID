<?php
// File: admin/daftar_pengajuan.php - Daftar Pengajuan (Operator Bisa Edit, Hapus, Cetak)
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: index.php?error=2");
    exit();
}
include '../koneksi.php';

// Proses update data pengajuan (Edit)
if(isset($_POST['edit_pengajuan'])) {
    $id_pengajuan = $_POST['id_pengajuan'];
    $nik_warga = mysqli_real_escape_string($conn, $_POST['nik_warga']);
    $nama_warga = mysqli_real_escape_string($conn, $_POST['nama_warga']);
    $jenis_surat = mysqli_real_escape_string($conn, $_POST['jenis_surat']);
    $tgl_pengajuan = mysqli_real_escape_string($conn, $_POST['tgl_pengajuan']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $catatan_admin = mysqli_real_escape_string($conn, $_POST['catatan_admin']);
    
    $query_lama = "SELECT status FROM pengajuan WHERE id='$id_pengajuan'";
    $result_lama = mysqli_query($conn, $query_lama);
    $status_lama = mysqli_fetch_assoc($result_lama)['status'];
    
    $update = "UPDATE pengajuan SET 
                nik_warga='$nik_warga', 
                nama_warga='$nama_warga', 
                jenis_surat='$jenis_surat', 
                tgl_pengajuan='$tgl_pengajuan', 
                status='$status', 
                catatan_admin='$catatan_admin' 
                WHERE id='$id_pengajuan'";
    
    if(mysqli_query($conn, $update)) {
        if($status_lama != $status) {
            $log = "INSERT INTO log_status (id_pengajuan, status_lama, status_baru, diubah_oleh) 
                    VALUES ('$id_pengajuan', '$status_lama', '$status', '".$_SESSION['username']."')";
            mysqli_query($conn, $log);
        }
        echo "<script>alert('Data pengajuan berhasil diupdate!'); window.location.href='daftar_pengajuan.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data: " . mysqli_error($conn) . "');</script>";
    }
}

// Proses hapus data (operator juga bisa hapus)
if(isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    $delete = "DELETE FROM pengajuan WHERE id='$id_hapus'";
    if(mysqli_query($conn, $delete)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location.href='daftar_pengajuan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!');</script>";
    }
}

// Filter dan pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$jenis_filter = isset($_GET['jenis']) ? $_GET['jenis'] : '';
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

// Pagination
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build WHERE clause
$where = [];
if(!empty($search)) {
    $where[] = "(nik_warga LIKE '%$search%' OR nama_warga LIKE '%$search%' OR kode_unik LIKE '%$search%')";
}
if(!empty($status_filter)) {
    $where[] = "status = '$status_filter'";
}
if(!empty($jenis_filter)) {
    $where[] = "jenis_surat = '$jenis_filter'";
}
if(!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $where[] = "tgl_pengajuan BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}
$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Hitung total data
$count_query = "SELECT COUNT(*) as total FROM pengajuan $where_sql";
$count_result = mysqli_query($conn, $count_query);
$total_data = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data - Urut berdasarkan tanggal terbaru
$query = "SELECT * FROM pengajuan $where_sql ORDER BY tgl_pengajuan DESC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);

function getStatusBadge($status) {
    switch($status) {
        case 'Pending': return '<span class="badge badge-pending">⏳ Pending</span>';
        case 'Diproses': return '<span class="badge badge-diproses">🔄 Diproses</span>';
        case 'Selesai': return '<span class="badge badge-selesai">✅ Selesai</span>';
        case 'Ditolak': return '<span class="badge badge-ditolak">❌ Ditolak</span>';
        default: return '<span class="badge">'.$status.'</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengajuan - SILAMPID</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f7fa; 
            padding: 20px; 
        }
        .container { 
            max-width: 100%; 
            margin: 0 auto; 
            background: white; 
            padding: 25px; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
            overflow-x: auto;
        }
        h1 { 
            color: #2c3e50; 
            margin-bottom: 20px; 
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Action Bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn-group-left {
            display: flex;
            gap: 10px;
        }
        
        .btn { 
            padding: 8px 16px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 5px; 
            font-size: 14px;
        }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #219a52; }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn-secondary:hover { background: #7f8c8d; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-warning:hover { background: #e67e22; }
        
        /* Filter Section */
        .filter-section { 
            background: #f8f9fa; 
            padding: 15px 20px; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            display: flex; 
            gap: 12px; 
            flex-wrap: wrap; 
            align-items: flex-end; 
        }
        .filter-group { 
            display: flex; 
            flex-direction: column;
        }
        .filter-group label { 
            font-size: 12px; 
            font-weight: bold; 
            margin-bottom: 5px; 
            color: #555; 
        }
        .filter-group input, .filter-group select { 
            padding: 8px 12px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-size: 14px;
            width: 160px;
        }
        .filter-group .btn {
            height: 38px;
            margin-top: 0;
        }
        
        /* Info Bar */
        .info-bar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            margin-bottom: 15px; 
            flex-wrap: wrap; 
            gap: 15px;
        }
        .total-data { 
            background: #e8f4f8; 
            padding: 5px 12px; 
            border-radius: 20px; 
            font-size: 13px;
            font-weight: bold;
            color: #2980b9;
        }
        .limit-select {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .limit-select label {
            font-size: 13px;
            color: #555;
        }
        .limit-select select {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }
        
        /* Tabel */
        .table-container { overflow-x: auto; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 1200px; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background: #2c3e50; color: white; font-weight: 600; white-space: nowrap; }
        td { vertical-align: middle; word-break: break-word; }
        tr:hover { background: #f8f9fa; }
        
        /* Lebar kolom */
        th:nth-child(1), td:nth-child(1) { width: 50px; text-align: center; }
        th:nth-child(2), td:nth-child(2) { width: 100px; }
        th:nth-child(3), td:nth-child(3) { width: 100px; }
        th:nth-child(4), td:nth-child(4) { width: 140px; }
        th:nth-child(5), td:nth-child(5) { width: 140px; }
        th:nth-child(6), td:nth-child(6) { min-width: 150px; }
        th:nth-child(7), td:nth-child(7) { width: 120px; }
        th:nth-child(8), td:nth-child(8) { width: 100px; }
        th:nth-child(9), td:nth-child(9) { width: 200px; }
        th:nth-child(10), td:nth-child(10) { width: 140px; }
        
        /* Badge */
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; white-space: nowrap; }
        .badge-pending { background: #fef3c7; color: #d97706; }
        .badge-diproses { background: #dbeafe; color: #2563eb; }
        .badge-selesai { background: #d1fae5; color: #059669; }
        .badge-ditolak { background: #fee2e2; color: #dc2626; }
        
        /* Catatan admin teks */
        .catatan-teks {
            max-width: 200px;
            word-wrap: break-word;
            white-space: normal;
            color: #555;
            font-style: italic;
        }
        .catatan-kosong {
            color: #ccc;
            font-style: italic;
        }
        
        /* Action Buttons */
        .action-btn { 
            padding: 5px 10px; 
            border-radius: 4px; 
            text-decoration: none; 
            font-size: 12px; 
            display: inline-flex; 
            align-items: center; 
            gap: 3px; 
            margin: 2px;
        }
        .action-btn.edit { background: #f39c12; color: white; }
        .action-btn.edit:hover { background: #e67e22; }
        .action-btn.print { background: #27ae60; color: white; }
        .action-btn.print:hover { background: #219a52; }
        .action-btn.delete { background: #e74c3c; color: white; }
        .action-btn.delete:hover { background: #c0392b; }
        
        /* Modal Edit */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ecf0f1;
        }
        .close {
            cursor: pointer;
            font-size: 24px;
            color: #7f8c8d;
        }
        .modal-form-group {
            margin-bottom: 15px;
        }
        .modal-form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 13px;
        }
        .modal-form-group input, .modal-form-group select, .modal-form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        
        /* Pagination */
        .pagination { 
            display: flex; 
            justify-content: center; 
            gap: 5px; 
            margin-top: 25px; 
            flex-wrap: wrap; 
        }
        .pagination a, .pagination span { 
            padding: 8px 12px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            text-decoration: none; 
            color: #333; 
            font-size: 13px;
        }
        .pagination a:hover { background: #3498db; color: white; border-color: #3498db; }
        .pagination .active { background: #3498db; color: white; border-color: #3498db; }
        
        @media (max-width: 768px) { 
            .filter-section { flex-direction: column; align-items: stretch; }
            .filter-group input, .filter-group select { width: 100%; }
            .info-bar { flex-direction: column; align-items: flex-start; }
            .action-bar { flex-direction: column; align-items: stretch; }
            .btn-group-left { justify-content: space-between; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-list-alt"></i> Daftar Pengajuan</h1>
        
        <!-- Action Bar -->
        <div class="action-bar">
            <div class="btn-group-left">
                <a href="export_filtered.php?<?php echo $_SERVER['QUERY_STRING']; ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
            </div>
            <a href="dashboard_admin.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-group">
                <label><i class="fas fa-search"></i> Cari</label>
                <input type="text" id="searchInput" placeholder="NIK / Nama / Kode" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-group">
                <label><i class="fas fa-filter"></i> Status</label>
                <select id="statusFilter">
                    <option value="">Semua</option>
                    <option value="Pending" <?php echo $status_filter=='Pending'?'selected':''; ?>>Pending</option>
                    <option value="Diproses" <?php echo $status_filter=='Diproses'?'selected':''; ?>>Diproses</option>
                    <option value="Selesai" <?php echo $status_filter=='Selesai'?'selected':''; ?>>Selesai</option>
                    <option value="Ditolak" <?php echo $status_filter=='Ditolak'?'selected':''; ?>>Ditolak</option>
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-file-alt"></i> Jenis Surat</label>
                <select id="jenisFilter">
                    <option value="">Semua</option>
                    <option value="Lahir" <?php echo $jenis_filter=='Lahir'?'selected':''; ?>>Lahir</option>
                    <option value="Kematian" <?php echo $jenis_filter=='Kematian'?'selected':''; ?>>Kematian</option>
                    <option value="Pindah Datang" <?php echo $jenis_filter=='Pindah Datang'?'selected':''; ?>>Pindah Datang</option>
                    <option value="Pindah Keluar" <?php echo $jenis_filter=='Pindah Keluar'?'selected':''; ?>>Pindah Keluar</option>
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> Tgl Awal</label>
                <input type="date" id="tglAwal" value="<?php echo $tanggal_awal; ?>">
            </div>
            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> Tgl Akhir</label>
                <input type="date" id="tglAkhir" value="<?php echo $tanggal_akhir; ?>">
            </div>
            <div class="filter-group">
                <button class="btn btn-primary" onclick="applyFilter()"><i class="fas fa-search"></i> Filter</button>
            </div>
            <div class="filter-group">
                <a href="daftar_pengajuan.php" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
            </div>
        </div>
        
        <!-- Info Bar dengan Pilihan Limit -->
        <div class="info-bar">
            <div class="total-data"><i class="fas fa-database"></i> Total: <?php echo $total_data; ?> data</div>
            <div class="limit-select">
                <label><i class="fas fa-eye"></i> Tampilkan:</label>
                <select id="limitSelect" onchange="changeLimit()">
                    <option value="5" <?php echo $limit == 5 ? 'selected' : ''; ?>>5</option>
                    <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="15" <?php echo $limit == 15 ? 'selected' : ''; ?>>15</option>
                    <option value="20" <?php echo $limit == 20 ? 'selected' : ''; ?>>20</option>
                </select>
                <span>data per halaman</span>
            </div>
        </div>
        
        <div class="table-container">
            <?php if(mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Unik</th>
                        <th>Tgl Pengajuan</th>
                        <th>NIK</th>
                        <th>No KK</th>
                        <th>Nama</th>
                        <th>Jenis Surat</th>
                        <th>Status</th>
                        <th>Catatan Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = $offset + 1; 
                    while($row = mysqli_fetch_assoc($result)): 
                        // Ambil no_kk dari tabel penduduk berdasarkan nik_warga
                        $no_kk = '';
                        $query_kk = "SELECT no_kk FROM penduduk WHERE nik = '{$row['nik_warga']}'";
                        $result_kk = mysqli_query($conn, $query_kk);
                        if(mysqli_num_rows($result_kk) > 0) {
                            $data_kk = mysqli_fetch_assoc($result_kk);
                            $no_kk = $data_kk['no_kk'];
                        }
                    ?>
                    <tr id="row-<?php echo $row['id']; ?>">
                        <td style="text-align: center;"><?php echo $no++; ?></td>
                        <td><strong><?php echo $row['kode_unik']; ?></strong></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tgl_pengajuan'])); ?></td>
                        <td><?php echo $row['nik_warga']; ?></td>
                        <td><?php echo $no_kk; ?></td>
                        <td><?php echo htmlspecialchars($row['nama_warga']); ?></td>
                        <td><?php echo $row['jenis_surat']; ?></td>
                        <td><?php echo getStatusBadge($row['status']); ?></td>
                        <td class="catatan-teks">
                            <?php 
                            if(!empty($row['catatan_admin'])) {
                                echo nl2br(htmlspecialchars($row['catatan_admin']));
                            } else {
                                echo '<span class="catatan-kosong">-</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <button class="action-btn edit" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['kode_unik']); ?>', '<?php echo addslashes($row['nik_warga']); ?>', '<?php echo addslashes($row['nama_warga']); ?>', '<?php echo addslashes($row['jenis_surat']); ?>', '<?php echo $row['tgl_pengajuan']; ?>', '<?php echo addslashes($row['status']); ?>', '<?php echo addslashes($row['catatan_admin']); ?>')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="action-btn print" onclick="cetakPDF(<?php echo $row['id']; ?>, '<?php echo $row['jenis_surat']; ?>')">
                                <i class="fas fa-print"></i> Cetak PDF
                            </button>
                            <a href="javascript:void(0)" onclick="if(confirm('Yakin hapus data pengajuan ini?')) window.location.href='daftar_pengajuan.php?hapus=<?php echo $row['id']; ?>'" class="action-btn delete"><i class="fas fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div style="text-align:center; padding:40px;">
                <i class="fas fa-inbox" style="font-size:48px; color:#ccc;"></i>
                <p>Tidak ada data pengajuan.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?><a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>&jenis=<?php echo $jenis_filter; ?>&tanggal_awal=<?php echo $tanggal_awal; ?>&tanggal_akhir=<?php echo $tanggal_akhir; ?>&limit=<?php echo $limit; ?>"><i class="fas fa-chevron-left"></i> Prev</a><?php endif; ?>
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <?php if($i == $page): ?><span class="active"><?php echo $i; ?></span><?php else: ?><a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>&jenis=<?php echo $jenis_filter; ?>&tanggal_awal=<?php echo $tanggal_awal; ?>&tanggal_akhir=<?php echo $tanggal_akhir; ?>&limit=<?php echo $limit; ?>"><?php echo $i; ?></a><?php endif; ?>
            <?php endfor; ?>
            <?php if($page < $total_pages): ?><a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>&jenis=<?php echo $jenis_filter; ?>&tanggal_awal=<?php echo $tanggal_awal; ?>&tanggal_akhir=<?php echo $tanggal_akhir; ?>&limit=<?php echo $limit; ?>">Next <i class="fas fa-chevron-right"></i></a><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Modal Edit Pengajuan -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Edit Data Pengajuan</h3>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form method="POST" id="editForm">
                <input type="hidden" name="id_pengajuan" id="edit_id">
                <input type="hidden" name="edit_pengajuan" value="1">
                <div class="modal-form-group">
                    <label>Kode Unik</label>
                    <input type="text" id="edit_kode_unik" readonly style="background:#e9ecef;">
                </div>
                <div class="modal-form-group">
                    <label>NIK *</label>
                    <input type="text" id="edit_nik" name="nik_warga" required>
                </div>
                <div class="modal-form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" id="edit_nama" name="nama_warga" required>
                </div>
                <div class="modal-form-group">
                    <label>Jenis Surat *</label>
                    <select id="edit_jenis_surat" name="jenis_surat" required>
                        <option value="Lahir">Lahir</option>
                        <option value="Kematian">Kematian</option>
                        <option value="Pindah Datang">Pindah Datang</option>
                        <option value="Pindah Keluar">Pindah Keluar</option>
                    </select>
                </div>
                <div class="modal-form-group">
                    <label>Tanggal Pengajuan *</label>
                    <input type="date" id="edit_tgl_pengajuan" name="tgl_pengajuan" required>
                </div>
                <div class="modal-form-group">
                    <label>Status *</label>
                    <select id="edit_status" name="status" required>
                        <option value="Pending">Pending</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="modal-form-group">
                    <label>Catatan Admin</label>
                    <textarea id="edit_catatan" name="catatan_admin" rows="3" placeholder="Isi catatan admin jika diperlukan"></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Open Edit Modal
        function openEditModal(id, kode_unik, nik, nama, jenis_surat, tgl_pengajuan, status, catatan) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_kode_unik').value = kode_unik;
            document.getElementById('edit_nik').value = nik;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_jenis_surat').value = jenis_surat;
            document.getElementById('edit_tgl_pengajuan').value = tgl_pengajuan;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_catatan').value = catatan;
            document.getElementById('editModal').style.display = 'flex';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Cetak PDF
        function cetakPDF(id, jenis) {
            window.open('cetak_surat.php?id=' + id + '&jenis=' + jenis, '_blank');
        }
        
        function applyFilter() {
            var search = document.getElementById('searchInput').value;
            var status = document.getElementById('statusFilter').value;
            var jenis = document.getElementById('jenisFilter').value;
            var tglAwal = document.getElementById('tglAwal').value;
            var tglAkhir = document.getElementById('tglAkhir').value;
            var limit = document.getElementById('limitSelect').value;
            window.location.href = 'daftar_pengajuan.php?search=' + encodeURIComponent(search) + '&status=' + encodeURIComponent(status) + '&jenis=' + encodeURIComponent(jenis) + '&tanggal_awal=' + encodeURIComponent(tglAwal) + '&tanggal_akhir=' + encodeURIComponent(tglAkhir) + '&limit=' + limit;
        }
        
        function changeLimit() {
            applyFilter();
        }
        
        document.getElementById('searchInput')?.addEventListener('keypress', function(e) { 
            if(e.key === 'Enter') applyFilter(); 
        });
        
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>