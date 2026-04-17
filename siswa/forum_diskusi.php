<?php
session_start();
if (!isset($_SESSION['id'])) header("Location: index.php");
include '../includes/koneksi.php';

// --- LOGIKA 1: BUAT TOPIK BARU ---
if (isset($_POST['buat_topik'])) {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $user_id = $_SESSION['id'];
    
    mysqli_query($conn, "INSERT INTO diskusi (user_id, judul, isi) VALUES ('$user_id', '$judul', '$isi')");
    echo "<script>window.location='forum_diskusi.php';</script>";
}

// --- LOGIKA 2: BALAS TOPIK ---
if (isset($_POST['balas'])) {
    $diskusi_id = $_POST['diskusi_id'];
    $isi_balasan = $_POST['isi_balasan'];
    $user_id = $_SESSION['id'];
    
    mysqli_query($conn, "INSERT INTO balasan_diskusi (diskusi_id, user_id, isi_balasan) VALUES ('$diskusi_id', '$user_id', '$isi_balasan')");
    echo "<script>window.location='forum_diskusi.php?detail=$diskusi_id';</script>";
}

 $mode_detail = isset($_GET['detail']);
include '../includes/siswa_layout.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark"><i class="bi bi-chat-dots text-primary me-2"></i>Forum Diskusi Kelas</h2>
    <?php if(!$mode_detail): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatTopik">
            <i class="bi bi-plus-circle me-2"></i>Buat Topik Baru
        </button>
    <?php else: ?>
        <a href="forum_diskusi.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Daftar</a>
    <?php endif; ?>
</div>

<?php if (!$mode_detail): ?>
    <!-- DAFTAR TOPIK -->
    <div class="row">
        <?php
        $query = mysqli_query($conn, "SELECT d.*, u.nama_lengkap, u.role FROM diskusi d JOIN users u ON d.user_id = u.id ORDER BY d.waktu DESC");
        if(mysqli_num_rows($query) == 0) echo "<p class='text-muted'>Belum ada diskusi. Jadilah yang pertama!</p>";
        while($row = mysqli_fetch_assoc($query)):
            $jml_balas = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM balasan_diskusi WHERE diskusi_id = ".$row['id']));
            $inisial = substr($row['nama_lengkap'], 0, 1);
            $warna = ($row['role'] == 'guru') ? 'bg-danger' : 'bg-primary';
        ?>
        <div class="col-md-12 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle <?= $warna; ?> text-white d-flex align-items-center justify-content-center fw-bold" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                <?= $inisial; ?>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between">
                                <h5 class="fw-bold mb-1 text-primary" style="cursor:pointer;" onclick="window.location='forum_diskusi.php?detail=<?= $row['id']; ?>'">
                                    <?= $row['judul']; ?>
                                </h5>
                                <small class="text-muted"><?= time_ago($row['waktu']); ?></small>
                            </div>
                            <p class="text-muted mb-1 small">Oleh: <?= $row['nama_lengkap']; ?></p>
                            <p class="mb-2 text-truncate" style="max-width: 800px;"><?= $row['isi']; ?></p>
                            <a href="forum_diskusi.php?detail=<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary">
                                Lihat Diskusi (<?= $jml_balas; ?> Balasan)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

<?php else: ?>
    <!-- DETAIL DISKUSI -->
    <?php
    $id_detail = $_GET['detail'];
    $q_detail = mysqli_query($conn, "SELECT d.*, u.nama_lengkap, u.role FROM diskusi d JOIN users u ON d.user_id = u.id WHERE d.id = '$id_detail'");
    $data_detail = mysqli_fetch_assoc($q_detail);
    $inisial = substr($data_detail['nama_lengkap'], 0, 1);
    $warna = ($data_detail['role'] == 'guru') ? 'bg-danger' : 'bg-primary';
    ?>

    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-primary text-white fw-bold"><?= $data_detail['judul']; ?></div>
        <div class="card-body">
            <div class="d-flex align-items-start">
                <div class="rounded-circle <?= $warna; ?> text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 50px; height: 50px;">
                    <?= $inisial; ?>
                </div>
                <div>
                    <h6 class="fw-bold"><?= $data_detail['nama_lengkap']; ?></h6>
                    <small class="text-muted d-block mb-2"><?= $data_detail['waktu']; ?></small>
                    <p class="mb-0"><?= nl2br($data_detail['isi']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-3">Komentar / Balasan</h5>
    <?php
    $q_balas = mysqli_query($conn, "SELECT b.*, u.nama_lengkap, u.role FROM balasan_diskusi b JOIN users u ON b.user_id = u.id WHERE b.diskusi_id = '$id_detail' ORDER BY b.waktu ASC");
    while($balas = mysqli_fetch_assoc($q_balas)):
        $ini_balas = substr($balas['nama_lengkap'], 0, 1);
        $warna_balas = ($balas['role'] == 'guru') ? 'bg-danger' : 'bg-secondary';
    ?>
    <div class="d-flex mb-3 ms-5">
        <div class="flex-shrink-0">
            <div class="rounded-circle <?= $warna_balas; ?> text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 0.9rem;">
                <?= $ini_balas; ?>
            </div>
        </div>
        <div class="card ms-3 w-100">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between">
                    <h6 class="fw-bold mb-1 small"><?= $balas['nama_lengkap']; ?></h6>
                    <small class="text-muted" style="font-size: 0.7rem;"><?= $balas['waktu']; ?></small>
                </div>
                <p class="mb-0 small"><?= $balas['isi_balasan']; ?></p>
            </div>
        </div>
    </div>
    <?php endwhile; ?>

    <!-- Form Balas -->
    <div class="card mt-4 bg-light">
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="diskusi_id" value="<?= $id_detail; ?>">
                <div class="mb-2">
                    <textarea name="isi_balasan" class="form-control" rows="2" placeholder="Tulis balasan kamu..." required></textarea>
                </div>
                <button type="submit" name="balas" class="btn btn-primary btn-sm">Kirim Balasan</button>
            </form>
        </div>
    </div>

<?php endif; ?>

<!-- Modal Buat Topik -->
<div class="modal fade" id="modalBuatTopik" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Topik Diskusi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Judul Topik</label>
                        <input type="text" name="judul" class="form-control" required placeholder="Contoh: Cara mengatasi error XAMPP">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Pertanyaan</label>
                        <textarea name="isi" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" name="buat_topik" class="btn btn-primary w-100">Posting</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
function time_ago($time) {
    $time_difference = time() - strtotime($time);
    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    
    if ($seconds <= 60) return "Baru saja";
    else if ($minutes <= 60) return "$minutes menit yang lalu";
    else if ($hours <= 24) return "$hours jam yang lalu";
    else return date('d M Y', strtotime($time));
}
?>
<?php include '../includes/layout_footer.php'; ?>