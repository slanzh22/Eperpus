<?php
session_start();

if (!isset($_SESSION["signInMember"])) {
    header("Location: ../../sign/member/sign_in.php");
    exit;
}

require "../../config/config.php";

// === LOGIKA PHP YANG LEBIH EFISIEN ===
$query = "SELECT * FROM buku";
$kategori_aktif = "Semua";

// Filter berdasarkan kategori dari URL
if (isset($_GET['kategori'])) {
    $kategori = mysqli_real_escape_string($connection, $_GET['kategori']);
    $query .= " WHERE kategori = '$kategori'";
    $kategori_aktif = $kategori;
}

// Filter berdasarkan pencarian
if (isset($_POST["search"])) {
    $keyword = htmlspecialchars($_POST["keyword"]);
    if (strpos($query, 'WHERE') !== false) {
        $query .= " AND (judul LIKE '%$keyword%' OR pengarang LIKE '%$keyword%')";
    } else {
        $query .= " WHERE judul LIKE '%$keyword%' OR pengarang LIKE '%$keyword%'";
    }
}

$query .= " ORDER BY judul ASC";
$buku = queryReadData($query);
$semua_kategori = queryReadData("SELECT DISTINCT kategori FROM buku ORDER BY kategori ASC");

// Ambil nama file saat ini untuk menandai link sidebar yang aktif
$currentFile = basename($_SERVER['PHP_SELF']);
$nama_member = $_SESSION['member']['nama'] ?? 'Member';
$index = 0; // Untuk delay animasi
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Buku | CupsLibs</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { width: 260px; background: linear-gradient(180deg, #c0e6ffff, #2980b9); color: #ecf0f1; position: fixed; height: 100%; padding: 20px; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar a { color: #ecf0f1; text-decoration: none; display: block; padding: 10px 15px; border-radius: 8px; margin: 5px 0; transition: background 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #5dade2; }
        .sidebar-footer { margin-top: auto; }
        .main-content { margin-left: 260px; padding: 2.5rem; }
        .book-card { transition: all 0.3s ease; border: none; border-radius: 12px; overflow: hidden; }
        .book-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
        .book-card-img { height: 280px; object-fit: cover; }
        .book-card .card-body { display: flex; flex-direction: column; justify-content: space-between; }
        .category-badge { font-size: 0.75rem; font-weight: 500; }
        .filter-nav .nav-link { color: #6c757d; border-radius: 30px; padding: 0.5rem 1rem; margin: 0 0.25rem; }
        .filter-nav .nav-link.active { background-color: #0d6efd; color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

        /* === CSS BARU UNTUK PROFIL === */
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

<div class="sidebar">
    <div>
        <a href="/perpustakaan/perpustakaan/DashboardMember/dashboardMember.php" class="mb-4 d-block">
            <img src="/perpustakaan/perpustakaan/assets/LogoPerpus.png" alt="logo" style="width: 150px;">
        </a>
        
        <a href="/perpustakaan/perpustakaan/DashboardMember/dashboardMember.php" class="<?= ($currentFile == 'dashboardMember.php') ? 'active' : '' ?>">
            <i class="fas fa-home fa-fw me-2"></i>Dashboard
        </a>
        <a href="/perpustakaan/perpustakaan/DashboardMember/buku/daftarBuku.php" class="<?= ($currentFile == 'daftarBuku.php') ? 'active' : '' ?>">
            <i class="fas fa-book fa-fw me-2"></i>Daftar Buku
        </a>
        <a href="/perpustakaan/perpustakaan/DashboardMember/formPeminjaman/TransaksiPeminjaman.php" class="<?= ($currentFile == 'TransaksiPeminjaman.php') ? 'active' : '' ?>">
            <i class="fas fa-hand-holding-heart fa-fw me-2"></i>Peminjaman Saya
        </a>
        <a href="/perpustakaan/perpustakaan/DashboardMember/formPeminjaman/TransaksiPengembalian.php" class="<?= ($currentFile == 'TransaksiPengembalian.php') ? 'active' : '' ?>">
            <i class="fas fa-undo-alt fa-fw me-2"></i>Pengembalian Saya
        </a>
    </div>
    
    <div class="sidebar-footer">
        <hr class="text-white-50">
        <div class="profile-section d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="/perpustakaan/perpustakaan/assets/memberLogo.png" alt="Avatar" class="profile-avatar me-3">
                <div class="profile-info">
                    <p class="profile-name text-capitalize"><?= htmlspecialchars($nama_member); ?></p>
                    <p class="profile-role">Member</p>
                </div>
            </div>
            <a href="/perpustakaan/perpustakaan/DashboardMember/signOut.php" class="sign-out-btn" title="Sign Out">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</div>

<div class="main-content">
    
    <div data-aos="fade-down">
        <h1 class="h2 fw-bold">Daftar Buku</h1>
        <p class="text-muted">Temukan dan pinjam buku favorit Anda di sini.</p>
    </div>
    
    <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
            <ul class="nav filter-nav mb-3 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($kategori_aktif == 'Semua') ? 'active' : '' ?>" href="daftarBuku.php">Semua</a>
                </li>
                <?php foreach($semua_kategori as $kat) : ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($kategori_aktif == $kat['kategori']) ? 'active' : '' ?>" href="?kategori=<?= urlencode($kat['kategori']); ?>"><?= htmlspecialchars($kat['kategori']); ?></a>
                </li>
                <?php endforeach; ?>
            </ul>
            <form action="" method="post" style="width: 300px;">
                <div class="input-group">
                    <input type="text" class="form-control" name="keyword" placeholder="Cari buku...">
                    <button class="btn btn-outline-secondary" type="submit" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row g-4">
        <?php if(empty($buku)) : ?>
            <div class="col-12 text-center py-5" data-aos="fade-up">
                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Buku tidak ditemukan.</h4>
                <p>Coba gunakan kata kunci atau filter kategori yang berbeda.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($buku as $item) : ?>
        <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= ($index % 4) * 100 ?>">
            <div class="card book-card h-100 shadow-sm">
                <img src="/perpustakaan/perpustakaan/imgDB/<?= htmlspecialchars($item["cover"]); ?>" class="card-img-top book-card-img" alt="Cover Buku">
                <div class="card-body">
                    <div>
                        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill mb-2 category-badge"><?= htmlspecialchars($item["kategori"]); ?></span>
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($item["judul"]); ?></h5>
                        <p class="card-text text-muted small">oleh <?= htmlspecialchars($item["pengarang"]); ?></p>
                    </div>
                    <div class="d-grid mt-3">
                        <a class="btn btn-success" href="detailBuku.php?id=<?= $item["id_buku"]; ?>">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
        <?php $index++; ?>
        <?php endforeach; ?>
    </div>
    
    <footer class="mt-4">
        <p class="text-center text-muted">Created by Cups © 2024</p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 600,
        once: true
    });
</script>
</body>
</html>