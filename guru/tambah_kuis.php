<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'guru') {
    header("Location: index.php");
    exit;
}

$pesan_sukses = "";
$pesan_error = "";

// Proses Simpan Soal Kuis
if (isset($_POST['simpan'])) {
    $materi_id   = mysqli_real_escape_string($conn, $_POST['materi_id']);
    $pertanyaan  = mysqli_real_escape_string($conn, trim($_POST['pertanyaan']));
    $a           = mysqli_real_escape_string($conn, trim($_POST['a']));
    $b           = mysqli_real_escape_string($conn, trim($_POST['b']));
    $c           = mysqli_real_escape_string($conn, trim($_POST['c']));
    $d           = mysqli_real_escape_string($conn, trim($_POST['d']));
    $benar       = mysqli_real_escape_string($conn, $_POST['benar']);

    if (!empty($pertanyaan) && !empty($a) && !empty($b) && !empty($c) && !empty($d) && !empty($benar)) {
        
        $query = mysqli_query($conn, "INSERT INTO kuis 
            (materi_id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar) 
            VALUES 
            ('$materi_id', '$pertanyaan', '$a', '$b', '$c', '$d', '$benar')");
        
        if ($query) {
            $pesan_sukses = "✅ Soal kuis berhasil ditambahkan!";
        } else {
            $pesan_error = "Gagal menyimpan soal kuis. Silakan coba lagi.";
        }
    } else {
        $pesan_error = "Semua field harus diisi!";
    }
}

// Ambil daftar materi untuk dropdown
$materi_list = mysqli_query($conn, "SELECT * FROM materi ORDER BY judul ASC");

include '../includes/guru_layout.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark">
        <i class="bi bi-question-square-fill text-success me-2"></i>Buat Soal Kuis
    </h2>
    <a href="../dashboard.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<!-- Notifikasi -->
<?php if ($pesan_sukses): ?>
    <div id="notif-sukses" class="alert alert-success d-none"><?= $pesan_sukses ?></div>
<?php elseif ($pesan_error): ?>
    <div id="notif-error" class="alert alert-danger d-none"><?= $pesan_error ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="post">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Pilih Materi Terkait</label>
                    <select name="materi_id" class="form-select form-select-lg" required>
                        <option value="" selected disabled>-- Pilih Materi --</option>
                        <?php while($m = mysqli_fetch_assoc($materi_list)) { ?>
                            <option value="<?= $m['id']; ?>"><?= htmlspecialchars($m['judul']); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Pertanyaan</label>
                    <textarea name="pertanyaan" class="form-control" rows="3" 
                              placeholder="Tulis soal di sini..." required></textarea>
                </div>

                <div class="col-12"><hr><h6 class="fw-bold">Opsi Jawaban</h6></div>
                
                <div class="col-md-6 mb-3">
                    <label class="text-primary fw-bold">Pilihan A</label>
                    <input type="text" name="a" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-primary fw-bold">Pilihan B</label>
                    <input type="text" name="b" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-primary fw-bold">Pilihan C</label>
                    <input type="text" name="c" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-primary fw-bold">Pilihan D</label>
                    <input type="text" name="d" class="form-control" required>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold text-danger">Kunci Jawaban Benar</label>
                    <select name="benar" class="form-select" required>
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                        <option value="d">D</option>
                    </select>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="simpan" 
                        class="btn btn-success btn-lg"
                        onclick="return confirm('Simpan soal kuis ini sekarang?')">
                    <i class="bi bi-check-circle me-2"></i>Simpan Soal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Tampilkan notifikasi setelah halaman load
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
            text: '<?= $pesan_error; ?>',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
});
</script>

<?php include '../includes/layout_footer.php'; ?>