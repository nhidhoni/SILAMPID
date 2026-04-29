<?php
// File: admin/cek_status_warga.php
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: index.php?error=2");
    exit();
}
include '../koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pengajuan - SILAMPID</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: rgba(0,0,0,0.3);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 { font-size: 48px; margin-bottom: 10px; }
        .container {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .status-card {
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 550px;
        }
        .status-card h3 {
            color: #2c3e50;
            text-align: center;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .status-card .sub {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }
        .status-search {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .status-search input {
            flex: 1;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
        }
        .status-search button {
            padding: 14px 28px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }
        .status-result {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        .status-item {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .status-label {
            font-weight: bold;
            width: 120px;
            color: #555;
        }
        .status-value { flex: 1; }
        .badge-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-pending { background: #fef3c7; color: #d97706; }
        .badge-diproses { background: #dbeafe; color: #2563eb; }
        .badge-selesai { background: #d1fae5; color: #059669; }
        .badge-ditolak { background: #fee2e2; color: #dc2626; }
        .error-msg {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 10px;
            margin-top: 15px;
            text-align: center;
        }
        .btn-back {
            display: inline-block;
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
            padding: 14px 24px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            text-align: center;
            margin-top: 25px;
        }
        .button-group { display: flex; justify-content: center; margin-top: 30px; }
        .footer {
            text-align: center;
            padding: 20px;
            color: white;
            background: rgba(0,0,0,0.2);
        }
        @media (max-width: 768px) {
            .status-search { flex-direction: column; }
            .status-item { flex-direction: column; }
            .status-label { width: 100%; margin-bottom: 5px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SILAMPID</h1>
        <p>Sistem Informasi Administrasi Kependudukan</p>
    </div>
    <div class="container">
        <div class="status-card">
            <h3><i class="fas fa-search"></i> Cek Status Pengajuan</h3>
            <div class="sub">Masukkan kode unik yang Anda terima</div>
            <div class="status-search">
                <input type="text" id="kodeUnik" placeholder="Contoh: ABC12345">
                <button onclick="cekStatus()" id="btnCek"><i class="fas fa-search"></i> Cek</button>
            </div>
            <div id="statusResult"></div>
            <div class="button-group">
                <a href="dashboard_admin.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
    <div class="footer">&copy; 2025 SILAMPID</div>
    <script>
        async function cekStatus() {
            const kodeUnik = document.getElementById('kodeUnik').value.trim();
            const resultDiv = document.getElementById('statusResult');
            const btnCek = document.getElementById('btnCek');
            
            if (kodeUnik === '') {
                resultDiv.innerHTML = '<div class="error-msg">Masukkan kode unik terlebih dahulu!</div>';
                return;
            }
            
            btnCek.innerHTML = '<span class="loading"></span> Mencari...';
            btnCek.disabled = true;
            
            try {
                const response = await fetch(`ajax_cek_status.php?kode=${encodeURIComponent(kodeUnik)}`);
                const data = await response.json();
                
                if (data.success) {
                    let statusBadge = '';
                    switch(data.status) {
                        case 'Pending': statusBadge = '<span class="badge-status badge-pending">⏳ Pending</span>'; break;
                        case 'Diproses': statusBadge = '<span class="badge-status badge-diproses">🔄 Diproses</span>'; break;
                        case 'Selesai': statusBadge = '<span class="badge-status badge-selesai">✅ Selesai</span>'; break;
                        case 'Ditolak': statusBadge = '<span class="badge-status badge-ditolak">❌ Ditolak</span>'; break;
                        default: statusBadge = data.status;
                    }
                    
                    resultDiv.innerHTML = `
                        <div class="status-result">
                            <div class="status-item"><div class="status-label">Kode Unik</div><div class="status-value"><strong>${data.kode_unik}</strong></div></div>
                            <div class="status-item"><div class="status-label">NIK</div><div class="status-value">${data.nik_warga}</div></div>
                            <div class="status-item"><div class="status-label">Nama</div><div class="status-value">${data.nama_warga}</div></div>
                            <div class="status-item"><div class="status-label">Jenis Surat</div><div class="status-value">${data.jenis_surat}</div></div>
                            <div class="status-item"><div class="status-label">Tanggal Pengajuan</div><div class="status-value">${data.tgl_pengajuan}</div></div>
                            <div class="status-item"><div class="status-label">Status</div><div class="status-value">${statusBadge}</div></div>
                            ${data.catatan_admin ? `<div class="status-item"><div class="status-label">Catatan Admin</div><div class="status-value">${data.catatan_admin}</div></div>` : ''}
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `<div class="error-msg">${data.message}</div>`;
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error-msg">Terjadi kesalahan, coba lagi.</div>';
            } finally {
                btnCek.innerHTML = '<i class="fas fa-search"></i> Cek';
                btnCek.disabled = false;
            }
        }
        
        document.getElementById('kodeUnik')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') cekStatus();
        });
    </script>
</body>
</html>