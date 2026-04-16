<?php
session_start();
include '../includes/koneksi.php';
include '../includes/admin_layout.php';

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: manajemen_user.php");
    exit;
}
?>

<div class="card border-0 shadow-sm col-md-6 mx-auto">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">Edit Data Pengguna</h5>
    </div>
    <div class="card-body">
        <form action="proses_edit_user.php" method="POST">
            <input type="hidden" name="id" value="<?= $data['id']; ?>">
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" value="<?= $data['nama_lengkap']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" value="<?= $data['username']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Role</label>
                <select name="role" class="form-select">
                    <option value="siswa" <?= $data['role'] == 'siswa' ? 'selected' : ''; ?>>Siswa</option>
                    <option value="guru" <?= $data['role'] == 'guru' ? 'selected' : ''; ?>>Guru</option>
                    <option value="admin" <?= $data['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <div class="alert alert-info small">
                <i class="bi bi-info-circle me-2"></i> Kosongkan password jika tidak ingin diubah.
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Password Baru (Opsional)</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="d-flex justify-content-between">
                <a href="manajemen_user.php" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/layout_footer.php'; ?>