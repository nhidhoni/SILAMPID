<?php
// File: admin/ganti_password.php - Ganti Password (Tanpa Tombol Kembali)
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: index.php?error=2");
    exit();
}
include '../koneksi.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password_lama = md5($_POST['password_lama']);
    $password_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi'];
    $user_id = $_SESSION['user_id'];
    
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM users WHERE id='$user_id'"));
    
    if($user['password'] != $password_lama) {
        $error = "Password lama salah!";
    } elseif(strlen($password_baru) < 4) {
        $error = "Password baru minimal 4 karakter!";
    } elseif($password_baru != $konfirmasi) {
        $error = "Konfirmasi password tidak sesuai!";
    } else {
        $password_baru_hash = md5($password_baru);
        if(mysqli_query($conn, "UPDATE users SET password='$password_baru_hash' WHERE id='$user_id'")) {
            session_destroy();
            echo "<script>alert('Password berhasil diubah! Silakan login kembali.'); window.location.href='index.php';</script>";
            exit();
        } else {
            $error = "Gagal mengubah password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - SILAMPID</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f7fa; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container { 
            max-width: 450px; 
            width: 100%;
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
        }
        h1 { 
            color: #2c3e50; 
            text-align: center; 
            margin-bottom: 10px; 
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .subtitle { 
            text-align: center; 
            color: #7f8c8d; 
            margin-bottom: 25px; 
            padding-bottom: 15px; 
            border-bottom: 2px solid #ecf0f1; 
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
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn-secondary:hover { background: #7f8c8d; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #219a52; }
        
        /* Form */
        .form-group { margin-bottom: 20px; }
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: bold; 
            color: #555; 
            font-size: 13px;
        }
        .form-group input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-group input:focus { 
            outline: none; 
            border-color: #3498db; 
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
        }
        
        /* Alert */
        .alert { 
            padding: 12px 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-danger { 
            background: #fee2e2; 
            color: #dc2626; 
            border-left: 4px solid #dc2626; 
        }
        
        /* Info Card */
        .info-card {
            background: #e8f4f8;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2980b9;
            font-size: 13px;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        .button-group .btn {
            flex: 1;
            justify-content: center;
        }
        
        @media (max-width: 768px) { 
            .container { padding: 20px; }
            .button-group { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-key"></i> Ganti Password</h1>
        <div class="subtitle">
            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['nama_lengkap']; ?> (<?php echo $_SESSION['role']; ?>)
        </div>
        
        <!-- Info Card -->
        <div class="info-card">
            <i class="fas fa-info-circle"></i> 
            Password baru minimal 4 karakter. Setelah diubah, Anda akan logout dan harus login kembali.
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password Lama</label>
                <input type="password" name="password_lama" required placeholder="Masukkan password lama">
            </div>
            <div class="form-group">
                <label><i class="fas fa-key"></i> Password Baru</label>
                <input type="password" name="password_baru" required placeholder="Minimal 4 karakter">
            </div>
            <div class="form-group">
                <label><i class="fas fa-check-circle"></i> Konfirmasi Password Baru</label>
                <input type="password" name="konfirmasi" required placeholder="Ketik ulang password baru">
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Ganti Password</button>
                <a href="dashboard_admin.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</body>
</html>