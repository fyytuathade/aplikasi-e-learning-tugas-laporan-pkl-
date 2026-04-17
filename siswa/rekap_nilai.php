<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') header("Location: index.php");
include '../includes/koneksi.php';
include '../includes/siswa_layout.php';

 $user_id = $_SESSION['id'];
 $query = mysqli_query($conn, "SELECT n.*, m.judul as nama_materi 
                              FROM nilai n 
                              JOIN kuis k ON n.kuis_id = k.id 
                              JOIN materi m ON k.materi_id = m.id 
                              WHERE n.siswa_id = ".$user_id."
                              ORDER BY n.waktu_tes DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h2 class="fw-bold text-dark"><i class="bi bi-bar-chart-line text-primary me-2"></i>Laporan Nilai</h2>
    <a href="../dashboard.php" class="btn btn-outline-secondary">Dashboard</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Tanggal Tes</th>
                        <th>Mata Pelajaran (Materi)</th>
                        <th>Skor Akhir</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no=1;
                    if(mysqli_num_rows($query) == 0) echo "<tr><td colspan='5' class='text-center py-4'>Belum ada data nilai.</td></tr>";
                    while($row = mysqli_fetch_assoc($query)):
                        $status = ($row['skor'] >= 75) ? 
                                  '<span class="badge bg-success">Lulus</span>' : 
                                  '<span class="badge bg-danger">Remedial</span>';
                    ?>
                    <tr class="text-center">
                        <td><?= $no++; ?></td>
                        <td><?= $row['waktu_tes']; ?></td>
                        <td class="text-start fw-bold"><?= $row['nama_materi']; ?></td>
                        <td><h4 class="mb-0"><?= $row['skor']; ?></h4></td>
                        <td><?= $status; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/layout_footer.php'; ?>