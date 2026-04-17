<?php
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php"); // Redirect ke root karena layout di dalam folder
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning Modern</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }

        .sidebar {
            min-height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, #2c3e50 0%, #000000 100%);
            color: white;
            position: fixed;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin: 5px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .sidebar .brand {
            padding: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .main-content { margin-left: 250px; padding: 20px; }

        /* Paksa Ikon Muncul */
        .bi { display: inline-block !important; font-size: 1em; line-height: 1; vertical-align: -0.125em; margin-right: 0.5rem; width: 1em; height: 1em; }

        /* Card Style */
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }

        /* Responsive Mobile */
        @media (max-width: 768px) {
            .sidebar { margin-left: -250px; }
            .sidebar.active { margin-left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar d-flex flex-column" id="sidebar">
        <div class="brand"><i class="bi bi-mortarboard-fill"></i> E-Learning</div>
        <div class="d-flex flex-column justify-content-between h-100 pb-3">
            <ul class="nav nav-pills flex-column mt-3">
                <!-- Menu Dashboard (Kita arahkan ke Dashboard masing-masing role) -->
                <?php
                    $dashboard_link = "../index.php"; // Default
                    if($_SESSION['role'] == 'guru' || $_SESSION['role'] == 'admin') $dashboard_link = "../dashboard.php";
                    elseif($_SESSION['role'] == 'siswa') $dashboard_link = "../dashboard.php";
                ?>
                <li class="nav-item">
                    <a href="<?= $dashboard_link; ?>" class="nav-link">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>

                <!-- Menu Guru -->
                <?php if ($_SESSION['role'] == 'guru' || $_SESSION['role'] == 'admin'): ?>
                    <li class="nav-item"><span class="text-white-50 small px-3 mt-2">MENU GURU</span></li>
                    <li class="nav-item">
                        <a href="absensi.php" class="nav-link">
                            <i class="bi bi-calendar-check"></i> Absensi Siswa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="tambah_materi.php" class="nav-link">
                            <i class="bi bi-file-earmark-plus"></i> Tambah Materi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="tambah_kuis.php" class="nav-link">
                            <i class="bi bi-clipboard-check"></i> Bank Soal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="lihat_nilai.php" class="nav-link">
                            <i class="bi bi-list-check"></i> Laporan Nilai
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Menu Admin -->
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li class="nav-item"><span class="text-white-50 small px-3 mt-2">MENU ADMIN</span></li>
                    <li class="nav-item">
                        <a href="../admin/manajemen_user.php" class="nav-link">
                            <i class="bi bi-people-fill me-2"></i> Manajemen User
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Bagian Bawah: Pengaturan Akun -->
            <div class="px-3 mt-3">
                <div class="border-top border-secondary pt-3 mb-3">
                    <small class="text-white-50 d-block px-1 mb-2">AKUN SAYA</small>
                    <a href="../profil.php" class="nav-link text-white ps-0 mb-2">
                        <i class="bi bi-person-circle me-2"></i> Profil Saya
                    </a>
                </div>
                <a href="../logout.php" class="btn btn-danger w-100"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <nav class="navbar navbar-expand navbar-light bg-white rounded shadow-sm mb-4 p-3">
            <div class="container-fluid">
                <!-- Tombol Hamburger Mobile -->
                <button class="btn btn-light d-md-none me-2" id="sidebarToggle"><i class="bi bi-list"></i></button>
                
                <!-- Logo Desktop -->
                <span class="navbar-brand mb-0 h1 d-none d-md-block">E-Learning</span>

                <!-- User Profile Dropdown (Pojok Kanan) -->
                <div class="ms-auto d-flex align-items-center">
                    <div class="text-end d-none d-sm-block me-3">
                        <div class="fw-bold text-dark small"><?= $_SESSION['nama']; ?></div>
                        <div class="text-muted" style="font-size: 0.75rem;"><?= ucfirst($_SESSION['role']); ?></div>
                    </div>
                    
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php 
                                // LOGIKA FOTO PROFILE (Auto Query untuk halaman selain profil.php)
                                if(isset($src_foto) && $src_foto != "") {
                                    $foto_display = $src_foto;
                                } else {
                                    include '../includes/koneksi.php';
                                    $uid = $_SESSION['id'];
                                    $q_foto = mysqli_query($conn, "SELECT foto FROM users WHERE id = '$uid'");
                                    $d_foto = mysqli_fetch_assoc($q_foto);
                                    
                                    if(!empty($d_foto['foto']) && file_exists('../uploads/'.$d_foto['foto'])) {
                                        $foto_display = '../uploads/'.$d_foto['foto'];
                                    } else {
                                        $foto_display = "";
                                    }
                                }
                            ?>
                            
                            <?php if($foto_display != ""): ?>
                                <img src="<?= $foto_display; ?>" alt="Profile" width="40" height="40" class="rounded-circle border">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                    <?= substr($_SESSION['nama'], 0, 1); ?>
                                </div>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item" href="../profil.php"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i> Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Script Detektor Notifikasi -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                <?php if(isset($_SESSION['sukses'])): ?>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '<?= $_SESSION['sukses']; ?>',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    <?php unset($_SESSION['sukses']); ?>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: '<?= $_SESSION['error']; ?>',
                        confirmButtonColor: '#d33'
                    });
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            });
        </script>