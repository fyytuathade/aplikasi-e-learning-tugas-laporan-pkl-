<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') header("Location: index.php");
include '../includes/koneksi.php';
include '../includes/siswa_layout.php';

// Logic Filter Bulan
 $bulan_sekarang = date('Y-m');
 $filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : $bulan_sekarang;

// Query Data Absensi
 $where_sql = "WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$filter_bulan'";
 $query = mysqli_query($conn, "SELECT a.* FROM absensi a $where_sql ORDER BY tanggal DESC");

// Hitung Statistik
 $stat = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
if (mysqli_num_rows($query) > 0) {
    $query_stat = mysqli_query($conn, "SELECT status, COUNT(*) as jumlah FROM absensi $where_sql GROUP BY status");
    while($row = mysqli_fetch_assoc($query_stat)){
        $stat[$row['status']] = $row['jumlah'];
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-dark">Riwayat Absensi</h2>
        <p class="text-muted mb-0">Periode: <strong><?= date('F Y', strtotime($filter_bulan.'-01')); ?></strong></p>
    </div>
    
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex">
            <input type="month" name="bulan" class="form-control" value="<?= $filter_bulan; ?>" required>
            <button class="btn btn-primary ms-2">Filter</button>
        </form>
        <button onclick="window.print()" class="btn btn-outline-dark"><i class="bi bi-printer"></i></button>
    </div>
</div>

<!-- Statistik Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body text-center">
                <h1><?= $stat['Hadir']; ?></h1>
                <span>Hadir</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body text-center">
                <h1><?= $stat['Sakit']; ?></h1>
                <span>Sakit</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body text-center">
                <h1><?= $stat['Izin']; ?></h1>
                <span>Izin</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white h-100">
            <div class="card-body text-center">
                <h1><?= $stat['Alpa']; ?></h1>
                <span>Alpa</span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no=1;
                    if(mysqli_num_rows($query) == 0) echo "<tr><td colspan='3' class='text-center py-4'>Belum ada data absensi periode ini.</td></tr>";
                    while($row = mysqli_fetch_assoc($query)):
                        $badge = '';
                        if($row['status'] == 'Hadir') $badge = 'bg-success';
                        elseif($row['status'] == 'Sakit') $badge = 'bg-warning text-dark';
                        elseif($row['status'] == 'Izin') $badge = 'bg-info text-dark';
                        else $badge = 'bg-danger';
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                        <td><span class="badge <?= $badge; ?>"><?= $row['status']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/layout_footer.php'; ?>