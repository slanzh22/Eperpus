<?php
session_start();

if (!isset($_SESSION["signInMember"])) {
    header("Location: ../../sign/member/sign_in.php");
    exit;
}

require "../../config/config.php";

// Tangkap id buku dari URL (GET) dan amankan
if (!isset($_GET["id"])) {
    header("Location: ../buku/daftarBuku.php");
    exit;
}
$idBuku = $_GET["id"];
$buku = queryReadData("SELECT * FROM buku WHERE id_buku = '$idBuku'")[0];

// Menampilkan data siswa yg sedang login
$npmSiswa = $_SESSION["member"]["npm"];
$dataSiswa = queryReadData("SELECT * FROM member WHERE npm = $npmSiswa")[0];

// Peminjaman 
if (isset($_POST["pinjam"])) {
    // Menyiapkan data untuk fungsi pinjamBuku()
    $_POST['tgl_peminjaman'] = date("Y-m-d");
    $_POST['tgl_pengembalian'] = date('Y-m-d', strtotime('+7 days'));
    $_POST['id'] = 1; // ID Admin default/sistem

    if (pinjamBuku($_POST) > 0) {
        echo "<script>
                alert('Buku berhasil dipinjam! Harap kembalikan sebelum " . date('d F Y', strtotime('+7 days')) . "');
                document.location.href = '../dashboardMember.php';
              </script>";
    } else {
        echo "<script>
                alert('Buku gagal dipinjam! Anda mungkin sudah meminjam buku lain atau memiliki denda.');
                document.location.href = '../buku/daftarBuku.php';
              </script>";
    }
}

// Data untuk sidebar
$currentFile = basename($_SERVER['PHP_SELF']);
$nama_member = $_SESSION['member']['nama'] ?? 'Member';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konfirmasi Pinjam Buku | CupsLibs</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { width: 260px; background: linear-gradient(180deg, #b9e3ffff, #2980b9); color: #ecf0f1; position: fixed; height: 100%; padding: 20px; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar a { color: #ecf0f1; text-decoration: none; display: block; padding: 10px 15px; border-radius: 8px; margin: 5px 0; transition: background 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #5dade2; }
        .sidebar-footer { margin-top: auto; }
        .main-content { margin-left: 260px; padding: 2.5rem; }
        .summary-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        .book-cover {
            width: 150px;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .summary-label { color: #6c757d; }
        .summary-value { font-weight: 500; }
        .alert-custom {
            background-color: #e9f7ef;
            color: #1d7b4b;
            border-color: #d1f0e1;
        }
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
        <a href="/perpustakaan/perpustakaan/DashboardMember/formPeminjaman/TransaksiPeminjaman.php" class="<?= ($currentFile == 'TransaksiPeminjaman.php' || $currentFile == 'pinjamBuku.php') ? 'active' : '' ?>">
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
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="h2 fw-bold">Konfirmasi Peminjaman</h1>
            <p class="text-muted">Periksa kembali detail buku dan data diri Anda sebelum melanjutkan.</p>
        </div>
    </div>

    <form action="" method="post">
        <input type="hidden" name="id_buku" value="<?= htmlspecialchars($buku["id_buku"]); ?>">
        <input type="hidden" name="npm" value="<?= htmlspecialchars($dataSiswa["npm"]); ?>">
        
        <div class="row g-4">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="card summary-card h-100">
                    <div class="card-body text-center">
                        <h4 class="mb-4">Detail Buku</h4>
                        <img src="/perpustakaan/perpustakaan/imgDB/<?= htmlspecialchars($buku["cover"]); ?>" alt="Cover Buku" class="book-cover mb-3">
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($buku["judul"]); ?></h5>
                        <p class="text-muted">oleh <?= htmlspecialchars($buku["pengarang"]); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7" data-aos="fade-left">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <h4 class="mb-4 text-center">Ringkasan Peminjaman</h4>
                        
                        <div class="summary-item">
                            <span class="summary-label">Nama Peminjam</span>
                            <span class="summary-value text-capitalize"><?= htmlspecialchars($dataSiswa["nama"]); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">NPM</span>
                            <span class="summary-value"><?= htmlspecialchars($dataSiswa["npm"]); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Jurusan</span>
                            <span class="summary-value"><?= htmlspecialchars($dataSiswa["jurusan"]); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Tanggal Pinjam</span>
                            <span class="summary-value"><?= date("d F Y"); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Tenggat Pengembalian</span>
                            <span class="summary-value fw-bold text-danger"><?= date('d F Y', strtotime('+7 days')); ?></span>
                        </div>
                        
                        <div class="alert alert-custom mt-4" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            Pastikan data di atas sudah benar. Buku harus dikembalikan sebelum tanggal tenggat untuk menghindari denda.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="../buku/detailBuku.php?id=<?= $buku['id_buku']; ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-success" name="pinjam">
                                <i class="fas fa-check-circle me-2"></i>Konfirmasi Pinjam
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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