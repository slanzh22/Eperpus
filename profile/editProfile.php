<?php
session_start();

if (!isset($_SESSION["signIn"])) {
    header("Location: ../../sign/admin/sign_in.php");
    exit;
}
require "../../config/config.php";

// Ambil ID admin dari session
$id_admin = $_SESSION['admin']['id'];

// Ambil data admin saat ini dari database
$current_admin = queryReadData("SELECT * FROM admin WHERE id = '$id_admin'")[0];

// Logika untuk memproses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Proses update nama
    if (isset($_POST["update_nama"])) {
        $nama_admin = htmlspecialchars($_POST["nama_admin"]);
        $query_update_nama = "UPDATE admin SET nama_admin = '$nama_admin' WHERE id = '$id_admin'";
        mysqli_query($connection, $query_update_nama);

        if (mysqli_affected_rows($connection) > 0) {
            // Update session juga agar nama di header berubah
            $_SESSION['admin']['nama_admin'] = $nama_admin;
            echo "<script>alert('Nama berhasil diperbarui!'); document.location.href = 'editProfile.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui nama atau tidak ada perubahan!');</script>";
        }
    }

    // Proses update password
    if (isset($_POST["update_password"])) {
        $password_baru = $_POST["password_baru"];
        $konfirmasi_password = $_POST["konfirmasi_password"];

        if ($password_baru !== $konfirmasi_password) {
            echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
        } else {
            // Enkripsi password baru
            $password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);
            $query_update_password = "UPDATE admin SET password = '$password_hashed' WHERE id = '$id_admin'";
            mysqli_query($connection, $query_update_password);

            if (mysqli_affected_rows($connection) > 0) {
                echo "<script>alert('Password berhasil diubah!'); document.location.href = 'editProfile.php';</script>";
            } else {
                echo "<script>alert('Gagal mengubah password!');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile | Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { width: 260px; background: linear-gradient(180deg, #2c3e50, #34495e); color: #ecf0f1; position: fixed; height: 100%; padding: 20px; display: flex; flex-direction: column; }
        .sidebar a { color: #ecf0f1; text-decoration: none; display: block; padding: 10px 15px; border-radius: 8px; margin: 5px 0; transition: background 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #4e637a; }
        .main-content { margin-left: 260px; padding: 2.5rem; }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="../dashboardAdmin.php" class="mb-4">
        <img src="../../assets/LogoPerpus.png" alt="logo" style="width: 150px;">
    </a>
    <a href="../dashboardAdmin.php"><i class="fas fa-tachometer-alt fa-fw me-2"></i>Dashboard</a>
    </div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="h2 fw-bold">Edit Profile</h1>
            <p class="text-muted">Kelola informasi pribadi dan keamanan akun Anda.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6" data-aos="fade-right">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Akun</h5>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="nama_admin" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_admin" name="nama_admin" value="<?= htmlspecialchars($current_admin['nama_admin']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($current_admin['username']); ?>" readonly>
                            <div class="form-text">Username tidak dapat diubah.</div>
                        </div>
                        <button type="submit" name="update_nama" class="btn btn-primary w-100">Simpan Perubahan Nama</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6" data-aos="fade-left">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Ubah Password</h5>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST" id="passwordForm">
                        <div class="mb-3">
                            <label for="password_baru" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password_baru" name="password_baru" required>
                        </div>
                        <div class="mb-3">
                            <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                        </div>
                        <button type="submit" name="update_password" class="btn btn-danger w-100">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });

    // Validasi sederhana untuk konfirmasi password
    const passwordForm = document.getElementById('passwordForm');
    passwordForm.addEventListener('submit', function(event) {
        const passwordBaru = document.getElementById('password_baru').value;
        const konfirmasiPassword = document.getElementById('konfirmasi_password').value;

        if (passwordBaru !== konfirmasiPassword) {
            alert('Konfirmasi password tidak cocok!');
            event.preventDefault(); // Mencegah form dikirim
        }
    });
</script>
</body>
</html>