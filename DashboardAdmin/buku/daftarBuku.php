<?php
session_start();

if(!isset($_SESSION["signIn"]) ) {
  header("Location: ../../sign/admin/sign_in.php");
  exit;
}
require "../../config/config.php"; 

$buku = queryReadData("SELECT * FROM buku ORDER BY judul ASC");

if(isset($_POST["search"]) ) {
  $buku = searchBuku($_POST["keyword"]); 
}

// Data untuk sidebar
$currentFile = basename($_SERVER['PHP_SELF']);
$nama_admin = $_SESSION['admin']['nama_admin'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Buku | Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { width: 260px; background: #2c3e50; color: #ecf0f1; position: fixed; height: 100%; padding: 20px; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar-nav a { color: #bdc3c7; text-decoration: none; display: flex; align-items: center; padding: 12px 15px; border-radius: 8px; margin-bottom: 8px; transition: all 0.3s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background-color: #34495e; color: #ffffff; }
        .sidebar-nav a i { margin-right: 15px; width: 20px; text-align: center; }
        .sidebar-footer { margin-top: auto; }
        .main-content { margin-left: 260px; padding: 2.5rem; }
        .table-custom tbody tr:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: all 0.2s ease-in-out; }
        .table-custom td, .table-custom th { vertical-align: middle; }
        .cover-img { width: 60px; height: 85px; object-fit: cover; border-radius: 4px; }
        
        /* CSS untuk Profil Section */
        .profile-section { padding: 10px; border-radius: 8px; transition: background-color 0.3s ease; }
        .profile-section:hover { background-color: rgba(255, 255, 255, 0.1); }
        .profile-avatar { width: 45px; height: 45px; object-fit: cover; border-radius: 50%; border: 2px solid rgba(255, 255, 255, 0.7); }
        .profile-info { line-height: 1.2; }
        .profile-name { font-weight: 600; margin: 0; color: #fff; }
        .profile-role { font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); margin: 0; }
        .sign-out-btn { background: none; border: none; color: rgba(255, 255, 255, 0.7); font-size: 1.2rem; padding: 5px 10px; border-radius: 6px; transition: all 0.3s ease; }
        .sign-out-btn:hover { color: #fff; background-color: rgba(255, 255, 255, 0.2); }
    </style>
</head>
<body>

<!-- === SIDEBAR LENGKAP === -->
<div class="sidebar">
    <div>
        <a href="../dashboardAdmin.php" class="mb-4 d-block">
            <img src="../../assets/LogoPerpus.png" alt="logo" style="width: 150px;">
        </a>
        <nav class="sidebar-nav">
            <a href="../dashboardAdmin.php" class="<?= ($currentFile == 'dashboardAdmin.php') ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="../member/member.php" class="<?= ($currentFile == 'member.php') ? 'active' : '' ?>"><i class="fas fa-users"></i> Kelola Anggota</a>
            <a href="daftarBuku.php" class="<?= ($currentFile == 'daftarBuku.php') ? 'active' : '' ?>"><i class="fas fa-book"></i> Kelola Buku</a>
            <a href="../peminjaman/peminjamanBuku.php" class="<?= ($currentFile == 'peminjamanBuku.php') ? 'active' : '' ?>"><i class="fas fa-hand-holding-heart"></i> Peminjaman</a>
            <a href="../pengembalian/pengembalianBuku.php" class="<?= ($currentFile == 'pengembalianBuku.php') ? 'active' : '' ?>"><i class="fas fa-undo-alt"></i> Pengembalian</a>
            <a href="../denda/daftarDenda.php" class="<?= ($currentFile == 'daftarDenda.php') ? 'active' : '' ?>"><i class="fas fa-dollar-sign"></i> Kelola Denda</a>
        </nav>
    </div>
    <div class="sidebar-footer">
        <hr class="text-white-50">
        <div class="profile-section d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="../../assets/adminLogo.png" alt="Avatar" class="profile-avatar me-3">
                <div class="profile-info">
                    <p class="profile-name text-capitalize"><?= htmlspecialchars($nama_admin); ?></p>
                    <p class="profile-role">Admin</p>
                </div>
            </div>
            <a href="../signOut.php" class="sign-out-btn" title="Sign Out">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</div>

<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="h2 fw-bold">Kelola Data Buku</h1>
            <p class="text-muted">Berikut adalah daftar semua buku yang tersedia di perprakashan.</p>
        </div>
        <div>
            <a href="tambahBuku.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Tambah Buku</a>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-body">
            
            <div class="mb-4">
                <form action="" method="post" class="d-flex justify-content-end">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" name="keyword" placeholder="Cari Judul atau Pengarang..." value="<?= isset($_POST['keyword']) ? htmlspecialchars($_POST['keyword']) : '' ?>">
                        <button class="btn btn-outline-secondary" type="submit" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead class="text-center">
                        <tr>
                            <th>Cover</th>
                            <th>Judul Buku</th>
                            <th>Pengarang</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($buku)) : ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Data buku tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php foreach($buku as $item) : ?>
                        <tr>
                            <td class="text-center">
                                <img src="../../imgDB/<?= htmlspecialchars($item["cover"]); ?>" alt="Cover Buku" class="cover-img">
                            </td>
                            <td><?= htmlspecialchars($item["judul"]); ?></td>
                            <td><?= htmlspecialchars($item["pengarang"]); ?></td>
                            <td class="text-center"><?= htmlspecialchars($item["penerbit"]); ?></td>
                            <td class="text-center"><?= htmlspecialchars($item["tahun_terbit"]); ?></td>
                            <td class="text-center">
                                <a href="updateBuku.php?id=<?= $item["id_buku"]; ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                                <a href="deleteBuku.php?id=<?= $item["id_buku"]; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus buku ini?');"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <footer class="mt-4">
        <p class="text-center text-muted">Created by Kelompok 2 © 2024</p>
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>
</body>
</html>