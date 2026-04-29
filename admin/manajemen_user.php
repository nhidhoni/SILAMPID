<?php
// File: admin/manajemen_user.php - Manajemen User (Tampilan Selaras)
session_start();
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: index.php?error=2");
    exit();
}
if($_SESSION['role'] != 'admin') {
    header("Location: dashboard_admin.php");
    exit();
}
include '../koneksi.php';

if(isset($_POST['tambah'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if(mysqli_num_rows($cek) > 0) { $error = "Username sudah ada!"; }
    else { $insert = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama_lengkap', '$role')"; if(mysqli_query($conn, $insert)) { $success = "User berhasil ditambahkan!"; } else { $error = "Gagal menambahkan user!"; } }
}
if(isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $update = "UPDATE users SET username='$username', nama_lengkap='$nama_lengkap', role='$role' WHERE id='$id'";
    if(mysqli_query($conn, $update)) { $success = "User berhasil diupdate!"; } else { $error = "Gagal mengupdate user!"; }
}
if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if($id == $_SESSION['user_id']) { $error = "Tidak bisa menghapus akun sendiri!"; }
    else { $delete = "DELETE FROM users WHERE id='$id'"; if(mysqli_query($conn, $delete)) { $success = "User berhasil dihapus!"; } else { $error = "Gagal menghapus user!"; } }
}
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User - SILAMPID</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        h1 { color: #2c3e50; margin-bottom: 20px; font-size: 24px; display: flex; align-items: center; gap: 10px; }
        
        /* Action Bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
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
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #219a52; }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-warning:hover { background: #e67e22; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn-secondary:hover { background: #7f8c8d; }
        
        /* Tabel */
        .table-container { overflow-x: auto; margin: 15px 0; border: 1px solid #ecf0f1; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        th { background: #2c3e50; color: white; font-weight: 600; white-space: nowrap; }
        td { vertical-align: middle; word-break: break-word; }
        tr:hover { background: #f8f9fa; }
        
        th:nth-child(1), td:nth-child(1) { width: 50px; text-align: center; }
        th:nth-child(2), td:nth-child(2) { width: 150px; }
        th:nth-child(3), td:nth-child(3) { min-width: 180px; }
        th:nth-child(4), td:nth-child(4) { width: 100px; }
        th:nth-child(5), td:nth-child(5) { width: 150px; }
        
        .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
        .action-btn { padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 11px; display: inline-flex; align-items: center; gap: 3px; }
        .action-btn.edit { background: #f39c12; color: white; }
        .action-btn.edit:hover { background: #e67e22; }
        .action-btn.delete { background: #e74c3c; color: white; }
        .action-btn.delete:hover { background: #c0392b; }
        
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
            width: 400px;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ecf0f1;
        }
        .close { cursor: pointer; font-size: 24px; color: #7f8c8d; }
        .close:hover { color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; font-size: 13px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        
        /* Alert */
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #059669; border-left: 4px solid #059669; }
        .alert-danger { background: #fee2e2; color: #dc2626; border-left: 4px solid #dc2626; }
        
        .info-bar { 
            display: flex; 
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 15px; 
            flex-wrap: wrap; 
            gap: 15px;
        }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-admin { background: #3498db; color: white; }
        .badge-operator { background: #27ae60; color: white; }
        
        @media (max-width: 768px) { 
            .table-container { overflow-x: auto; }
            th, td { font-size: 12px; padding: 8px; }
            .action-buttons { flex-direction: column; }
            .modal-content { width: 90%; }
            .action-bar { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-users-gear"></i> Manajemen User</h1>
        
        <!-- Action Bar -->
        <div class="action-bar">
            <button class="btn btn-success" onclick="showTambahModal()"><i class="fas fa-plus"></i> Tambah User</button>
            <a href="dashboard_admin.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="info-bar">
            <div class="total-data" style="background: #e8f4f8; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; color: #2980b9;">
                <i class="fas fa-database"></i> Total: <?php echo mysqli_num_rows($result); ?> user
            </div>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td><span class="badge <?php echo $row['role']=='admin'?'badge-admin':'badge-operator'; ?>"><?php echo $row['role']; ?></span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn edit" onclick="showEditModal(<?php echo $row['id']; ?>, '<?php echo $row['username']; ?>', '<?php echo addslashes($row['nama_lengkap']); ?>', '<?php echo $row['role']; ?>')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <?php if($row['id'] != $_SESSION['user_id']): ?>
                                <a href="?hapus=<?php echo $row['id']; ?>" class="action-btn delete" onclick="return confirm('Yakin hapus user <?php echo $row['username']; ?>?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal Tambah User -->
    <div id="tambahModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-plus"></i> Tambah User</h3>
                <span class="close" onclick="closeModal('tambahModal')">&times;</span>
            </div>
            <form method="POST">
                <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" required></div>
                <div class="form-group"><label>Role</label>
                    <select name="role">
                        <option value="operator">Operator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('tambahModal')">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Edit User -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-edit"></i> Edit User</h3>
                <span class="close" onclick="closeModal('editModal')">&times;</span>
            </div>
            <form method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group"><label>Username</label><input type="text" name="username" id="edit_username" required></div>
                <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" id="edit_nama" required></div>
                <div class="form-group"><label>Role</label>
                    <select name="role" id="edit_role">
                        <option value="operator">Operator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Batal</button>
                    <button type="submit" name="edit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showTambahModal() { document.getElementById('tambahModal').style.display = 'flex'; }
        function showEditModal(id, username, nama, role) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_role').value = role;
            document.getElementById('editModal').style.display = 'flex';
        }
        function closeModal(modalId) { document.getElementById(modalId).style.display = 'none'; }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>