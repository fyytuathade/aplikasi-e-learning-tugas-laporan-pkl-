<?php
session_start();
include 'includes/koneksi.php';

// Pesan Error/Sukses
 $message = "";
 $msg_type = "";

// --- LOGIKA LOGIN ---
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND password = '$password'");
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['id'] = $data['id'];
        $_SESSION['nama'] = $data['nama_lengkap'];
        $_SESSION['role'] = $data['role'];
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Username atau Password salah!";
        $msg_type = "danger";
    }
}

// --- LOGIKA REGISTER (DAFTAR) ---
if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $user = $_POST['username_reg'];
    $pass = $_POST['password_reg'];
    $role = isset($_POST['role_reg']) ? $_POST['role_reg'] : 'siswa';

    // Cek apakah username sudah ada
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$user'");
    if (mysqli_num_rows($cek_user) > 0) {
        $message = "Username sudah digunakan orang lain!";
        $msg_type = "warning";
    } else {
        // Insert User Baru
        $insert = mysqli_query($conn, "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$user', '$pass', '$nama', '$role')");
        if ($insert) {
            $message = "Pendaftaran Berhasil! Silakan Login.";
            $msg_type = "success";
        } else {
            $message = "Terjadi kesalahan sistem.";
            $msg_type = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk / Daftar - E-Learning</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; justify-content: center; }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 900px;
            max-width: 95%;
            display: flex;
            min-height: 550px;
        }

        /* Sisi Kiri (Branding) */
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Dekorasi Lingkaran di background */
        .login-left::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            top: -50px;
            left: -50px;
        }

        /* Sisi Kanan (Form) */
        .login-right {
            flex: 1.2;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            background-color: #f9f9f9;
        }
        .form-control:focus {
            background-color: white;
            border-color: #0d6efd;
            box-shadow: none;
        }

        .btn-primary {
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Animasi Pindah Form */
        .form-section {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }
        .form-section.active { display: block; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Mobile */
        @media (max-width: 768px) {
            .login-container { flex-direction: column; height: auto; }
            .login-left { padding: 30px; min-height: 200px; }
            .login-right { padding: 30px; }
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Sisi Kiri -->
    <div class="login-left">
        <i class="bi bi-mortarboard-fill" style="font-size: 5rem; margin-bottom: 20px;"></i>
        <h2 class="fw-bold mb-3">Selamat Datang!</h2>
        <p class="opacity-75">Platform E-Learning Modern untuk masa depan pendidikan yang lebih baik.</p>
        <div class="mt-4 small opacity-50">&copy; 2024 E-Learning Sekolah</div>
    </div>

    <!-- Sisi Kanan -->
    <div class="login-right">
        
        <!-- Pesan Alert -->
        <?php if($message): ?>
            <div class="alert alert-<?= $msg_type; ?> alert-dismissible fade show" role="alert">
                <?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tombol Toggle Login/Register -->
        <div class="d-flex justify-content-end mb-4">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active" id="btn-show-login" onclick="switchForm('login')">Masuk</button>
                <button type="button" class="btn btn-outline-primary" id="btn-show-register" onclick="switchForm('register')">Daftar</button>
            </div>
        </div>

        <!-- FORM LOGIN -->
        <div id="form-login" class="form-section active">
            <h4 class="fw-bold mb-4 text-dark">Login ke Akun</h4>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label text-muted small">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 mb-3">MASUK SEKARANG</button>
                
            </form>
        </div>

        <!-- FORM REGISTER -->
        <div id="form-register" class="form-section">
            <h4 class="fw-bold mb-4 text-dark">Buat Akun Baru</h4>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label text-muted small">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama lengkap kamu" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username_reg" class="form-control" placeholder="Buat username unik" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Password</label>
                    <input type="password" name="password_reg" class="form-control" placeholder="Buat password" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small">Daftar Sebagai</label>
                    <select name="role_reg" class="form-select">
                        <option value="siswa" selected>Siswa</option>
                        <option value="guru">Guru</option>
                    </select>
                </div>
                <button type="submit" name="register" class="btn btn-primary w-100 mb-3">DAFTAR AKUN</button>
                <div class="text-center small text-muted">
                    Dengan mendaftar, kamu menyetujui syarat & ketentuan.
                </div>
            </form>
        </div>

    </div>
</div>

<!-- Script JS Sederhana untuk Pindah Tab -->
<script>
    function switchForm(type) {
        const loginForm = document.getElementById('form-login');
        const registerForm = document.getElementById('form-register');
        const btnLogin = document.getElementById('btn-show-login');
        const btnRegister = document.getElementById('btn-show-register');

        if (type === 'login') {
            loginForm.classList.add('active');
            registerForm.classList.remove('active');
            btnLogin.classList.add('active');
            btnRegister.classList.remove('active');
        } else {
            loginForm.classList.remove('active');
            registerForm.classList.add('active');
            btnLogin.classList.remove('active');
            btnRegister.classList.add('active');
        }
    }
</script>

</body>
</html>