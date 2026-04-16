<?php
session_start();
include '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $pass = $_POST['password'];

    // Jika password diisi, maka update dengan password baru (hash)
    if (!empty($pass)) {
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nama_lengkap='$nama', username='$user', role='$role', password='$pass_hash' WHERE id='$id'";
    } else {
        // Jika password kosong, jangan update kolom password
        $sql = "UPDATE users SET nama_lengkap='$nama', username='$user', role='$role' WHERE id='$id'";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['sukses'] = "Data $nama berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data.";
    }
}

header("Location: manajemen_user.php");
exit;