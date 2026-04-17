<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'guru') {
    header("Location: index.php");
    exit;
}

$pesan_sukses = "";
$pesan_error = "";

// Proses Tambah Materi
if (isset($_POST['simpan'])) {
    $judul = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $guru_id = $_SESSION['id'];

    if (!empty($judul) && !empty($deskripsi)) {
        $query = mysqli_query($conn, "INSERT INTO materi (judul, deskripsi, guru_id) VALUES ('$judul', '$deskripsi', '$guru_id')");
        
        if ($query) {
            $pesan_sukses = "✅ Materi berhasil ditambahkan!<br><strong>$judul</strong>";
        } else {
            $pesan_error = "Gagal menambahkan materi. Silakan coba lagi.";
        }
    } else {
        $pesan_error = "Judul dan deskripsi tidak boleh kosong!";
    }
}

// Ambil daftar materi (opsional, jika ingin ditampilkan)
$materi_list = mysqli_query($conn, "SELECT * FROM materi ORDER BY id DESC");

include '../includes/guru_layout.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark">
        <i class="bi bi-book-plus-fill text-primary me-2"></i>Tambah Materi Baru
    </h2>
    <a href="../dashboard.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<!-- Notifikasi Area -->
<?php if ($pesan_sukses): ?>
    <div id="notif-sukses" class="alert alert-success d-none"><?= $pesan_sukses ?></div>
<?php elseif ($pesan_error): ?>
    <div id="notif-error" class="alert alert-danger d-none"><?= $pesan_error ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="post">
            <div class="mb-3">
                <label class="form-label fw-bold">Judul Materi</label>
                <input type="text" name="judul" class="form-control form-control-lg" 
                       placeholder="Contoh: Dasar Pemrograman PHP" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi Singkat</label>
                <textarea name="deskripsi" class="form-control" rows="4" 
                          placeholder="Jelaskan sedikit tentang materi ini..." required></textarea>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" name="simpan" 
                        class="btn btn-primary btn-lg"
                        onclick="return confirm('Simpan materi ini sekarang?')">
                    <i class="bi bi-save me-2"></i>Simpan Materi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Tampilkan notifikasi setelah halaman selesai dimuat
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($pesan_sukses): ?>
        Swal.fire({
            title: 'Berhasil!',
            html: '<?= $pesan_sukses; ?>',
            icon: 'success',
            confirmButtonText: 'OK',
            timer: 5000,
            timerProgressBar: true
        });
    <?php elseif ($pesan_error): ?>
        Swal.fire({
            title: 'Gagal',
            html: '<?= $pesan_error; ?>',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
});
</script>

<?php include '../includes/layout_footer.php'; ?>