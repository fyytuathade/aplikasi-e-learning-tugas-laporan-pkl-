<?php
session_start();
// Cek akses: Hanya Guru atau Admin
if (!isset($_SESSION['id']) || ($_SESSION['role'] != 'guru' && $_SESSION['role'] != 'admin')) {
    header("Location: index.php");
    exit;
}
include '../includes/koneksi.php';

// Logika Pencarian Sederhana
 $search = isset($_GET['q']) ? $_GET['q'] : '';
 $where = "";
if($search != ""){
    $where = "AND u.nama_lengkap LIKE '%$search%' OR m.judul LIKE '%$search%'";
}

// Query Gabungan (4 Tabel) untuk data lengkap
 $query = mysqli_query($conn, "SELECT n.id, u.nama_lengkap, m.judul as materi, n.skor, n.waktu_tes
                              FROM nilai n
                              JOIN users u ON n.siswa_id = u.id
                              JOIN kuis k ON n.kuis_id = k.id
                              JOIN materi m ON k.materi_id = m.id
                              WHERE 1=1 $where
                              ORDER BY n.waktu_tes DESC");

// Sertakan Layout (Pastikan layout.php ada di root atau sesuaikan pathnya)
include '../includes/guru_layout.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h2 class="fw-bold text-dark"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Laporan Nilai Siswa</h2>
    
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex">
            <input type="text" name="q" class="form-control" placeholder="Cari Nama / Materi..." value="<?= $search; ?>">
            <button class="btn btn-primary ms-2">Cari</button>
        </form>
        <button onclick="window.print()" class="btn btn-outline-dark"><i class="bi bi-printer"></i> Print</button>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle border">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Mata Pelajaran</th>
                        <th>Tanggal</th>
                        <th>Nilai</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no=1;
                    if(mysqli_num_rows($query) == 0) {
                        echo "<tr><td colspan='6' class='text-center py-4'>Data tidak ditemukan.</td></tr>";
                    }
                    while($row = mysqli_fetch_assoc($query)):
                        // Warna badge status
                        $status = ($row['skor'] >= 75) ? 
                                  '<span class="badge bg-success">Lulus</span>' : 
                                  '<span class="badge bg-danger">Remedial</span>';
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="fw-bold"><?= $row['nama_lengkap']; ?></td>
                        <td><?= $row['materi']; ?></td>
                        <td class="text-muted small"><?= date('d M Y', strtotime($row['waktu_tes'])); ?></td>
                        <td class="text-center fw-bold"><?= $row['skor']; ?></td>
                        <td class="text-center"><?= $status; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- CSS Print Sederhana -->
<style>
    @media print {
        .no-print { display: none !important; }
        /* Sembunyikan elemen layout saat print jika perlu */
        .sidebar, .d-md-none { display: none !important; }
        .main-content { margin-left: 0; padding: 0; }
        body { background-color: white; }
        .card { border: none; box-shadow: none; }
    }
</style>

<?php include '../includes/layout_footer.php'; ?>