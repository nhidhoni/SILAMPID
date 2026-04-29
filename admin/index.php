<?php
// File: admin/index.php
session_start();

if(isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: dashboard_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SILAMPID</title>
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
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        /* Navbar */
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
            gap: 20px;
            flex-wrap: wrap;
        }
        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .nav-menu a:hover {
            background: #3498db;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        /* Login Container */
        .login-container {
            background: white;
            padding: 30px 35px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            width: 380px;
            text-align: center;
        }
        .login-icon {
            font-size: 56px;
            color: #3498db;
            margin-bottom: 15px;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 24px;
        }
        .subtitle {
            color: #7f8c8d;
            margin-bottom: 25px;
            font-size: 13px;
        }
        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #555;
            font-size: 13px;
        }
        input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
        }
        button {
            width: 100%;
            padding: 10px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 5px;
        }
        button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 18px;
            text-align: center;
            font-size: 13px;
        }
        .info {
            text-align: center;
            margin-top: 18px;
            font-size: 11px;
            color: #888;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 12px;
            background: white;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 11px;
            flex-shrink: 0;
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 10px;
                text-align: center;
                padding: 10px 20px;
            }
            .login-container {
                width: 90%;
                padding: 25px 20px;
            }
            .login-icon {
                font-size: 48px;
            }
            h2 {
                font-size: 20px;
            }
        }
        
        @media (max-height: 600px) {
            .login-container {
                padding: 20px 25px;
            }
            .login-icon {
                font-size: 40px;
                margin-bottom: 10px;
            }
            .subtitle {
                margin-bottom: 15px;
            }
            .form-group {
                margin-bottom: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-id-card"></i>
            <span>SILAMPID</span>
        </div>
        <div class="nav-menu">
            <a href="../warga/beranda.php">🏠 Beranda</a>
        </div>
    </nav>

    <div class="main-content">
        <div class="login-container">
            <div class="login-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <h2>Login Admin</h2>
            <div class="subtitle">Dinas Kependudukan & Pencatatan Sipil<br>Kabupaten Pakpak Bharat</div>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="error">
                    <?php 
                        if($_GET['error'] == 1) echo "<i class='fas fa-exclamation-triangle'></i> Username atau password salah!";
                        if($_GET['error'] == 2) echo "<i class='fas fa-exclamation-triangle'></i> Silakan login terlebih dahulu!";
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="cek_login.php">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required autofocus>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-key"></i> Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit"><i class="fas fa-sign-in-alt"></i> Login</button>
            </form>
            
            <div class="info">
                <i class="fas fa-info-circle"></i> Demo: username <strong>admin</strong> | password <strong>admin123</strong>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 SILAMPID - Dinas Dukcapil Kabupaten Pakpak Bharat</p>
    </div>
</body>
</html>