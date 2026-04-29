<?php
// File: admin/cek_login.php
session_start();
include '../koneksi.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = md5($_POST['password']);

$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $_SESSION['login'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['role'] = $user['role'];
    header("Location: dashboard_admin.php");
} else {
    header("Location: index.php?error=1");
}
?>