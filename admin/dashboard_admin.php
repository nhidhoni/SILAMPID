<?php
// File: admin/dashboard_admin.php
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: index.php?error=2");
    exit();
}
include '../koneksi.php';

// Ambil statistik
$total_pengajuan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan"))['total'];
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan WHERE status='Pending'"))['total'];
$total_diproses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan WHERE status='Diproses'"))['total'];
$total_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan WHERE status='Selesai'"))['total'];
$total_ditolak = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan WHERE status='Ditolak'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SILAMPID</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
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
        .nav-menu a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; transition: all 0.3s; }
        .nav-menu a:hover { background: #3498db; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .logout-btn { background: #e74c3c; padding: 6px 12px; border-radius: 5px; }
        .logout-btn:hover { background: #c0392b; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .welcome-section {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .welcome-section h1 { font-size: 28px; margin-bottom: 10px; }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-card h3 { font-size: 14px; color: #7f8c8d; margin-bottom: 10px; }
        .stat-card .number { font-size: 32px; font-weight: bold; color: #2c3e50; }
        .stat-card.pending .number { color: #e74c3c; }
        .stat-card.diproses .number { color: #f39c12; }
        .stat-card.selesai .number { color: #27ae60; }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        .menu-card {
            background: white;
            padding: 25px 20px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: #2c3e50;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: block;
            cursor: pointer;
        }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .menu-card .icon { font-size: 48px; margin-bottom: 12px; }
        .menu-card h3 { font-size: 16px; margin-bottom: 8px; }
        .menu-card p { font-size: 12px; color: #7f8c8d; }
        /* Warna icon spesifik untuk setiap menu - SELARAS dengan beranda */
        .menu-card.lahir .icon { color: #3498db; }
        .menu-card.kematian .icon { color: #7f8c8d; }
        .menu-card.pindah-datang .icon { color: #27ae60; }
        .menu-card.pindah-keluar .icon { color: #f39c12; }
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
        .modal-header h3 { color: #2c3e50; }
        .close { cursor: pointer; font-size: 24px; color: #7f8c8d; }
        .close:hover { color: #2c3e50; }
        .modal-input { width: 100%; padding: 12px; margin: 15px 0; border: 1px solid #ddd; border-radius: 8px; }
        .btn-cek { width: 100%; padding: 12px; background: #27ae60; color: white; border: none; border-radius: 8px; margin-bottom: 10px; cursor: pointer; font-weight: bold; }
        .btn-kembali { width: 100%; padding: 12px; background: #95a5a6; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .footer {
            text-align: center;
            padding: 25px;
            background: white;
            margin-top: 40px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 12px;
        }
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 15px; text-align: center; }
            .menu-grid { grid-template-columns: repeat(2, 1fr); }
            .stats { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) { .menu-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-id-card"></i><span>SILAMPID</span></div>
        <div class="nav-menu">
            <a href="ganti_password.php" style="background: #f39c12;"><i class="fas fa-key"></i> Ganti Password</a>
            <?php if($_SESSION['role'] == 'admin'): ?>
            <a href="manajemen_user.php"><i class="fas fa-users-gear"></i> Manajemen User</a>
            <?php endif; ?>
            <div class="user-info">
                <span><i class="fas fa-user-circle"></i> <?php echo $_SESSION['nama_lengkap']; ?> (<?php echo $_SESSION['role']; ?>)</span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <h1><i class="fas fa-chalkboard-user"></i> Dashboard Admin</h1>
            <p>Selamat datang di panel administrator SILAMPID. Kelola data kependudukan dengan mudah dan cepat.</p>
        </div>

        <div class="stats">
            <div class="stat-card"><h3>Total Pengajuan</h3><div class="number"><?php echo $total_pengajuan; ?></div></div>
            <div class="stat-card pending"><h3>Menunggu Verifikasi</h3><div class="number"><?php echo $total_pending; ?></div></div>
            <div class="stat-card diproses"><h3>Sedang Diproses</h3><div class="number"><?php echo $total_diproses; ?></div></div>
            <div class="stat-card selesai"><h3>Selesai / Ditolak</h3><div class="number"><?php echo $total_selesai + $total_ditolak; ?></div></div>
        </div>

        <div class="menu-grid">
            <!-- Lahir - Icon Bayi (fa-baby) SELARAS dengan beranda -->
            <a href="lahir.php" class="menu-card lahir">
                <div class="icon"><i class="fas fa-baby"></i></div>
                <h3>Lahir</h3>
                <p>Input data kelahiran & akta</p>
            </a>
            
            <!-- Kematian - Icon Kepala Tengkorak (fa-skull) SELARAS dengan beranda -->
            <a href="kematian.php" class="menu-card kematian">
                <div class="icon"><i class="fas fa-skull"></i></div>
                <h3>Kematian</h3>
                <p>Input data kematian</p>
            </a>
            
            <!-- Pindah Datang - Icon SIGN IN (fa-sign-in-alt) SELARAS dengan beranda -->
            <a href="pindah_datang.php" class="menu-card pindah-datang">
                <div class="icon"><i class="fas fa-sign-in-alt"></i></div>
                <h3>Pindah Datang</h3>
                <p>Input pindah datang</p>
            </a>
            
            <!-- Pindah Keluar - Icon SIGN OUT (fa-sign-out-alt) SELARAS dengan beranda -->
            <a href="pindah_keluar.php" class="menu-card pindah-keluar">
                <div class="icon"><i class="fas fa-sign-out-alt"></i></div>
                <h3>Pindah Keluar</h3>
                <p>Input pindah keluar</p>
            </a>
            
            <!-- Semua Pengajuan -->
            <a href="daftar_pengajuan.php" class="menu-card">
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                <h3>Semua Pengajuan</h3>
                <p>Kelola semua pengajuan</p>
            </a>
            
            <!-- Data Penduduk -->
            <a href="data_penduduk.php" class="menu-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <h3>Data Penduduk</h3>
                <p>Kelola data warga</p>
            </a>
            
            <!-- Laporan Bulanan -->
            <a href="laporan_bulanan.php" class="menu-card">
                <div class="icon"><i class="fas fa-file-pdf"></i></div>
                <h3>Laporan Bulanan</h3>
                <p>Cetak laporan statistik</p>
            </a>
            
            <!-- Cek Status -->
            <div onclick="showCekStatusModal()" class="menu-card" style="cursor: pointer;">
                <div class="icon"><i class="fas fa-search"></i></div>
                <h3>Cek Status</h3>
                <p>Cek status pengajuan warga</p>
            </div>
        </div>
    </div>

    <div id="cekStatusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h3><i class="fas fa-search"></i> Cek Status Pengajuan</h3><span class="close" onclick="closeModal()">&times;</span></div>
            <p>Masukkan kode unik yang Anda terima saat mengajukan surat</p>
            <input type="text" id="kodeUnikModal" class="modal-input" placeholder="Contoh: ABC12345">
            <button onclick="cekStatus()" class="btn-cek"><i class="fas fa-search"></i> Cek Status</button>
            <button onclick="closeModal()" class="btn-kembali"><i class="fas fa-arrow-left"></i> Kembali</button>
            <div id="hasilStatus" style="margin-top:15px;"></div>
        </div>
    </div>

    <div class="footer"><p>&copy; 2025 SILAMPID - Dinas Dukcapil Kabupaten Pakpak Bharat</p></div>

    <script>
        function showCekStatusModal() {
            document.getElementById('cekStatusModal').style.display = 'flex';
            document.getElementById('kodeUnikModal').value = '';
            document.getElementById('hasilStatus').innerHTML = '';
        }
        function closeModal() { document.getElementById('cekStatusModal').style.display = 'none'; }
        async function cekStatus() {
            const kode = document.getElementById('kodeUnikModal').value.trim();
            const hasil = document.getElementById('hasilStatus');
            if(!kode) { hasil.innerHTML = '<div style="background:#fee2e2;padding:12px;border-radius:8px;color:#dc2626;">Masukkan kode unik terlebih dahulu!</div>'; return; }
            hasil.innerHTML = '<div style="background:#dbeafe;padding:12px;border-radius:8px;">⏳ Mencari data...</div>';
            try {
                const response = await fetch(`ajax_cek_status.php?kode=${encodeURIComponent(kode)}`);
                const data = await response.json();
                if(data.success) {
                    let badge = '';
                    switch(data.status) {
                        case 'Pending': badge = '<span style="background:#fef3c7;color:#d97706;padding:4px 12px;border-radius:20px;">⏳ Pending</span>'; break;
                        case 'Diproses': badge = '<span style="background:#dbeafe;color:#2563eb;padding:4px 12px;border-radius:20px;">🔄 Diproses</span>'; break;
                        case 'Selesai': badge = '<span style="background:#d1fae5;color:#059669;padding:4px 12px;border-radius:20px;">✅ Selesai</span>'; break;
                        case 'Ditolak': badge = '<span style="background:#fee2e2;color:#dc2626;padding:4px 12px;border-radius:20px;">❌ Ditolak</span>'; break;
                        default: badge = data.status;
                    }
                    hasil.innerHTML = `<div style="background:#f8f9fa;padding:15px;border-radius:10px;margin-top:15px;"><div style="padding:8px 0;border-bottom:1px solid #eee;"><strong>Kode Unik:</strong> ${data.kode_unik}</div><div style="padding:8px 0;border-bottom:1px solid #eee;"><strong>NIK:</strong> ${data.nik_warga}</div><div style="padding:8px 0;border-bottom:1px solid #eee;"><strong>Nama:</strong> ${data.nama_warga}</div><div style="padding:8px 0;border-bottom:1px solid #eee;"><strong>Jenis Surat:</strong> ${data.jenis_surat}</div><div style="padding:8px 0;"><strong>Status:</strong> ${badge}</div>${data.catatan_admin ? `<div style="padding:8px 0;border-top:1px solid #eee;margin-top:8px;"><strong>Catatan Admin:</strong><br>${data.catatan_admin}</div>` : ''}</div>`;
                } else { hasil.innerHTML = `<div style="background:#fee2e2;padding:12px;border-radius:8px;color:#dc2626;">${data.message}</div>`; }
            } catch(error) { hasil.innerHTML = '<div style="background:#fee2e2;padding:12px;border-radius:8px;color:#dc2626;">Terjadi kesalahan, coba lagi.</div>'; }
        }
        document.getElementById('kodeUnikModal')?.addEventListener('keypress', function(e) { if(e.key === 'Enter') cekStatus(); });
        window.onclick = function(event) { const modal = document.getElementById('cekStatusModal'); if(event.target == modal) closeModal(); }
    </script>
</body>
</html>