<?php
// File: warga/beranda.php - Halaman utama warga (Icon Sama dengan Admin)
// Halaman publik untuk layanan warga
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>SILAMPID - Sistem Informasi Administrasi Kependudukan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Navbar - SAMA dengan Admin Dashboard */
        .navbar {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            color: white;
            padding: 12px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            flex-shrink: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: bold;
        }
        .logo i {
            font-size: 28px;
            color: #3498db;
        }
        .nav-menu {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
        }
        .nav-menu a:hover, .nav-menu a.active {
            background: #3498db;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px 20px 15px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 24px 28px;
            border-radius: 15px;
            margin-bottom: 28px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .welcome-section h1 {
            font-size: 24px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .welcome-section p {
            opacity: 0.95;
            font-size: 14px;
        }
        
        /* Service Grid */
        .service-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        
        /* Style Card Layanan */
        .service-card {
            background: white;
            border-radius: 12px;
            padding: 22px 15px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: block;
            color: #2c3e50;
            cursor: pointer;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .service-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }
        .service-card h3 {
            font-size: 18px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .service-card p {
            font-size: 12px;
            color: #7f8c8d;
        }
        .badge-layanan {
            display: inline-block;
            background: #e8f4f8;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10px;
            margin-top: 10px;
            color: #2980b9;
        }
        
        /* Warna Icon spesifik */
        .card-lahir .service-icon { color: #3498db; }
        .card-kematian .service-icon { color: #7f8c8d; }
        .card-pindah-datang .service-icon { color: #27ae60; }
        .card-pindah-keluar .service-icon { color: #f39c12; }
        
        /* Card Cek Status */
        .service-card-large {
            background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
            border-radius: 12px;
            padding: 22px 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            color: white;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .service-card-large:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .service-card-large .service-icon {
            font-size: 48px;
            margin-bottom: 12px;
            color: white;
        }
        .service-card-large h3 {
            font-size: 20px;
            margin-bottom: 8px;
            color: white;
        }
        .service-card-large p {
            font-size: 12px;
            color: rgba(255,255,255,0.9);
        }
        
        /* Grid positioning */
        .grid-lahir { grid-row: 1; grid-column: 1; }
        .grid-kematian { grid-row: 1; grid-column: 3; }
        .grid-pindah-datang { grid-row: 2; grid-column: 1; }
        .grid-pindah-keluar { grid-row: 2; grid-column: 3; }
        .grid-cekstatus { grid-row: 1 / span 2; grid-column: 2; }
        
        /* Admin Login Button */
        .admin-section {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e0e6ed;
        }
        .btn-admin {
            background: #2c3e50;
            color: white;
            padding: 10px 28px;
            border-radius: 30px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-admin:hover {
            background: #3498db;
            transform: translateY(-2px);
        }
        
        /* Modal */
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
            box-shadow: 0 20px 35px rgba(0,0,0,0.2);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ecf0f1;
        }
        .modal-header h3 { color: #2c3e50; font-size: 18px; }
        .close {
            cursor: pointer;
            font-size: 28px;
            color: #7f8c8d;
            line-height: 1;
        }
        .close:hover { color: #2c3e50; }
        .modal-input {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        .btn-cek {
            width: 100%;
            padding: 12px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }
        .btn-cek:hover { background: #229954; }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 15px 20px;
            background: white;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 12px;
            flex-shrink: 0;
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 12px;
                text-align: center;
                padding: 12px 20px;
            }
            .nav-menu {
                justify-content: center;
            }
            .service-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .grid-lahir, .grid-kematian, .grid-pindah-datang, .grid-pindah-keluar, .grid-cekstatus {
                grid-row: auto;
                grid-column: 1;
            }
            .service-card-large {
                min-height: auto;
            }
            .welcome-section h1 {
                font-size: 20px;
            }
            .welcome-section {
                padding: 18px;
            }
            .service-icon, .service-card-large .service-icon {
                font-size: 40px;
            }
        }
        
        @media (max-width: 480px) {
            .service-card h3, .service-card-large h3 {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-id-card"></i>
            <span>SILAMPID</span>
        </div>
        <div class="nav-menu">
            <a href="beranda.php" class="active"><i class="fas fa-home"></i> Beranda</a>
            <a href="#layanan-lahir"><i class="fas fa-baby"></i> Lahir</a>
            <a href="#layanan-kematian"><i class="fas fa-skull"></i> Kematian</a>
            <a href="#layanan-pindah-datang"><i class="fas fa-sign-in-alt"></i> Pindah Datang</a>
            <a href="#layanan-pindah-keluar"><i class="fas fa-sign-out-alt"></i> Pindah Keluar</a>
            <a href="#layanan-cekstatus"><i class="fas fa-search"></i> Cek Status</a>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="welcome-section">
                <h1><i class="fas fa-hand-peace"></i> Selamat Datang di SILAMPID</h1>
                <p>Sistem Informasi Administrasi Kependudukan - Dinas Dukcapil Kabupaten Pakpak Bharat. Ajukan surat kependudukan secara online dengan mudah dan cepat.</p>
            </div>

            <div class="service-grid">
                <!-- Lahir - Icon Bayi (fa-baby) -->
                <a href="../admin/lahir.php" class="service-card card-lahir grid-lahir" id="layanan-lahir">
                    <div class="service-icon"><i class="fas fa-baby"></i></div>
                    <h3>Lahir</h3>
                    <p>Pendaftaran Akta Kelahiran</p>
                    <span class="badge-layanan"><i class="fas fa-clock"></i> Online 24 Jam</span>
                </a>

                <!-- Kematian - Icon Kepala Tengkorak (fa-skull) -->
                <a href="../admin/kematian.php" class="service-card card-kematian grid-kematian" id="layanan-kematian">
                    <div class="service-icon"><i class="fas fa-skull"></i></div>
                    <h3>Kematian</h3>
                    <p>Pencatatan Akta Kematian</p>
                    <span class="badge-layanan"><i class="fas fa-file-signature"></i> Proses Cepat</span>
                </a>

                <!-- Pindah Datang - Icon SIGN IN (fa-sign-in-alt) - orang masuk -->
                <a href="../admin/pindah_datang.php" class="service-card card-pindah-datang grid-pindah-datang" id="layanan-pindah-datang">
                    <div class="service-icon"><i class="fas fa-sign-in-alt"></i></div>
                    <h3>Pindah Datang</h3>
                    <p>Pengurusan surat kedatangan & domisili</p>
                    <span class="badge-layanan"><i class="fas fa-home"></i> Domisili Baru</span>
                </a>

                <!-- Pindah Keluar - Icon SIGN OUT (fa-sign-out-alt) - orang keluar -->
                <a href="../admin/pindah_keluar.php" class="service-card card-pindah-keluar grid-pindah-keluar" id="layanan-pindah-keluar">
                    <div class="service-icon"><i class="fas fa-sign-out-alt"></i></div>
                    <h3>Pindah Keluar</h3>
                    <p>Pengurusan surat pindah keluar daerah</p>
                    <span class="badge-layanan"><i class="fas fa-truck"></i> Migrasi</span>
                </a>

                <!-- Cek Status (Large) -->
                <div onclick="showCekStatus()" class="service-card-large grid-cekstatus" id="layanan-cekstatus" style="cursor: pointer;">
                    <div class="service-icon"><i class="fas fa-search"></i></div>
                    <h3>Cek Status</h3>
                    <p>Lacak status pengajuan surat Anda</p>
                    <p style="margin-top: 6px; font-size: 11px; font-weight: 300;"><i class="fas fa-qrcode"></i> Masukkan kode unik yang Anda terima</p>
                </div>
            </div>

            <div class="admin-section">
                <a href="../admin/index.php" class="btn-admin">
                    <i class="fas fa-user-shield"></i> Login Admin
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Cek Status -->
    <div id="cekStatusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-search"></i> Cek Status Pengajuan</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <p>Masukkan kode unik yang Anda terima saat mengajukan surat</p>
            <input type="text" id="kodeUnikModal" class="modal-input" placeholder="Contoh: ABC12345 atau AK-2025/001">
            <button onclick="cekStatus()" class="btn-cek"><i class="fas fa-search"></i> Cek Status</button>
            <div id="hasilStatus" style="margin-top:15px;"></div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 SILAMPID - Dinas Dukcapil Kabupaten Pakpak Bharat | Pelayanan Prima, Data Akurat</p>
    </div>

    <script>
        function showCekStatus() {
            document.getElementById('cekStatusModal').style.display = 'flex';
            document.getElementById('kodeUnikModal').value = '';
            document.getElementById('hasilStatus').innerHTML = '';
        }

        function closeModal() {
            document.getElementById('cekStatusModal').style.display = 'none';
        }

        async function cekStatus() {
            const kode = document.getElementById('kodeUnikModal').value.trim();
            const hasil = document.getElementById('hasilStatus');
            
            if(!kode) {
                hasil.innerHTML = '<div style="background:#fee2e2;padding:12px;border-radius:8px;color:#dc2626;">❌ Masukkan kode unik terlebih dahulu!</div>';
                return;
            }
            
            hasil.innerHTML = '<div style="background:#dbeafe;padding:12px;border-radius:8px;"><i class="fas fa-spinner fa-pulse"></i> ⏳ Mencari data...</div>';
            
            try {
                const response = await fetch(`../admin/ajax_cek_status.php?kode=${encodeURIComponent(kode)}`);
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
                    
                    hasil.innerHTML = `
                        <div style="background:#f8f9fa;padding:15px;border-radius:10px;margin-top:15px; border:1px solid #e2e8f0;">
                            <div style="padding:8px 0;border-bottom:1px solid #e2e8f0;"><strong><i class="fas fa-hashtag"></i> Kode Unik:</strong> ${data.kode_unik}</div>
                            <div style="padding:8px 0;border-bottom:1px solid #e2e8f0;"><strong><i class="fas fa-id-card"></i> NIK:</strong> ${data.nik_warga}</div>
                            <div style="padding:8px 0;border-bottom:1px solid #e2e8f0;"><strong><i class="fas fa-user"></i> Nama:</strong> ${data.nama_warga}</div>
                            <div style="padding:8px 0;border-bottom:1px solid #e2e8f0;"><strong><i class="fas fa-file-alt"></i> Jenis Surat:</strong> ${data.jenis_surat}</div>
                            <div style="padding:8px 0;"><strong><i class="fas fa-chart-line"></i> Status:</strong> ${badge}</div>
                            ${data.catatan_admin ? `<div style="padding:8px 0;border-top:1px solid #e2e8f0;margin-top:8px;"><strong><i class="fas fa-sticky-note"></i> Catatan Admin:</strong><br>${data.catatan_admin}</div>` : ''}
                        </div>
                    `;
                } else {
                    hasil.innerHTML = `<div style="background:#fee2e2;padding:12px;border-radius:8px;color:#dc2626;"><i class="fas fa-times-circle"></i> ${data.message}</div>`;
                }
            } catch(error) {
                console.error(error);
                hasil.innerHTML = '<div style="background:#fee2e2;padding:12px;border-radius:8px;color:#dc2626;"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan koneksi. Silakan coba lagi nanti.</div>';
            }
        }

        document.getElementById('kodeUnikModal')?.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') cekStatus();
        });

        window.onclick = function(event) {
            const modal = document.getElementById('cekStatusModal');
            if(event.target == modal) closeModal();
        }

        document.querySelectorAll('.nav-menu a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if(targetId && targetId !== '#') {
                    const targetElement = document.querySelector(targetId);
                    if(targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
    </script>
</body>
</html>