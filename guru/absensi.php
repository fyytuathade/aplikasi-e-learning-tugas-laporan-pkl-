<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'guru') {
    header("Location: index.php");
    exit;
}

$pesan_sukses = "";
$pesan_error = "";

// Proses Simpan Absen
if (isset($_POST['simpan_absen'])) {
    $tanggal = date('Y-m-d');
    $berhasil = 0;
    $total = 0;

    if (isset($_POST['status']) && is_array($_POST['status'])) {
        foreach ($_POST['status'] as $siswa_id => $status_absen) {
            $total++;
            $siswa_id = mysqli_real_escape_string($conn, $siswa_id);
            $status_absen = mysqli_real_escape_string($conn, $status_absen);

            // Cek agar tidak double input di hari yang sama
            $cek = mysqli_query($conn, "SELECT * FROM absensi WHERE tanggal='$tanggal' AND siswa_id='$siswa_id'");
            
            if (mysqli_num_rows($cek) == 0) {
                $insert = mysqli_query($conn, "INSERT INTO absensi (tanggal, siswa_id, status) VALUES ('$tanggal', '$siswa_id', '$status_absen')");
                if ($insert) $berhasil++;
            } else {
                // Jika sudah ada, update saja
                mysqli_query($conn, "UPDATE absensi SET status='$status_absen' WHERE tanggal='$tanggal' AND siswa_id='$siswa_id'");
                $berhasil++;
            }
        }
    }

    if ($total > 0 && $berhasil > 0) {
        $pesan_sukses = "✅ Absensi berhasil disimpan untuk hari ini ($berhasil dari $total siswa)";
    } else {
        $pesan_error = "Tidak ada data absensi yang disimpan atau terjadi kesalahan.";
    }
}

// Query Data Siswa
$query = mysqli_query($conn, "SELECT * FROM users WHERE role='siswa' ORDER BY nama_lengkap ASC");

include '../includes/guru_layout.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark">Absensi Siswa</h2>
    <a href="../dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<!-- Notifikasi -->
<?php if ($pesan_sukses): ?>
    <div id="notif-sukses" class="alert alert-success d-none"><?= $pesan_sukses ?></div>
<?php elseif ($pesan_error): ?>
    <div id="notif-error" class="alert alert-danger d-none"><?= $pesan_error ?></div>
<?php endif; ?>

<div class="card p-4 shadow-sm border-0">
    <form method="post">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Hadir</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>Alpa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)):
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td class="text-center">
                            <input type="radio" name="status[<?= $row['id']; ?>]" value="Hadir" checked>
                        </td>
                        <td class="text-center">
                            <input type="radio" name="status[<?= $row['id']; ?>]" value="Sakit">
                        </td>
                        <td class="text-center">
                            <input type="radio" name="status[<?= $row['id']; ?>]" value="Izin">
                        </td>
                        <td class="text-center">
                            <input type="radio" name="status[<?= $row['id']; ?>]" value="Alpa">
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <button type="submit" name="simpan_absen" 
                class="btn btn-primary mt-3 px-4"
                onclick="return confirm('Simpan absensi hari ini?')">
            <i class="bi bi-save me-2"></i>Simpan Absensi
        </button>
    </form>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Tampilkan notifikasi SweetAlert setelah halaman selesai load
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