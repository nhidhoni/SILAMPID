<?php
// File: admin/data_penduduk.php - Data Penduduk (Final - Operator Bisa Edit, Hapus, Cetak)
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: index.php?error=2");
    exit();
}
include '../koneksi.php';

// Proses hapus data (operator juga bisa hapus)
if(isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    $cek_pengajuan = "SELECT COUNT(*) as total FROM pengajuan WHERE id_penduduk='$id_hapus'";
    $result_cek = mysqli_query($conn, $cek_pengajuan);
    $ada_pengajuan = mysqli_fetch_assoc($result_cek)['total'];
    if($ada_pengajuan > 0) {
        $error_hapus = "Tidak dapat menghapus data penduduk karena masih memiliki $ada_pengajuan pengajuan surat.";
    } else {
        $delete = "DELETE FROM penduduk WHERE id='$id_hapus'";
        if(mysqli_query($conn, $delete)) {
            $success_hapus = "Data penduduk berhasil dihapus!";
        } else {
            $error_hapus = "Gagal menghapus data: " . mysqli_error($conn);
        }
    }
}

// Filter dan pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$jenis_kelamin_filter = isset($_GET['jk']) ? $_GET['jk'] : '';

// Pagination
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build WHERE clause
$where = [];
if(!empty($search)) {
    $where[] = "(nik LIKE '%$search%' OR nama_lengkap LIKE '%$search%' OR no_kk LIKE '%$search%' OR alamat LIKE '%$search%')";
}
if(!empty($jenis_kelamin_filter)) {
    $where[] = "jenis_kelamin = '$jenis_kelamin_filter'";
}
$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Hitung total data
$count_query = "SELECT COUNT(*) as total FROM penduduk $where_sql";
$count_result = mysqli_query($conn, $count_query);
$total_data = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data - Urut berdasarkan nama abjad
$query = "SELECT * FROM penduduk $where_sql ORDER BY nama_lengkap ASC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penduduk - SILAMPID</title>
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
        }
        h1 { 
            color: #2c3e50; 
            margin-bottom: 20px; 
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn-group-left { display: flex; gap: 10px; }
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
        
        .filter-section { 
            background: #f8f9fa; 
            padding: 12px 20px; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            display: flex; 
            gap: 12px; 
            flex-wrap: wrap; 
            align-items: flex-end; 
        }
        .filter-group { display: flex; flex-direction: column; }
        .filter-group label { font-size: 12px; font-weight: bold; margin-bottom: 5px; color: #555; }
        .filter-group input, .filter-group select { 
            padding: 8px 12px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-size: 14px;
            width: 200px;
        }
        .filter-group .btn { height: 38px; margin-top: 0; }
        
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
        .limit-select label { font-size: 13px; color: #555; }
        .limit-select select {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }
        
        .table-container { 
            overflow-x: auto; 
            margin: 15px 0; 
            border: 1px solid #ecf0f1;
            border-radius: 8px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 13px; 
        }
        th, td { 
            padding: 10px 8px; 
            text-align: left; 
            border-bottom: 1px solid #ecf0f1; 
        }
        th { 
            background: #2c3e50; 
            color: white; 
            font-weight: 600; 
            white-space: nowrap;
        }
        td { 
            vertical-align: top; 
            word-break: break-word;
        }
        tr:hover { background: #f8f9fa; }
        
        th:nth-child(1), td:nth-child(1) { width: 45px; text-align: center; }
        th:nth-child(2), td:nth-child(2) { width: 140px; }
        th:nth-child(3), td:nth-child(3) { width: 140px; }
        th:nth-child(4), td:nth-child(4) { min-width: 150px; }
        th:nth-child(5), td:nth-child(5) { width: 80px; }
        th:nth-child(6), td:nth-child(6) { width: 110px; }
        th:nth-child(7), td:nth-child(7) { min-width: 200px; }
        th:nth-child(8), td:nth-child(8) { width: 45px; }
        th:nth-child(9), td:nth-child(9) { width: 45px; }
        th:nth-child(10), td:nth-child(10) { min-width: 110px; }
        th:nth-child(11), td:nth-child(11) { min-width: 110px; }
        th:nth-child(12), td:nth-child(12) { width: 120px; }
        
        td:nth-child(2), td:nth-child(3) { white-space: nowrap; }
        td:nth-child(7) { white-space: normal; line-height: 1.4; }
        
        .badge { 
            padding: 4px 10px; 
            border-radius: 20px; 
            font-size: 11px; 
            font-weight: 600; 
            display: inline-block; 
            white-space: nowrap; 
        }
        .badge-laki { background: #dbeafe; color: #2563eb; }
        .badge-perempuan { background: #fce7f3; color: #db2777; }
        .badge-status { background: #e8f8f0; color: #27ae60; }
        
        .action-btn { 
            padding: 4px 8px; 
            border-radius: 4px; 
            text-decoration: none; 
            font-size: 11px; 
            display: inline-flex; 
            align-items: center; 
            gap: 3px; 
            margin: 2px;
        }
        .action-btn.edit { background: #f39c12; color: white; }
        .action-btn.edit:hover { background: #e67e22; }
        .action-btn.delete { background: #e74c3c; color: white; }
        .action-btn.delete:hover { background: #c0392b; }
        
        .pagination { 
            display: flex; 
            justify-content: center; 
            gap: 5px; 
            margin-top: 25px; 
            flex-wrap: wrap; 
        }
        .pagination a, .pagination span { 
            padding: 6px 12px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            text-decoration: none; 
            color: #333; 
            font-size: 13px;
        }
        .pagination a:hover { background: #3498db; color: white; border-color: #3498db; }
        .pagination .active { background: #3498db; color: white; border-color: #3498db; }
        
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #059669; border-left: 4px solid #059669; }
        .alert-danger { background: #fee2e2; color: #dc2626; border-left: 4px solid #dc2626; }
        
        @media (max-width: 768px) { 
            .filter-section { flex-direction: column; align-items: stretch; }
            .filter-group input, .filter-group select { width: 100%; }
            .info-bar { flex-direction: column; align-items: flex-start; }
            .action-bar { flex-direction: column; align-items: stretch; }
            .btn-group-left { justify-content: space-between; }
            td:nth-child(2), td:nth-child(3) { white-space: normal; }
            .filter-section > div[style*="flex: 1"] { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-users"></i> Data Penduduk</h1>
        
        <div class="action-bar">
            <div class="btn-group-left">
                <a href="penduduk_tambah.php" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Penduduk</a>
            </div>
            <a href="dashboard_admin.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>
        
        <?php if(isset($success_hapus)): ?><div class="alert alert-success"><?php echo $success_hapus; ?></div><?php endif; ?>
        <?php if(isset($error_hapus)): ?><div class="alert alert-danger"><?php echo $error_hapus; ?></div><?php endif; ?>
        <?php if(isset($_GET['success'])): ?><div class="alert alert-success"><?php echo $_GET['success']=='tambah'?'Data berhasil ditambahkan!':'Data berhasil diperbarui!'; ?></div><?php endif; ?>
        
        <div class="filter-section">
            <div class="filter-group"><label><i class="fas fa-search"></i> Cari</label><input type="text" id="searchInput" placeholder="NIK / Nama / KK" value="<?php echo htmlspecialchars($search); ?>"></div>
            <div class="filter-group"><label><i class="fas fa-venus-mars"></i> JK</label><select id="jkFilter"><option value="">Semua</option><option value="Laki-laki" <?php echo $jenis_kelamin_filter=='Laki-laki'?'selected':''; ?>>Laki-laki</option><option value="Perempuan" <?php echo $jenis_kelamin_filter=='Perempuan'?'selected':''; ?>>Perempuan</option></select></div>
            <div class="filter-group"><button class="btn btn-primary" onclick="applyFilter()"><i class="fas fa-search"></i> Filter</button></div>
            <div class="filter-group"><a href="data_penduduk.php" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a></div>
            <div style="flex: 1;"></div>
            <div class="filter-group"><label><i class="fas fa-print"></i> Cetak</label><div style="display: flex; gap: 8px;"><select id="cetakJenis" style="padding: 8px; border-radius: 6px; border: 1px solid #ddd;"><option value="nik">Per NIK</option><option value="kk">Per KK</option></select><input type="text" id="cetakNilai" placeholder="NIK / No KK" style="width: 200px;"><button class="btn btn-warning" onclick="cetakPenduduk()" style="padding: 8px 12px;"><i class="fas fa-print"></i> Cetak</button></div></div>
        </div>
        
        <div class="info-bar">
            <div class="total-data"><i class="fas fa-database"></i> Total: <?php echo $total_data; ?> data</div>
            <div class="limit-select"><label><i class="fas fa-eye"></i> Tampilkan:</label><select id="limitSelect" onchange="changeLimit()"><option value="5" <?php echo $limit == 5 ? 'selected' : ''; ?>>5</option><option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option><option value="15" <?php echo $limit == 15 ? 'selected' : ''; ?>>15</option><option value="20" <?php echo $limit == 20 ? 'selected' : ''; ?>>20</option></select><span>data per halaman</span></div>
        </div>
        
        <div class="table-container">
            <?php if(mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>NO KK</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>Status Hubungan</th>
                        <th>Alamat</th>
                        <th>RT</th>
                        <th>RW</th>
                        <th>Desa/Kelurahan</th>
                        <th>Kecamatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = $offset + 1; while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $no++; ?></td>
                        <td style="white-space: nowrap;"><?php echo $row['nik']; ?></td>
                        <td style="white-space: nowrap;"><?php echo $row['no_kk']; ?></td>
                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td><span class="badge <?php echo $row['jenis_kelamin']=='Laki-laki'?'badge-laki':'badge-perempuan'; ?>"><?php echo $row['jenis_kelamin']; ?></span></td>
                        <td><span class="badge badge-status"><?php echo $row['status_hubungan'] ?: '-'; ?></span></td>
                        <td><?php echo nl2br(htmlspecialchars(substr($row['alamat'], 0, 80))); ?><?php echo strlen($row['alamat']) > 80 ? '...' : ''; ?></td>
                        <td><?php echo $row['rt']; ?></td>
                        <td><?php echo $row['rw']; ?></td>
                        <td><?php echo $row['desa_kelurahan']; ?></td>
                        <td><?php echo $row['kecamatan']; ?></td>
                        <td>
                            <a href="penduduk_edit.php?id=<?php echo $row['id']; ?>" class="action-btn edit"><i class="fas fa-edit"></i> Edit</a>
                            <a href="javascript:void(0)" onclick="if(confirm('Yakin hapus data penduduk <?php echo addslashes($row['nama_lengkap']); ?>?')) window.location.href='data_penduduk.php?hapus=<?php echo $row['id']; ?>'" class="action-btn delete"><i class="fas fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div style="text-align:center; padding:40px;">
                <i class="fas fa-inbox" style="font-size:48px; color:#ccc;"></i>
                <p>Tidak ada data penduduk.</p>
                <a href="penduduk_tambah.php" class="btn btn-success" style="margin-top:10px;"><i class="fas fa-plus"></i> Tambah Penduduk Pertama</a>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?><a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&jk=<?php echo $jenis_kelamin_filter; ?>&limit=<?php echo $limit; ?>"><i class="fas fa-chevron-left"></i> Prev</a><?php endif; ?>
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <?php if($i == $page): ?><span class="active"><?php echo $i; ?></span><?php else: ?><a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&jk=<?php echo $jenis_kelamin_filter; ?>&limit=<?php echo $limit; ?>"><?php echo $i; ?></a><?php endif; ?>
            <?php endfor; ?>
            <?php if($page < $total_pages): ?><a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&jk=<?php echo $jenis_kelamin_filter; ?>&limit=<?php echo $limit; ?>">Next <i class="fas fa-chevron-right"></i></a><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        function applyFilter() {
            var search = document.getElementById('searchInput').value;
            var jk = document.getElementById('jkFilter').value;
            var limit = document.getElementById('limitSelect').value;
            window.location.href = 'data_penduduk.php?search=' + encodeURIComponent(search) + '&jk=' + encodeURIComponent(jk) + '&limit=' + limit;
        }
        
        function changeLimit() {
            applyFilter();
        }
        
        function cetakPenduduk() {
            var jenis = document.getElementById('cetakJenis').value;
            var nilai = document.getElementById('cetakNilai').value.trim();
            if (nilai === '') { alert('Masukkan NIK atau No KK terlebih dahulu!'); return; }
            if (jenis === 'nik' && nilai.length !== 16) { alert('NIK harus 16 digit angka!'); return; }
            fetch('cek_data_penduduk.php?jenis=' + encodeURIComponent(jenis) + '&nilai=' + encodeURIComponent(nilai))
                .then(response => response.json())
                .then(data => { if (data.found) { window.open('cetak_penduduk.php?jenis=' + encodeURIComponent(jenis) + '&nilai=' + encodeURIComponent(nilai), '_blank'); } else { alert('Data ' + (jenis === 'nik' ? 'NIK' : 'No KK') + ' ' + nilai + ' tidak ditemukan!'); } })
                .catch(error => { alert('Terjadi kesalahan, coba lagi.'); });
        }
        
        document.getElementById('searchInput')?.addEventListener('keypress', function(e) { if(e.key === 'Enter') applyFilter(); });
        document.getElementById('cetakNilai')?.addEventListener('keypress', function(e) { if(e.key === 'Enter') cetakPenduduk(); });
    </script>
</body>
</html>