<?php
// File: admin/lahir.php - Form Pencatatan Kelahiran (Auto-fill Ibu & Ayah + catatan_admin kosong)
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
$no_akta = '';
$kode_unik = '';

// Fungsi generate nomor akta
function generateNoAkta($conn) {
    $tahun = date('Y');
    $bulan = date('m');
    $query = "SELECT COUNT(*) as total FROM pengajuan WHERE jenis_surat='Lahir' AND YEAR(tgl_pengajuan)='$tahun'";
    $result = mysqli_query($conn, $query);
    $count = mysqli_fetch_assoc($result)['total'];
    $urutan = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return "474.3/$tahun/$bulan/$urutan";
}

// Proses form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Data anak/bayi
    $nama_anak = isset($_POST['nama_anak']) ? mysqli_real_escape_string($conn, $_POST['nama_anak']) : '';
    $tempat_lahir = isset($_POST['tempat_lahir']) ? mysqli_real_escape_string($conn, $_POST['tempat_lahir']) : '';
    $tanggal_lahir = isset($_POST['tanggal_lahir']) ? mysqli_real_escape_string($conn, $_POST['tanggal_lahir']) : '';
    $jenis_kelamin = isset($_POST['jenis_kelamin']) ? mysqli_real_escape_string($conn, $_POST['jenis_kelamin']) : '';
    $anak_ke = isset($_POST['anak_ke']) ? mysqli_real_escape_string($conn, $_POST['anak_ke']) : '';
    $berat_bayi = isset($_POST['berat_bayi']) ? mysqli_real_escape_string($conn, $_POST['berat_bayi']) : '';
    $panjang_bayi = isset($_POST['panjang_bayi']) ? mysqli_real_escape_string($conn, $_POST['panjang_bayi']) : '';
    
    // Data ibu
    $nik_ibu = isset($_POST['nik_ibu']) ? mysqli_real_escape_string($conn, $_POST['nik_ibu']) : '';
    $nama_ibu = isset($_POST['nama_ibu']) ? mysqli_real_escape_string($conn, $_POST['nama_ibu']) : '';
    $tempat_lahir_ibu = isset($_POST['tempat_lahir_ibu']) ? mysqli_real_escape_string($conn, $_POST['tempat_lahir_ibu']) : '';
    $tanggal_lahir_ibu = isset($_POST['tanggal_lahir_ibu']) ? mysqli_real_escape_string($conn, $_POST['tanggal_lahir_ibu']) : '';
    $pekerjaan_ibu = isset($_POST['pekerjaan_ibu']) ? mysqli_real_escape_string($conn, $_POST['pekerjaan_ibu']) : '';
    $alamat_ibu = isset($_POST['alamat_ibu']) ? mysqli_real_escape_string($conn, $_POST['alamat_ibu']) : '';
    
    // Data ayah
    $nik_ayah = isset($_POST['nik_ayah']) ? mysqli_real_escape_string($conn, $_POST['nik_ayah']) : '';
    $nama_ayah = isset($_POST['nama_ayah']) ? mysqli_real_escape_string($conn, $_POST['nama_ayah']) : '';
    $tempat_lahir_ayah = isset($_POST['tempat_lahir_ayah']) ? mysqli_real_escape_string($conn, $_POST['tempat_lahir_ayah']) : '';
    $tanggal_lahir_ayah = isset($_POST['tanggal_lahir_ayah']) ? mysqli_real_escape_string($conn, $_POST['tanggal_lahir_ayah']) : '';
    $pekerjaan_ayah = isset($_POST['pekerjaan_ayah']) ? mysqli_real_escape_string($conn, $_POST['pekerjaan_ayah']) : '';
    $alamat_ayah = isset($_POST['alamat_ayah']) ? mysqli_real_escape_string($conn, $_POST['alamat_ayah']) : '';
    
    // Data saksi
    $saksi1 = isset($_POST['saksi1']) ? mysqli_real_escape_string($conn, $_POST['saksi1']) : '';
    $saksi2 = isset($_POST['saksi2']) ? mysqli_real_escape_string($conn, $_POST['saksi2']) : '';
    
    // Generate kode unik dan nomor akta
    $kode_unik = strtoupper(substr(md5(uniqid()), 0, 8));
    $no_akta = generateNoAkta($conn);
    
    // INSERT KE PENGAJUAN (catatan_admin KOSONG)
    $query = "INSERT INTO pengajuan (id_penduduk, nik_warga, nama_warga, jenis_surat, tgl_pengajuan, status, kode_unik, catatan_admin) 
              VALUES (0, 'LAHIR-$kode_unik', '$nama_anak', 'Lahir', CURDATE(), 'Pending', '$kode_unik', '')";
    
    if(mysqli_query($conn, $query)) {
        $success = true;
    } else {
        $error = "Gagal menyimpan ke pengajuan: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Kelahiran - SILAMPID</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
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
        
        /* Readonly field style */
        input:read-only, textarea:read-only {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        
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
        <h1><i class="fas fa-baby"></i> Formulir Kelahiran</h1>
        <div class="subtitle">Form pencatatan kelahiran</div>
        
        <?php if($error): ?>
            <div class="alert-error"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" id="kelahiranForm">
            <!-- Data Anak -->
            <div class="form-section">
                <h3><i class="fas fa-child"></i> Data Anak / Bayi</h3>
                <div class="form-grid">
                    <div class="form-group full-width"><label>Nama Lengkap Anak *</label><input type="text" name="nama_anak" required></div>
                    <div class="form-group"><label>Tempat Lahir *</label><input type="text" name="tempat_lahir" required></div>
                    <div class="form-group"><label>Tanggal Lahir *</label><input type="date" name="tanggal_lahir" required></div>
                    <div class="form-group"><label>Jenis Kelamin *</label>
                        <select name="jenis_kelamin" required>
                            <option value="">Pilih</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Anak Ke- *</label><input type="number" name="anak_ke" min="1" required></div>
                    <div class="form-group"><label>Berat Bayi (kg)</label><input type="text" name="berat_bayi" placeholder="Contoh: 3.2"></div>
                    <div class="form-group"><label>Panjang Bayi (cm)</label><input type="text" name="panjang_bayi" placeholder="Contoh: 48"></div>
                </div>
            </div>
            
            <!-- Data Ibu (dengan auto-fill) -->
            <div class="form-section">
                <h3><i class="fas fa-female"></i> Data Ibu</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>NIK Ibu *</label>
                        <input type="text" id="nik_ibu" name="nik_ibu" maxlength="16" pattern="[0-9]{16}" required placeholder="16 digit NIK">
                        <small style="color:#27ae60; display:none; font-size:11px; margin-top:5px;" id="autoFillMsgIbu"></small>
                    </div>
                    <div class="form-group"><label>Nama Lengkap Ibu *</label><input type="text" id="nama_ibu" name="nama_ibu" required readonly></div>
                    <div class="form-group"><label>Tempat Lahir Ibu</label><input type="text" id="tempat_lahir_ibu" name="tempat_lahir_ibu" readonly></div>
                    <div class="form-group"><label>Tanggal Lahir Ibu</label><input type="date" id="tanggal_lahir_ibu" name="tanggal_lahir_ibu" readonly></div>
                    <div class="form-group"><label>Pekerjaan Ibu</label><input type="text" name="pekerjaan_ibu"></div>
                    <div class="form-group"><label>Alamat Ibu</label><textarea id="alamat_ibu" name="alamat_ibu" rows="2" readonly></textarea></div>
                </div>
            </div>
            
            <!-- Data Ayah (dengan auto-fill) -->
            <div class="form-section">
                <h3><i class="fas fa-male"></i> Data Ayah</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>NIK Ayah *</label>
                        <input type="text" id="nik_ayah" name="nik_ayah" maxlength="16" pattern="[0-9]{16}" required placeholder="16 digit NIK">
                        <small style="color:#27ae60; display:none; font-size:11px; margin-top:5px;" id="autoFillMsgAyah"></small>
                    </div>
                    <div class="form-group"><label>Nama Lengkap Ayah *</label><input type="text" id="nama_ayah" name="nama_ayah" required readonly></div>
                    <div class="form-group"><label>Tempat Lahir Ayah</label><input type="text" id="tempat_lahir_ayah" name="tempat_lahir_ayah" readonly></div>
                    <div class="form-group"><label>Tanggal Lahir Ayah</label><input type="date" id="tanggal_lahir_ayah" name="tanggal_lahir_ayah" readonly></div>
                    <div class="form-group"><label>Pekerjaan Ayah</label><input type="text" name="pekerjaan_ayah"></div>
                    <div class="form-group"><label>Alamat Ayah</label><textarea id="alamat_ayah" name="alamat_ayah" rows="2" readonly></textarea></div>
                </div>
            </div>
            
            <!-- Data Saksi -->
            <div class="form-section">
                <h3><i class="fas fa-users"></i> Saksi</h3>
                <div class="form-grid">
                    <div class="form-group"><label>Saksi 1</label><input type="text" name="saksi1" placeholder="Nama saksi 1"></div>
                    <div class="form-group"><label>Saksi 2</label><input type="text" name="saksi2" placeholder="Nama saksi 2"></div>
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
            <p>Data kelahiran telah berhasil disimpan.</p>
            <div class="kode-unik">
                <i class="fas fa-qrcode"></i> <?php echo $kode_unik; ?>
            </div>
            <p><strong>Nomor Akta:</strong> <?php echo $no_akta; ?></p>
            <div class="info">
                <i class="fas fa-info-circle"></i> Simpan Kode Unik ini untuk mengecek status pengajuan Anda.
            </div>
            <button onclick="closeAlert()"><i class="fas fa-check"></i> OK</button>
        </div>
    </div>
    
    <script>
        // Auto-fill untuk NIK Ibu
        document.getElementById('nik_ibu').addEventListener('blur', function() {
            var nik = this.value.trim();
            var msgDiv = document.getElementById('autoFillMsgIbu');
            
            if (nik.length === 16) {
                msgDiv.style.display = 'block';
                msgDiv.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Mencari data...';
                msgDiv.style.color = '#27ae60';
                
                fetch('ajax_get_penduduk.php?nik=' + encodeURIComponent(nik))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('nama_ibu').value = data.nama_lengkap || '';
                            document.getElementById('tempat_lahir_ibu').value = data.tempat_lahir || '';
                            document.getElementById('tanggal_lahir_ibu').value = data.tanggal_lahir || '';
                            document.getElementById('alamat_ibu').value = data.alamat || '';
                            
                            msgDiv.innerHTML = '<i class="fas fa-check-circle"></i> Data ibu otomatis terisi!';
                            msgDiv.style.color = '#27ae60';
                            setTimeout(function() {
                                msgDiv.style.display = 'none';
                            }, 2000);
                        } else {
                            document.getElementById('nama_ibu').value = '';
                            document.getElementById('tempat_lahir_ibu').value = '';
                            document.getElementById('tanggal_lahir_ibu').value = '';
                            document.getElementById('alamat_ibu').value = '';
                            
                            msgDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + data.message;
                            msgDiv.style.color = '#dc2626';
                            setTimeout(function() {
                                msgDiv.style.display = 'none';
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        msgDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Gagal mengambil data';
                        msgDiv.style.color = '#dc2626';
                        setTimeout(function() {
                            msgDiv.style.display = 'none';
                        }, 3000);
                    });
            } else if (nik.length > 0 && nik.length !== 16) {
                msgDiv.style.display = 'block';
                msgDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> NIK harus 16 digit';
                msgDiv.style.color = '#dc2626';
                setTimeout(function() {
                    msgDiv.style.display = 'none';
                }, 3000);
            }
        });
        
        // Auto-fill untuk NIK Ayah
        document.getElementById('nik_ayah').addEventListener('blur', function() {
            var nik = this.value.trim();
            var msgDiv = document.getElementById('autoFillMsgAyah');
            
            if (nik.length === 16) {
                msgDiv.style.display = 'block';
                msgDiv.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Mencari data...';
                msgDiv.style.color = '#27ae60';
                
                fetch('ajax_get_penduduk.php?nik=' + encodeURIComponent(nik))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('nama_ayah').value = data.nama_lengkap || '';
                            document.getElementById('tempat_lahir_ayah').value = data.tempat_lahir || '';
                            document.getElementById('tanggal_lahir_ayah').value = data.tanggal_lahir || '';
                            document.getElementById('alamat_ayah').value = data.alamat || '';
                            
                            msgDiv.innerHTML = '<i class="fas fa-check-circle"></i> Data ayah otomatis terisi!';
                            msgDiv.style.color = '#27ae60';
                            setTimeout(function() {
                                msgDiv.style.display = 'none';
                            }, 2000);
                        } else {
                            document.getElementById('nama_ayah').value = '';
                            document.getElementById('tempat_lahir_ayah').value = '';
                            document.getElementById('tanggal_lahir_ayah').value = '';
                            document.getElementById('alamat_ayah').value = '';
                            
                            msgDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + data.message;
                            msgDiv.style.color = '#dc2626';
                            setTimeout(function() {
                                msgDiv.style.display = 'none';
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        msgDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Gagal mengambil data';
                        msgDiv.style.color = '#dc2626';
                        setTimeout(function() {
                            msgDiv.style.display = 'none';
                        }, 3000);
                    });
            } else if (nik.length > 0 && nik.length !== 16) {
                msgDiv.style.display = 'block';
                msgDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> NIK harus 16 digit';
                msgDiv.style.color = '#dc2626';
                setTimeout(function() {
                    msgDiv.style.display = 'none';
                }, 3000);
            }
        });
    </script>
    
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