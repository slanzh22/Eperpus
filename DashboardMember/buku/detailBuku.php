<?php
session_start();

if (!isset($_SESSION["signInMember"])) {
    header("Location: ../sign/member/sign_in.php");
    exit;
}

require "../../config/config.php";

if (!isset($_GET["id"])) {
    header("Location: daftarBuku.php");
    exit;
}
$idBuku = $_GET["id"];

// Menggunakan Prepared Statement untuk keamanan
$stmt = mysqli_prepare($connection, "SELECT * FROM buku WHERE id_buku = ?");
mysqli_stmt_bind_param($stmt, "s", $idBuku);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "Buku tidak ditemukan.";
    exit;
}
$item = mysqli_fetch_assoc($result);

// Data untuk sidebar
$currentFile = basename($_SERVER['PHP_SELF']);
$nama_member = $_SESSION['member']['nama'] ?? 'Member';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail: <?= htmlspecialchars($item["judul"]); ?> || CupsLibs</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { width: 260px; background: linear-gradient(180deg, #cfecffff, #2980b9); color: #ecf0f1; position: fixed; height: 100%; padding: 20px; display: flex; flex-direction: column; z-index: 1000;}
        .sidebar a { color: #ecf0f1; text-decoration: none; display: block; padding: 10px 15px; border-radius: 8px; margin: 5px 0; transition: background 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #5dade2; }
        .sidebar-footer { margin-top: auto; }
        .main-content { margin-left: 260px; padding: 2.5rem; }
        .detail-cover-img {
            width: 100%;
            max-width: 350px;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
        }
        .detail-cover-img:hover { transform: scale(1.03); }
        .book-title { font-size: 2.5rem; font-weight: 700; line-height: 1.2; }
        .book-author { font-size: 1.25rem; font-weight: 400; color: #6c757d; }
        .book-description { font-size: 1rem; line-height: 1.8; color: #495057; }
        .detail-item { display: flex; align-items: center; margin-bottom: 0.75rem; font-size: 0.95rem; }
        .detail-item i { width: 30px; text-align: center; color: #0d6efd; }
        .btn-pinjam { padding: 0.8rem 2rem; font-weight: 600; }
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
        <a href="/perpustakaan/perpustakaan/DashboardMember/buku/daftarBuku.php" class="<?= ($currentFile == 'daftarBuku.php' || $currentFile == 'detailBuku.php') ? 'active' : '' ?>">
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
        <div class="d-flex align-items-center">
            <img src="/perpustakaan/perpustakaan/assets/memberLogo.png" alt="memberLogo" width="40px" class="rounded-circle me-2">
            <div>
                <span class="fw-bold text-capitalize d-block"><?= htmlspecialchars($nama_member); ?></span>
                <a href="/perpustakaan/perpustakaan/DashboardMember/signOut.php" class="text-white-50" style="font-size: 0.8rem;">Sign Out</a>
            </div>
        </div>
    </div>
</div>

<div class="main-content">
    
    <div class="mb-4">
        <a href="daftarBuku.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Buku</a>
    </div>

    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-body p-4 p-md-5">
            <div class="row g-5">
                <div class="col-lg-4 text-center text-lg-start">
                    <img src="/perpustakaan/perpustakaan/imgDB/<?= htmlspecialchars($item["cover"]); ?>" class="detail-cover-img" alt="Cover Buku">
                </div>
                
                <div class="col-lg-8">
                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill mb-2"><?= htmlspecialchars($item["kategori"]); ?></span>
                    <h1 class="book-title"><?= htmlspecialchars($item["judul"]); ?></h1>
                    <p class="book-author mb-4">oleh <?= htmlspecialchars($item["pengarang"]); ?></p>
                    
                    <h5 class="fw-bold mt-4">Deskripsi</h5>
                    <p class="book-description">
                        <?= nl2br(htmlspecialchars($item["buku_deskripsi"])); ?>
                    </p>

                    <hr class="my-4">

                    <h5 class="fw-bold">Detail Buku</h5>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <i class="fas fa-building fa-fw"></i>
                                <span><strong>Penerbit:</strong> <?= htmlspecialchars($item["penerbit"]); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt fa-fw"></i>
                                <span><strong>Tahun Terbit:</strong> <?= htmlspecialchars($item["tahun_terbit"]); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <i class="fas fa-book-open fa-fw"></i>
                                <span><strong>Jumlah Halaman:</strong> <?= htmlspecialchars($item["jumlah_halaman"]); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <i class="fas fa-barcode fa-fw"></i>
                                <span><strong>ID Buku:</strong> <?= htmlspecialchars($item["id_buku"]); ?></span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <a href="../formPeminjaman/pinjamBuku.php?id=<?= $item["id_buku"]; ?>" class="btn btn-success btn-lg btn-pinjam">
                            <i class="fas fa-hand-holding-heart me-2"></i>Pinjam Buku
                        </a>
                        <a href="daftarBuku.php" class="btn btn-outline-secondary btn-lg">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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