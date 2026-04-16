<?php
session_start();
// Proteksi: Hanya admin yang boleh menjalankan script ini
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form dan bersihkan (Security: Prevent SQL Injection)
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username     = mysqli_real_escape_string($conn, $_POST['username']);
    $password     = $_POST['password'];
    $role         = mysqli_real_escape_string($conn, $_POST['role']);

    // 1. Cek apakah username sudah ada di database
    $cek_username = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($cek_username) > 0) {
        // Jika username sudah dipakai
        $_SESSION['error'] = "Gagal! Username sudah terdaftar di sistem.";
        header("Location: manajemen_user.php");
        exit;
    } else {
        // 2. Hash password sebelum disimpan (Keamanan Standar RPL)
        $password_aman = password_hash($password, PASSWORD_BCRYPT);

        // 3. Insert ke database
        $query = "INSERT INTO users (nama_lengkap, username, password, role) 
                  VALUES ('$nama_lengkap', '$username', '$password_aman', '$role')";
        
        if (mysqli_query($conn, $query)) {
            // Berhasil: Set session sukses untuk SweetAlert2 di layout
            $_SESSION['sukses'] = "User $nama_lengkap berhasil ditambahkan!";
            header("Location: manajemen_user.php");
            exit;
        } else {
            // Gagal Query
            $_SESSION['error'] = "Terjadi kesalahan sistem saat menyimpan data.";
            header("Location: manajemen_user.php");
            exit;
        }
    }
} else {
    // Jika akses file ini tanpa melalui form (Direct Access)
    header("Location: manajemen_user.php");
    exit;
}