<?php
// File: admin/form_pengajuan.php
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: index.php?error=2");
    exit();
}
include '../koneksi.php';

$success = false;
$error = '';
$kode_unik = '';
$data_penduduk = null;

if(isset($_POST['cari_nik'])) {
    $nik_cari = mysqli_real_escape_string($conn, $_POST['nik']);
    $query = "SELECT * FROM penduduk WHERE nik = '$nik_cari'";
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0) {
        $data_penduduk = mysqli_fetch_assoc($result);
    } else {
        $error = "NIK tidak ditemukan. Silakan input data penduduk terlebih dahulu.";
    }
}

if(isset($_POST['submit'])) {
    $id_penduduk = $_POST['id_penduduk'];
    $nik_warga = $_POST['nik_warga'];
    $nama_warga = $_POST['nama_warga'];
    $jenis_surat = $_POST['jenis_surat'];
    $tgl_pengajuan = date('Y-m-d');
    $catatan_warga = mysqli_real_escape_string($conn, $_POST['catatan_warga']);
    
    $kode_unik = strtoupper(substr(md5(uniqid()), 0, 8));
    
    $query = "INSERT INTO pengajuan (id_penduduk, nik_warga, nama_warga, jenis_surat, tgl_pengajuan, status, kode_unik, catatan_admin) 
              VALUES ('$id_penduduk', '$nik_warga', '$nama_warga', '$jenis_surat', '$tgl_pengajuan', 'Pending', '$kode_unik', '$catatan_warga')";
    
    if(mysqli_query($conn, $query)) {
        $success = true;
    } else {
        $error = "Gagal menyimpan: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan Surat - SILAMPID</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; }
        h1 { color: #2c3e50; text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        button { background: #27ae60; color: white; padding: 12px; border: none; border-radius: 8px; width: 100%; cursor: pointer; font-size: 16px; }
        .success { background: #d1fae5; color: #059669; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .error { background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 5px; margin-top: 15px; text-align: center; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Form Pengajuan Surat</h1>
        <?php if($success): ?>
            <div class="success">✅ Pengajuan berhasil!<br><strong>Kode Unik Anda:</strong> <?php echo $kode_unik; ?><br>Simpan kode ini untuk mengecek status.</div>
            <div class="info">🔍 Cek status pengajuan: <a href="cek_status.php">Klik di sini</a></div>
        <?php endif; ?>
        <?php if($error): ?><div class="error">❌ <?php echo $error; ?></div><?php endif; ?>
        
        <?php if(!$success): ?>
            <form method="POST"><div class="form-group"><label>🔍 Masukkan NIK Anda</label><input type="text" name="nik" placeholder="16 digit NIK" maxlength="16" required></div>
            <button type="submit" name="cari_nik">Cari Data</button></form>
            
            <?php if($data_penduduk): ?>
                <hr style="margin:20px 0">
                <form method="POST">
                    <input type="hidden" name="id_penduduk" value="<?php echo $data_penduduk['id']; ?>">
                    <input type="hidden" name="nik_warga" value="<?php echo $data_penduduk['nik']; ?>">
                    <div class="form-group"><label>NIK</label><input type="text" value="<?php echo $data_penduduk['nik']; ?>" readonly></div>
                    <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama_warga" value="<?php echo $data_penduduk['nama_lengkap']; ?>" readonly></div>
                    <div class="form-group"><label>Jenis Surat *</label><select name="jenis_surat" required><option value="">-- Pilih --</option><option value="Lahir">Akta Kelahiran</option><option value="Kawin">Akta Perkawinan</option><option value="Kematian">Akta Kematian</option><option value="Pindah Datang">Surat Pindah Datang</option><option value="Pindah Keluar">Surat Pindah Keluar</option></select></div>
                    <div class="form-group"><label>Catatan / Keterangan</label><textarea name="catatan_warga" rows="3" placeholder="Contoh: untuk keperluan sekolah, kerja, dll"></textarea></div>
                    <button type="submit" name="submit">Kirim Pengajuan</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
        <a href="dashboard_admin.php" class="back-link">← Kembali ke Dashboard</a>
    </div>
</body>
</html>