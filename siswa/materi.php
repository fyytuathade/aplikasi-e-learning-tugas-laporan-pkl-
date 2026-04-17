<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') header("Location: index.php");
include '../includes/koneksi.php';
include '../includes/siswa_layout.php';
?>

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1"><i class="bi bi-collection-play text-primary me-2"></i>Materi Pembelajaran</h2>
        <p class="text-muted small mb-0">Akses materi pelajaran terbaru dari guru pengajar.</p>
    </div>
    <a href="../dashboard.php" class="btn btn-outline-primary mt-3 mt-md-0 shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Content Section -->
<div class="row g-4">
    <?php
    $query = mysqli_query($conn, "SELECT m.*, u.nama_lengkap FROM materi m JOIN users u ON m.guru_id = u.id ORDER BY m.id DESC");
    
    if(mysqli_num_rows($query) == 0): 
    ?>
        <!-- Empty State -->
        <div class="col-12">
            <div class="text-center py-5 bg-white rounded-3 shadow-sm border border-light">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486747.png" width="120" alt="Empty" class="mb-3 opacity-50">
                <h5 class="text-muted">Belum Ada Materi</h5>
                <p class="text-muted small">Guru belum mengunggah materi pelajaran saat ini.</p>
            </div>
        </div>
    <?php
    else:
        while($row = mysqli_fetch_assoc($query)):
    ?>
        <!-- Card Materi -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 hover-lift transition-all">
                <!-- Header Card -->
                <div class="card-header bg-white border-bottom border-light py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-person-video3 fs-5"></i>
                        </div>
                        <div>
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-normal px-2 py-1 rounded-pill small mb-1">
                                <?= $row['nama_lengkap']; ?>
                            </span>
                            <div class="text-muted small" style="font-size: 0.75rem;">
                                <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y', strtotime($row['tanggal'])); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Body Card -->
                <div class="card-body">
                    <h5 class="fw-bold text-dark mb-3 text-truncate" title="<?= $row['judul']; ?>"><?= $row['judul']; ?></h5>
                    <p class="text-muted small" style="display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; height: 60px;">
                        <?= $row['deskripsi']; ?>
                    </p>
                </div>

                <!-- Footer Card -->
                <div class="card-footer bg-white border-top border-light pt-3 pb-3 px-3">
                    <!-- Tombol Trigger Modal -->
                    <button type="button" class="btn btn-primary w-100 fw-semibold shadow-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalMateri<?= $row['id']; ?>">
                        <i class="bi bi-eye me-2"></i>Lihat Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL POPUP (Unik per ID Materi) -->
        <div class="modal fade" id="modalMateri<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-secondary mb-2"><?= $row['nama_lengkap']; ?></span>
                                <h4 class="modal-title fw-bold text-dark"><?= $row['judul']; ?></h4>
                                <small class="text-muted"><i class="bi bi-calendar-event me-1"></i> <?= date('l, d F Y', strtotime($row['tanggal'])); ?></small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="bg-light p-4 rounded-3">
                            <h6 class="fw-bold text-dark mb-2">Deskripsi Materi:</h6>
                            <p class="text-secondary text-justify" style="line-height: 1.6;">
                                <?= nl2br($row['deskripsi']); ?>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <?php if(!empty($row['link'])): ?>
                            <a href="<?= $row['link']; ?>" target="_blank" class="btn btn-success text-white">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Buka Link Materi
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Tidak ada lampiran link</button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MODAL -->

    <?php 
        endwhile; 
    endif; 
    ?>
</div>

<!-- Custom CSS -->
<style>
    .bg-light-primary {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>

<?php include '../includes/layout_footer.php'; ?>