<?php
// File: admin/pindah_datang.php - Form Pindah Datang (Manual Semua + catatan_admin kosong)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../koneksi.php';

// Deteksi apakah user sudah login sebagai admin
session_start();
$is_admin = isset($_SESSION['login']) && $_SESSION['login'] === true;
$home_target = $is_admin ? "dashboard_admin.php" : "../warga/beranda.php";
$home_text = $is_admin ? "Ke Dashboard" : "Ke Beranda";

$success = false;
$error = '';
$no_surat = '';
$kode_unik = '';

// Fungsi generate nomor surat
function generateNoSurat($conn) {
    $tahun = date('Y');
    $bulan = date('m');
    $query = "SELECT COUNT(*) as total FROM pengajuan WHERE jenis_surat='Pindah Datang' AND YEAR(tgl_pengajuan)='$tahun'";
    $result = mysqli_query($conn, $query);
    $count = mysqli_fetch_assoc($result)['total'];
    $urutan = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return "474.6/$tahun/$bulan/$urutan";
}

// Proses form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari form (semua manual)
    $nik = isset($_POST['nik']) ? mysqli_real_escape_string($conn, $_POST['nik']) : '';
    $no_kk = isset($_POST['no_kk']) ? mysqli_real_escape_string($conn, $_POST['no_kk']) : '';
    $nama = isset($_POST['nama']) ? mysqli_real_escape_string($conn, $_POST['nama']) : '';
    $jenis_kelamin = isset($_POST['jenis_kelamin']) ? mysqli_real_escape_string($conn, $_POST['jenis_kelamin']) : '';
    $status_hubungan = isset($_POST['status_hubungan']) ? mysqli_real_escape_string($conn, $_POST['status_hubungan']) : '';
    $tempat_lahir = isset($_POST['tempat_lahir']) ? mysqli_real_escape_string($conn, $_POST['tempat_lahir']) : '';
    $tanggal_lahir = isset($_POST['tanggal_lahir']) ? mysqli_real_escape_string($conn, $_POST['tanggal_lahir']) : '';
    $alamat_asal = isset($_POST['alamat_asal']) ? mysqli_real_escape_string($conn, $_POST['alamat_asal']) : '';
    $alamat_tujuan = isset($_POST['alamat_tujuan']) ? mysqli_real_escape_string($conn, $_POST['alamat_tujuan']) : '';
    $alasan_pindah = isset($_POST['alasan_pindah']) ? mysqli_real_escape_string($conn, $_POST['alasan_pindah']) : '';
    $tanggal_datang = isset($_POST['tanggal_datang']) ? mysqli_real_escape_string($conn, $_POST['tanggal_datang']) : '';
    
    // Generate kode unik dan nomor surat
    $kode_unik = strtoupper(substr(md5(uniqid()), 0, 8));
    $no_surat = generateNoSurat($conn);
    
    // INSERT KE PENGAJUAN (catatan_admin KOSONG)
    $query = "INSERT INTO pengajuan (id_penduduk, nik_warga, nama_warga, jenis_surat, tgl_pengajuan, status, kode_unik, catatan_admin) 
              VALUES (0, '$nik', '$nama', 'Pindah Datang', CURDATE(), 'Pending', '$kode_unik', '')";
    
    if(mysqli_query($conn, $query)) {
        $success = true;
    } else {
        $error = "Gagal simpan pengajuan: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pindah Datang - SILAMPID</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        h1 { color: #2c3e50; margin-bottom: 10px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .subtitle { color: #7f8c8d; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #ecf0f1; }
        
        .form-section { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px; }
        .form-section h3 { color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #ddd; display: flex; align-items: center; gap: 10px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .form-group { margin-bottom: 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; font-size: 13px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #3498db; }
        .full-width { grid-column: span 2; }
        
        .btn-group { display: flex; gap: 15px; margin-top: 25px; flex-wrap: wrap; }
        .btn-submit { background: #27ae60; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .btn-submit:hover { background: #219a52; transform: translateY(-2px); }
        .btn-back { background: #95a5a6; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; display: inline-block; transition: all 0.3s; }
        .btn-back:hover { background: #7f8c8d; transform: translateY(-2px); }
        
        /* ALERT MODAL */
        .alert-overlay {
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
        .alert-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .alert-box .icon { font-size: 64px; color: #27ae60; margin-bottom: 20px; }
        .alert-box h3 { color: #2c3e50; margin-bottom: 15px; }
        .alert-box .kode-unik {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #3498db;
            border: 1px dashed #3498db;
        }
        .alert-box .info { font-size: 12px; color: #7f8c8d; margin-top: 10px; }
        .alert-box button { background: #3498db; color: white; padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer; margin-top: 15px; font-size: 14px; }
        .alert-box button:hover { background: #2980b9; }
        
        .alert-error { background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .full-width { grid-column: span 1; } .btn-group { flex-direction: column; } .btn-submit, .btn-back { text-align: center; } }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-sign-in-alt"></i> Formulir Pindah Datang</h1>
        <div class="subtitle">Form pendaftaran pindah datang</div>
        
        <?php if($error): ?>
            <div class="alert-error"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-section">
                <h3><i class="fas fa-user"></i> Data Pemohon</h3>
                <div class="form-grid">
                    <div class="form-group"><label>NIK *</label><input type="text" name="nik" maxlength="16" pattern="[0-9]{16}" required placeholder="16 digit NIK"></div>
                    <div class="form-group"><label>No KK *</label><input type="text" name="no_kk" maxlength="16" required placeholder="Nomor Kartu Keluarga"></div>
                    <div class="form-group full-width"><label>Nama Lengkap *</label><input type="text" name="nama" required></div>
                    <div class="form-group"><label>Jenis Kelamin *</label>
                        <select name="jenis_kelamin" required>
                            <option value="">Pilih</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Status Hubungan *</label>
                        <select name="status_hubungan" required>
                            <option value="">Pilih</option>
                            <option value="Kepala Keluarga">Kepala Keluarga</option>
                            <option value="Istri">Istri</option>
                            <option value="Anak">Anak</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Tempat Lahir</label><input type="text" name="tempat_lahir"></div>
                    <div class="form-group"><label>Tanggal Lahir</label><input type="date" name="tanggal_lahir"></div>
                    <div class="form-group full-width"><label>Alamat Asal *</label><textarea name="alamat_asal" rows="2" required placeholder="Alamat lengkap sebelum pindah"></textarea></div>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-truck-moving"></i> Data Pindah</h3>
                <div class="form-grid">
                    <div class="form-group full-width"><label>Alamat Tujuan *</label><textarea name="alamat_tujuan" rows="2" required placeholder="Alamat lengkap setelah pindah"></textarea></div>
                    <div class="form-group"><label>Alasan Pindah *</label><input type="text" name="alasan_pindah" required placeholder="Contoh: Pekerjaan, Keluarga, dll"></div>
                    <div class="form-group"><label>Tanggal Datang *</label><input type="date" name="tanggal_datang" required></div>
                </div>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Kirim Pengajuan</button>
                <a href="<?php echo $home_target; ?>" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
    
    <!-- ALERT MODAL -->
    <div id="successModal" class="alert-overlay">
        <div class="alert-box">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <h3>Pengajuan Berhasil!</h3>
            <p>Data pindah datang telah berhasil disimpan.</p>
            <div class="kode-unik">
                <i class="fas fa-qrcode"></i> <?php echo $kode_unik; ?>
            </div>
            <p><strong>Nomor Surat:</strong> <?php echo $no_surat; ?></p>
            <div class="info">
                <i class="fas fa-info-circle"></i> Simpan Kode Unik ini untuk mengecek status pengajuan Anda.
            </div>
            <button onclick="closeAlert()"><i class="fas fa-check"></i> OK</button>
        </div>
    </div>
    
    <script>
        <?php if($success): ?>
        document.getElementById('successModal').style.display = 'flex';
        <?php endif; ?>
        
        function closeAlert() {
            document.getElementById('successModal').style.display = 'none';
            window.location.href = '<?php echo $home_target; ?>';
        }
        
        window.onclick = function(event) {
            var modal = document.getElementById('successModal');
            if (event.target == modal) {
                closeAlert();
            }
        }
    </script>
</body>
</html>