<?php
session_start();

if (!isset($_SESSION["signInMember"])) {
    header("Location: ../../sign/member/sign_in.php");
    exit;
}

require "../../config/config.php";
$npm_member = $_SESSION["member"]["npm"];

// Query yang ditingkatkan untuk hanya menampilkan peminjaman yang masih aktif
$dataPinjam = queryReadData("
    SELECT 
        peminjaman.id_peminjaman, 
        peminjaman.id_buku, 
        buku.judul,
        peminjaman.tgl_peminjaman, 
        peminjaman.tgl_pengembalian
    FROM peminjaman
    INNER JOIN buku ON peminjaman.id_buku = buku.id_buku
    WHERE 
        peminjaman.npm = '$npm_member' AND 
        peminjaman.id_peminjaman NOT IN (SELECT id_peminjaman FROM pengembalian)
    ORDER BY peminjaman.tgl_peminjaman DESC
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
    <title>Peminjaman Saya | CupsLibs</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { width: 260px; background: linear-gradient(180deg, #bce4ffff, #0391f0ff); color: #ecf0f1; position: fixed; height: 100%; padding: 20px; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar a { color: #ecf0f1; text-decoration: none; display: block; padding: 10px 15px; border-radius: 8px; margin: 5px 0; transition: background 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #5dade2; }
        .sidebar-footer { margin-top: auto; }
        .main-content { margin-left: 260px; padding: 2.5rem; }
        .table-custom thead th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; }
        .table-custom tbody tr:hover { background-color: #f1f1f1; }
        .table-custom td, .table-custom th { vertical-align: middle; }

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
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="h2 fw-bold">Peminjaman Saya</h1>
            <p class="text-muted">Berikut adalah daftar buku yang sedang Anda pinjam.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>ID Peminjaman</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tenggat Kembali</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataPinjam)) : ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-book-reader fa-3x mb-3"></i>
                                    <h5 class="mb-0">Tidak ada buku yang sedang dipinjam.</h5>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($dataPinjam as $item) : ?>
                            <?php
                                $isOverdue = strtotime($item['tgl_pengembalian']) < time();
                            ?>
                        <tr>
                            <td><?= htmlspecialchars($item["id_peminjaman"]); ?></td>
                            <td><?= htmlspecialchars($item["judul"]); ?></td>
                            <td><?= date('d M Y', strtotime($item["tgl_peminjaman"])); ?></td>
                            <td><?= date('d M Y', strtotime($item["tgl_pengembalian"])); ?></td>
                            <td class="text-center">
                                <span class="badge <?= $isOverdue ? 'bg-danger' : 'bg-warning text-dark'; ?>">
                                    <?= $isOverdue ? 'Terlambat' : 'Dipinjam'; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a class="btn btn-success btn-sm" href="pengembalianBuku.php?id=<?= $item["id_peminjaman"]; ?>">
                                    <i class="fas fa-undo-alt me-1"></i> Kembalikan
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
</script>
</body>
</html>