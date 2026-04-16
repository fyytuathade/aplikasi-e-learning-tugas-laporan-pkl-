<?php
session_start();
// Proteksi: Hanya admin yang bisa akses
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../includes/koneksi.php';
include '../includes/admin_layout.php'; 

// Penampung Modal agar tidak menyebabkan flickering jika ditaruh di dalam table
$modal_edit_list = ""; 
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Manajemen User</h2>
        <p class="text-muted small mb-0">Kelola data Akun Siswa, Guru, dan Admin sistem.</p>
    </div>
    <button type="button" class="btn btn-primary shadow-sm mt-3 mt-md-0" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
        <i class="bi bi-person-plus-fill me-2"></i> Tambah User Baru
    </button>
</div>

<div class="row g-3 mb-4">
    <?php
    $count_user = mysqli_query($conn, "SELECT role, COUNT(*) as total FROM users GROUP BY role");
    while($c = mysqli_fetch_assoc($count_user)):
        $color = ($c['role'] == 'admin') ? 'danger' : (($c['role'] == 'guru') ? 'success' : 'primary');
    ?>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="badge bg-<?= $color; ?> bg-opacity-10 text-<?= $color; ?> p-3 rounded-3 me-3">
                    <i class="bi bi-person-badge fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0 small text-uppercase"><?= $c['role']; ?></h6>
                    <h4 class="fw-bold mb-0"><?= $c['total']; ?> User</h4>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small">USER</th>
                        <th class="py-3 text-muted small">ROLE</th>
                        <th class="py-3 text-muted small">USERNAME</th>
                        <th class="py-3 text-muted small text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, nama_lengkap ASC");
                    if(mysqli_num_rows($query) == 0):
                    ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada data user.</td></tr>
                    <?php
                    else:
                        while($row = mysqli_fetch_assoc($query)):
                            $badge_color = ($row['role'] == 'admin') ? 'danger' : (($row['role'] == 'guru') ? 'success' : 'primary');
                            
                            // Menghasilkan Modal HTML untuk setiap user secara terpisah
                            $modal_edit_list .= '
                            <div class="modal fade" id="modalEditUser'.$row['id'].'" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold">Edit User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="proses_edit_user.php" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="'.$row['id'].'">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Nama Lengkap</label>
                                                    <input type="text" name="nama_lengkap" class="form-control" value="'.$row['nama_lengkap'].'" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Username</label>
                                                    <input type="text" name="username" class="form-control" value="'.$row['username'].'" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Role</label>
                                                    <select name="role" class="form-select">
                                                        <option value="siswa" '.($row['role'] == 'siswa' ? 'selected' : '').'>Siswa</option>
                                                        <option value="guru" '.($row['role'] == 'guru' ? 'selected' : '').'>Guru</option>
                                                        <option value="admin" '.($row['role'] == 'admin' ? 'selected' : '').'>Admin</option>
                                                    </select>
                                                </div>
                                                <div class="bg-light p-3 rounded-3">
                                                    <label class="form-label small fw-bold mb-1">Ganti Password</label>
                                                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin diubah">
                                                    <small class="text-muted" style="font-size: 0.7rem;">Gunakan password minimal 8 karakter untuk keamanan.</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0 pt-0">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>';
                    ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark"><?= $row['nama_lengkap']; ?></div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">ID: #<?= $row['id']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-<?= $badge_color; ?> bg-opacity-10 text-<?= $badge_color; ?> px-3 py-2 rounded-pill"><?= ucfirst($row['role']); ?></span></td>
                        <td class="font-monospace small"><?= $row['username']; ?></td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm rounded">
                                <button type="button" class="btn btn-sm btn-white border" data-bs-toggle="modal" data-bs-target="#modalEditUser<?= $row['id']; ?>" title="Edit User">
                                    <i class="bi bi-pencil-square text-primary"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-white border" onclick="confirmDelete(<?= $row['id']; ?>)" title="Hapus User">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Registrasi Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses_tambah_user.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama lengkap sesuai ijazah..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username untuk login..." required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Role</label>
                            <select name="role" class="form-select">
                                <option value="siswa">Siswa</option>
                                <option value="guru">Guru</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $modal_edit_list; ?>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus User?',
        text: "Akun ini tidak akan bisa dipulihkan setelah dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus Tetap!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "proses_hapus_user.php?id=" + id;
        }
    })
}
</script>

<?php include '../includes/layout_footer.php'; ?>