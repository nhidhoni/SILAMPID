<?php
// File: admin/penduduk_tambah.php - Form Tambah Penduduk (Dengan Anak Ke-)
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: index.php?error=2");
    exit();
}
include '../koneksi.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $no_kk = mysqli_real_escape_string($conn, $_POST['no_kk']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $rt = mysqli_real_escape_string($conn, $_POST['rt']);
    $rw = mysqli_real_escape_string($conn, $_POST['rw']);
    $desa_kelurahan = mysqli_real_escape_string($conn, $_POST['desa_kelurahan']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $kabupaten_kota = mysqli_real_escape_string($conn, $_POST['kabupaten_kota']);
    $status_hubungan = mysqli_real_escape_string($conn, $_POST['status_hubungan']);
    $anak_ke = isset($_POST['anak_ke']) && $_POST['anak_ke'] !== '' ? mysqli_real_escape_string($conn, $_POST['anak_ke']) : null;
    
    if(strlen($nik) != 16) {
        $error = "NIK harus 16 digit!";
    } else {
        $cek = mysqli_query($conn, "SELECT id FROM penduduk WHERE nik='$nik'");
        if(mysqli_num_rows($cek) > 0) {
            $error = "NIK sudah terdaftar!";
        } else {
            $anak_ke_sql = $anak_ke ? "'$anak_ke'" : "NULL";
            $query = "INSERT INTO penduduk (nik, no_kk, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, rt, rw, desa_kelurahan, kecamatan, kabupaten_kota, status_hubungan, anak_ke) 
                      VALUES ('$nik', '$no_kk', '$nama_lengkap', '$tempat_lahir', '$tanggal_lahir', '$jenis_kelamin', '$alamat', '$rt', '$rw', '$desa_kelurahan', '$kecamatan', '$kabupaten_kota', '$status_hubungan', $anak_ke_sql)";
            
            if(mysqli_query($conn, $query)) {
                header("Location: data_penduduk.php?success=tambah");
                exit();
            } else {
                $error = "Gagal: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Penduduk - SILAMPID</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        h1 { color: #2c3e50; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .btn { padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .btn-primary { background: #3498db; color: white; }
        .btn-secondary { background: #95a5a6; color: white; text-decoration: none; display: inline-block; }
        .alert { background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .anak-ke-field { display: none; }
        .anak-ke-field.show { display: block; }
        @media (max-width:768px){ .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-user-plus"></i> Tambah Data Penduduk</h1>
        <?php if($error): ?><div class="alert"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST" id="formPenduduk">
            <div class="form-row">
                <div class="form-group"><label>NIK * (16 digit)</label><input type="text" name="nik" maxlength="16" pattern="[0-9]{16}" required></div>
                <div class="form-group"><label>No KK *</label><input type="text" name="no_kk" maxlength="16" required></div>
            </div>
            <div class="form-group"><label>Nama Lengkap *</label><input type="text" name="nama_lengkap" required></div>
            <div class="form-row">
                <div class="form-group"><label>Tempat Lahir</label><input type="text" name="tempat_lahir"></div>
                <div class="form-group"><label>Tanggal Lahir</label><input type="date" name="tanggal_lahir"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Jenis Kelamin</label>
                    <select name="jenis_kelamin">
                        <option value="">Pilih</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="form-group"><label>Status Hubungan</label>
                    <select name="status_hubungan" id="status_hubungan">
                        <option value="Kepala Keluarga">Kepala Keluarga</option>
                        <option value="Istri">Istri</option>
                        <option value="Anak">Anak</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
            <div class="form-group anak-ke-field" id="anak_ke_field">
                <label>Anak Ke-</label>
                <input type="number" name="anak_ke" min="1" placeholder="Contoh: 1">
            </div>
            <div class="form-group"><label>Alamat</label><textarea name="alamat" rows="2"></textarea></div>
            <div class="form-row">
                <div class="form-group"><label>RT</label><input type="text" name="rt"></div>
                <div class="form-group"><label>RW</label><input type="text" name="rw"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Desa/Kelurahan</label><input type="text" name="desa_kelurahan"></div>
                <div class="form-group"><label>Kecamatan</label><input type="text" name="kecamatan"></div>
            </div>
            <div class="form-group"><label>Kabupaten/Kota</label><input type="text" name="kabupaten_kota" value="Pakpak Bharat"></div>
            <div style="display:flex; gap:10px; margin-top:20px;"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="data_penduduk.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a></div>
        </form>
    </div>
    <script>
        const statusSelect = document.getElementById('status_hubungan');
        const anakKeField = document.getElementById('anak_ke_field');
        function toggleAnakKeField() {
            if (statusSelect.value === 'Anak') {
                anakKeField.classList.add('show');
            } else {
                anakKeField.classList.remove('show');
            }
        }
        statusSelect.addEventListener('change', toggleAnakKeField);
        toggleAnakKeField();
    </script>
</body>
</html>