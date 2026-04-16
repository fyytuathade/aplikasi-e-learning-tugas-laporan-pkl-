<?php
session_start();
include 'includes/koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

 $user_id = $_SESSION['id'];
 $pesan = "";

// --- LOGIKA 1: UPDATE DATA & PASSWORD ---
if (isset($_POST['update_profil'])) {
    $nama_baru = $_POST['nama_lengkap'];
    $pass_lama = $_POST['password_lama'];
    $pass_baru = $_POST['password_baru'];

    // Validasi Password Lama
    $cek_pass = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id' AND password = '$pass_lama'");
    
    if (mysqli_num_rows($cek_pass) > 0) {
        $query_update = "UPDATE users SET nama_lengkap = '$nama_baru'";
        if (!empty($pass_baru)) {
            $query_update .= ", password = '$pass_baru'";
        }
        $query_update .= " WHERE id = '$user_id'";
        
        if (mysqli_query($conn, $query_update)) {
            $_SESSION['sukses'] = "Data diri berhasil diperbarui!";
            $_SESSION['nama'] = $nama_baru;
            header("Location: profil.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Password lama salah!";
        header("Location: profil.php");
        exit;
    }
}

// --- LOGIKA 2: UPLOAD FOTO ---
if (isset($_POST['upload_foto_internal'])) {
    $namaFile = $_FILES['foto']['name'];
    $tmpName = $_FILES['foto']['tmp_name'];
    $error = $_FILES['foto']['error'];

    if ($error === 4) {
        $pesan = "<div class='alert alert-warning'>Pilih gambar terlebih dahulu!</div>";
    } else {
        $ekstensiGambarValid = ['jpg', 'jpeg', 'png', 'gif'];
        $ekstensiGambar = explode('.', $namaFile);
        $ekstensiGambar = strtolower(end($ekstensiGambar));

        if (in_array($ekstensiGambar, $ekstensiGambarValid)) {
            $namaBaru = 'user_' . $user_id . '_' . time() . '.' . $ekstensiGambar;
            $folderTujuan = __DIR__ . '/uploads/'; // __DIR__ = folder tempat file ini berada
            
            // Paksa buat folder jika tidak ada
            if (!is_dir($folderTujuan)) {
                mkdir($folderTujuan, 0777, true);
            }

            if (move_uploaded_file($tmpName, $folderTujuan . $namaBaru)) {
                $query = mysqli_query($conn, "UPDATE users SET foto = '$namaBaru' WHERE id = '$user_id'");
                if ($query) {
                    $_SESSION['sukses'] = "Foto profil berhasil diupload!";
                    header("Location: profil.php");
                    exit;
                } else {
                    $pesan = "<div class='alert alert-danger'>Database error.</div>";
                }
            } else {
                $pesan = "<div class='alert alert-danger'>Gagal upload file.</div>";
            }
        } else {
            $pesan = "<div class='alert alert-danger'>File harus berupa gambar!</div>";
        }
    }
}

// Ambil Data User
 $data_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
 $user = mysqli_fetch_assoc($data_user);

// Definisikan variabel $src_foto untuk layout
if (!empty($user['foto']) && file_exists('uploads/' . $user['foto'])) {
    $src_foto = 'uploads/' . $user['foto'];
} else {
    $src_foto = ""; // Kosong agar layout pakai inisial
}

// Sertakan Layout
include 'includes/main_layout.php';
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <h3 class="fw-bold text-center mb-4">Profil Saya</h3>
                    <?= $pesan; ?>

                    <!-- TAMPILAN FOTO PROFIL -->
                    <div class="text-center mb-5">
                        <?php if (!empty($user['foto']) && file_exists('uploads/' . $user['foto'])): ?>
                            <img src="uploads/<?= $user['foto']; ?>" class="rounded-circle border border-4 border-white shadow" width="150" height="150" style="object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white shadow mx-auto" style="width: 150px; height: 150px; font-size: 3rem; font-weight: bold;">
                                <?= substr($user['nama_lengkap'], 0, 1); ?>
                            </div>
                        <?php endif; ?>
                        <br/>
                        <!-- FORM UPLOAD INTERNAL -->
                        <form method="post" enctype="multipart/form-data" class="mt-3 d-inline-block">
                            <label for="fileInput" class="btn btn-outline-primary btn-sm cursor-pointer">
                                <i class="bi bi-camera-fill"></i> Upload Foto
                            </label>
                            <input type="file" name="foto" id="fileInput" accept="image/*" style="display: none;" onchange="this.form.submit()">
                            <input type="hidden" name="upload_foto_internal" value="1">
                        </form>
                    </div>

                    <hr class="my-4">

                    <!-- FORM EDIT DATA -->
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Username</label>
                                <input type="text" class="form-control" value="<?= $user['username']; ?>" disabled style="background-color: #f8f9fa; color: #6c757d;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Role</label>
                                <input type="text" class="form-control text-uppercase fw-bold" value="<?= $user['role']; ?>" disabled style="background-color: #f8f9fa; color: #6c757d;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control form-control-lg" value="<?= $user['nama_lengkap']; ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Password Lama</label>
                                <input type="password" name="password_lama" class="form-control" placeholder="Wajib diisi untuk simpan" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Password Baru (Opsional)</label>
                                <input type="password" name="password_baru" class="form-control" placeholder="Kosongkan jika tidak diganti">
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="update_profil" class="btn btn-primary btn-lg">Simpan Perubahan</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/layout_footer.php'; ?>