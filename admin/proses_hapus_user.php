<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../includes/koneksi.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Cegah admin menghapus dirinya sendiri
    if ($id == $_SESSION['id']) {
        $_SESSION['error'] = "Gagal! Anda tidak bisa menghapus akun sendiri.";
    } else {
        $query = mysqli_query($conn, "DELETE FROM users WHERE id = '$id'");
        if ($query) {
            $_SESSION['sukses'] = "Data user berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus data dari database.";
        }
    }
}

header("Location: manajemen_user.php");
exit;