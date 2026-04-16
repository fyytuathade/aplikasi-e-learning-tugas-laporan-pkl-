<?php
session_start();
include 'includes/koneksi.php';

// Query Statistik
 $total_siswa = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role='siswa'"));
 $total_guru = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role='guru'"));
 $total_materi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM materi"));

// Data Grafik (Distribusi Nilai)
 $grafik_nilai = mysqli_query($conn, "SELECT skor, COUNT(*) as jumlah FROM nilai GROUP BY skor ORDER BY skor ASC");
 $labels = [];
 $data = [];
while($row = mysqli_fetch_assoc($grafik_nilai)){
    $labels[] = "Nilai " . $row['skor'];
    $data[] = $row['jumlah'];
}

include 'includes/main_layout.php'; // Memanggil Header & Sidebar
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark">Dashboard Overview</h2>
    <div class="text-muted">Halo, <b><?php echo $_SESSION['nama']; ?></b> (<?php echo ucfirst($_SESSION['role']); ?>)</div>
</div>

<!-- Statistik Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title opacity-75">Total Siswa</h6>
                    <h2 class="fw-bold mb-0"><?php echo $total_siswa; ?></h2>
                </div>
                <i class="bi bi-people-fill stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title opacity-75">Total Guru</h6>
                    <h2 class="fw-bold mb-0"><?php echo $total_guru; ?></h2>
                </div>
                <i class="bi bi-person-badge-fill stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title opacity-75">Materi Aktif</h6>
                    <h2 class="fw-bold mb-0"><?php echo $total_materi; ?></h2>
                </div>
                <i class="bi bi-book-half stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- Grafik & Tabel -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-bold py-3">Statistik Nilai Siswa</div>
            <div class="card-body">
                <canvas id="nilaiChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-bold py-3">Aktivitas Terbaru</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                    $aktivitas = mysqli_query($conn, "SELECT * FROM nilai ORDER BY id DESC LIMIT 5");
                    while($act = mysqli_fetch_assoc($aktivitas)):
                        $siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE id=".$act['siswa_id']));
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold d-block text-primary"><?= $siswa['nama_lengkap']; ?></span>
                            <small class="text-muted">Telah mengerjakan kuis</small>
                        </div>
                        <span class="badge bg-success rounded-pill"><?= $act['skor']; ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('nilaiChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Jumlah Siswa',
                data: <?php echo json_encode($data); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    // Toggle Sidebar Mobile
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>

<?php include 'includes/layout_footer.php'; ?> 
<!-- Kita perlu file layout_footer.php untuk menutup tag </body></html> -->