<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit;
}

include '../includes/koneksi.php';

if (isset($_POST['kirim_jawaban'])) {
    $benar = 0;
    $total_soal = 0;
    
    if (isset($_POST['jawaban'])) {
        foreach ($_POST['jawaban'] as $id_kuis => $jawaban_siswa) {
            $total_soal++;
            $cek = mysqli_query($conn, "SELECT jawaban_benar FROM kuis WHERE id = '$id_kuis'");
            $kunci = mysqli_fetch_assoc($cek);
            if ($kunci && $kunci['jawaban_benar'] == $jawaban_siswa) {
                $benar++;
            }
        }
    }
    
    $skor = ($total_soal > 0) ? round((100 / $total_soal) * $benar) : 0;
    $siswa_id = $_SESSION['id'];
    
    // Ambil ID kuis pertama sebagai referensi
    $id_kuis_terakhir = isset($_POST['jawaban']) ? array_key_first($_POST['jawaban']) : 0;
    
    if ($id_kuis_terakhir != 0) {
        $query_simpan = mysqli_query($conn, "INSERT INTO nilai (siswa_id, kuis_id, skor) VALUES ('$siswa_id', '$id_kuis_terakhir', '$skor')");
        
        if ($query_simpan) {
            $_SESSION['sukses'] = "Kuis selesai! Nilai Anda: $skor";
            header("Location: kuis.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal menyimpan nilai kuis. Silakan coba lagi.";
            header("Location: kuis.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Tidak ada jawaban yang dikirim.";
        header("Location: kuis.php");
        exit;
    }
}

// ===== BAGIAN LAYOUT =====
include '../includes/siswa_layout.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark">
        <i class="bi bi-pencil-square text-warning me-2"></i>Kerjakan Kuis
    </h2>
    <a href="../dashboard.php" class="btn btn-outline-danger">Batal</a>
</div>

<form method="post">
    <?php
    $no = 1;
    $query = mysqli_query($conn, "SELECT * FROM kuis");
    if (mysqli_num_rows($query) == 0) {
        echo "<div class='alert alert-warning text-center'>Belum ada soal kuis tersedia.</div>";
    }
    while ($row = mysqli_fetch_assoc($query)):
    ?>
    <div class="card shadow-sm mb-4 border-start border-4 border-primary">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-3">
                <span class="badge bg-primary me-2"><?= $no++; ?></span>
                <?= htmlspecialchars($row['pertanyaan']); ?>
            </h5>
            
            <div class="list-group list-group-flush">
                <label class="list-group-item list-group-item-action d-flex gap-3 py-3">
                    <input class="form-check-input flex-shrink-0 mt-1" type="radio" name="jawaban[<?= $row['id']; ?>]" value="a" required>
                    <span class="pt-1 form-checked-content"><strong>A.</strong> <?= htmlspecialchars($row['pilihan_a']); ?></span>
                </label>
                <label class="list-group-item list-group-item-action d-flex gap-3 py-3">
                    <input class="form-check-input flex-shrink-0 mt-1" type="radio" name="jawaban[<?= $row['id']; ?>]" value="b">
                    <span class="pt-1 form-checked-content"><strong>B.</strong> <?= htmlspecialchars($row['pilihan_b']); ?></span>
                </label>
                <label class="list-group-item list-group-item-action d-flex gap-3 py-3">
                    <input class="form-check-input flex-shrink-0 mt-1" type="radio" name="jawaban[<?= $row['id']; ?>]" value="c">
                    <span class="pt-1 form-checked-content"><strong>C.</strong> <?= htmlspecialchars($row['pilihan_c']); ?></span>
                </label>
                <label class="list-group-item list-group-item-action d-flex gap-3 py-3">
                    <input class="form-check-input flex-shrink-0 mt-1" type="radio" name="jawaban[<?= $row['id']; ?>]" value="d">
                    <span class="pt-1 form-checked-content"><strong>D.</strong> <?= htmlspecialchars($row['pilihan_d']); ?></span>
                </label>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    
    <?php if (mysqli_num_rows($query) > 0): ?>
        <div class="d-grid gap-2 mt-5">
            <button type="submit" name="kirim_jawaban" class="btn btn-warning btn-lg text-white fw-bold" onclick="return confirm('Apakah Anda yakin ingin mengirim jawaban? Jawaban tidak dapat diubah lagi.')">
                <i class="bi bi-send-fill me-2"></i>Kirim Jawaban Sekarang
            </button>
        </div>
    <?php endif; ?>
</form>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Notifikasi setelah halaman dashboard dimuat
<?php if (isset($_SESSION['sukses'])): ?>
    Swal.fire({
        title: 'Berhasil!',
        html: '<?= $_SESSION['sukses']; ?>',
        icon: 'success',
        confirmButtonText: 'OK',
        timer: 4000,
        timerProgressBar: true
    });
    <?php unset($_SESSION['sukses']); ?>
<?php elseif (isset($_SESSION['error'])): ?>
    Swal.fire({
        title: 'Gagal',
        text: '<?= $_SESSION['error']; ?>',
        icon: 'error',
        confirmButtonText: 'OK'
    });
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>

<?php include '../includes/layout_footer.php'; ?>