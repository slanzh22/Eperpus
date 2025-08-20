<?php 
session_start();

if(!isset($_SESSION["signInMember"]) ) { // Diperbaiki: Menggunakan session member
  header("Location: ../../sign/member/sign_in.php");
  exit;
}
require "../../config/config.php";

$akunMember = $_SESSION["member"]["npm"];
// --- QUERY DIPERBARUI: Menambahkan 'buku.cover' ---
$dataPengembalian = queryReadData("
    SELECT 
        pengembalian.id_pengembalian, 
        buku.judul, 
        buku.cover, 
        pengembalian.buku_kembali, 
        pengembalian.keterlambatan, 
        pengembalian.denda
    FROM pengembalian
    INNER JOIN buku ON pengembalian.id_buku = buku.id_buku
    WHERE pengembalian.npm = '$akunMember'
    ORDER BY pengembalian.buku_kembali DESC
");

// Data untuk sidebar
$currentFile = basename($_SERVER['PHP_SELF']);
$nama_member = $_SESSION['member']['nama'] ?? 'Member';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Pengembalian | CupsLibs</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        :root {
            --navy-blue: #1A237E;
            --off-white: #F5F5F5;
        }

        body { font-family: 'Poppins', sans-serif; background-color: var(--off-white); }
        .sidebar { width: 260px; background: var(--navy-blue); color: var(--off-white); position: fixed; height: 100%; padding: 20px; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar a { color: var(--off-white); text-decoration: none; display: block; padding: 10px 15px; border-radius: 8px; margin: 5px 0; transition: background 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255, 255, 255, 0.1); }
        .sidebar-footer { margin-top: auto; }
        .main-content { margin-left: 260px; padding: 2.5rem; }
        .history-card { background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 1.5rem; margin-bottom: 1rem; display: flex; align-items: center; transition: all 0.2s ease-in-out; }
        .history-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.09); }
        .history-cover img { width: 70px; height: 100px; object-fit: cover; border-radius: 6px; margin-right: 1.5rem; }
        .history-details { flex-grow: 1; display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; align-items: center; }
        .detail-item .label { font-size: 0.8rem; color: #6c757d; display: block; }
        .detail-item .value { font-weight: 500; }
        .book-title { font-weight: 600; color: var(--navy-blue); }
        .denda-unpaid { font-weight: 600; color: #D32F2F; }

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
            <i class="fas fa-undo-alt fa-fw me-2"></i>Riwayat Pengembalian
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
        <h1 class="h2 fw-bold" style="color: var(--navy-blue);">Riwayat Pengembalian</h1>
        <p class="text-muted">Berikut adalah catatan semua buku yang pernah Anda kembalikan.</p>
    </div>

    <div class="mt-4" data-aos="fade-up">
        <?php if (empty($dataPengembalian)) : ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-history fa-3x mb-3"></i>
                <h5 class="mb-0">Belum ada riwayat pengembalian.</h5>
            </div>
        <?php endif; ?>

        <?php foreach ($dataPengembalian as $item) : ?>
        <div class="history-card" data-aos="fade-up" data-aos-delay="100">
            <div class="history-cover">
                <img src="/perpustakaan/perpustakaan/imgDB/<?= htmlspecialchars($item["cover"]); ?>" alt="Cover Buku">
            </div>
            <div class="history-details">
                <div class="detail-item">
                    <span class="label">Judul Buku</span>
                    <span class="value book-title"><?= htmlspecialchars($item["judul"]); ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">Tanggal Kembali</span>
                    <span class="value"><?= date('d F Y', strtotime($item["buku_kembali"])); ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">Denda</span>
                    <span class="value <?= ((int)$item['denda'] > 0) ? 'denda-unpaid' : ''; ?>">
                        Rp <?= number_format($item["denda"], 0, ',', '.'); ?>
                    </span>
                </div>
                <div class="detail-item text-center">
                    <span class="label">Status</span>
                    <span class="value">
                        <?php if ((int)$item['denda'] > 0) : ?>
                            <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis">Belum Lunas</span>
                        <?php else : ?>
                            <span class="badge rounded-pill bg-success-subtle text-success-emphasis">Lunas</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
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