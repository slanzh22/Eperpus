<?php 
session_start();

if(!isset($_SESSION["signIn"]) ) {
  header("Location: ../../sign/admin/sign_in.php");
  exit;
}
require "../../config/config.php";

$dataPengembalian = queryReadData("
    SELECT 
        pengembalian.id_pengembalian, 
        pengembalian.id_buku, 
        buku.judul, 
        pengembalian.npm, 
        member.nama, 
        pengembalian.buku_kembali, 
        pengembalian.keterlambatan, 
        pengembalian.denda
    FROM pengembalian
    INNER JOIN buku ON pengembalian.id_buku = buku.id_buku
    INNER JOIN member ON pengembalian.npm = member.npm
    ORDER BY pengembalian.buku_kembali DESC
");

// Data untuk sidebar
$currentFile = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Pengembalian | Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { width: 260px; background: #2c3e50; color: #ecf0f1; position: fixed; height: 100%; padding: 20px; z-index: 1000; }
        .sidebar-nav a { color: #bdc3c7; text-decoration: none; display: flex; align-items: center; padding: 12px 15px; border-radius: 8px; margin-bottom: 8px; transition: all 0.3s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background-color: #34495e; color: #ffffff; }
        .sidebar-nav a i { margin-right: 15px; width: 20px; text-align: center; }
        .main-content { margin-left: 260px; padding: 2.5rem; }
        
        .table-custom { border-collapse: separate; border-spacing: 0 10px; }
        .table-custom thead th { border: none; font-weight: 600; color: #6c757d; }
        .table-custom tbody tr {
            background-color: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }
        .table-custom tbody tr:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .table-custom td { border: none; padding: 20px; }
        .table-custom td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
        .table-custom td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }

        .book-title { font-weight: 500; }
        .member-name { display: block; font-weight: 500; }
        .member-npm { font-size: 0.85rem; color: #6c757d; }
    </style>
</head>
<body>

<div class="sidebar">
    <div>
        <a href="../dashboardAdmin.php" class="mb-4 d-block">
            <img src="../../assets/LogoPerpus.png" alt="logo" style="width: 150px;">
        </a>
        <nav class="sidebar-nav">
            <a href="../dashboardAdmin.php" class="<?= ($currentFile == 'dashboardAdmin.php') ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="../member/member.php" class="<?= ($currentFile == 'member.php') ? 'active' : '' ?>"><i class="fas fa-users"></i> Kelola Anggota</a>
            <a href="../buku/daftarBuku.php" class="<?= ($currentFile == 'daftarBuku.php') ? 'active' : '' ?>"><i class="fas fa-book"></i> Kelola Buku</a>
            <a href="../peminjaman/peminjamanBuku.php" class="<?= ($currentFile == 'peminjamanBuku.php') ? 'active' : '' ?>"><i class="fas fa-hand-holding-heart"></i> Peminjaman</a>
            <a href="pengembalianBuku.php" class="active"><i class="fas fa-undo-alt"></i> Pengembalian</a>
            <a href="../denda/daftarDenda.php" class="<?= ($currentFile == 'daftarDenda.php') ? 'active' : '' ?>"><i class="fas fa-dollar-sign"></i> Kelola Denda</a>
        </nav>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="h2 fw-bold">Riwayat Pengembalian Buku</h1>
            <p class="text-muted">Monitor semua buku yang telah dikembalikan dan status denda.</p>
        </div>
    </div>
    
    <div data-aos="fade-up">
        <table class="table table-borderless table-custom">
            <thead>
                <tr>
                    <th>Judul Buku</th>
                    <th>Nama Peminjam</th>
                    <th>Tgl Kembali</th>
                    <th>Denda</th>
                    <th class="text-center">Status Denda</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $delay = 0; ?>
                <?php foreach ($dataPengembalian as $item) : ?>
                    <?php
                        $denda = (int)$item['denda'];
                        $statusDenda = ($denda > 0) ? 'Belum Lunas' : 'Lunas';
                        $badgeClass = ($denda > 0) ? 'bg-danger' : 'bg-success';
                        $delay += 100; // Tambah delay untuk setiap baris
                    ?>
                <tr data-aos="fade-up" data-aos-delay="<?= $delay; ?>">
                    <td>
                        <span class="book-title"><?= htmlspecialchars($item["judul"]); ?></span>
                    </td>
                    <td>
                        <span class="member-name"><?= htmlspecialchars($item["nama"]); ?></span>
                        <span class="member-npm"><?= htmlspecialchars($item["npm"]); ?></span>
                    </td>
                    <td><?= date('d M Y', strtotime($item["buku_kembali"])); ?></td>
                    <td>Rp <?= number_format($denda, 0, ',', '.'); ?></td>
                    <td class="text-center">
                        <span class="badge <?= $badgeClass; ?>"><?= $statusDenda; ?></span>
                    </td>
                    <td class="text-center">
                        <?php if ($denda > 0) : ?>
                            <a href="../denda/formBayarDenda.php?id=<?= $item['id_pengembalian']; ?>" class="btn btn-sm btn-warning">
                                Bayar Denda
                            </a>
                        <?php endif; ?>
                        <a href="deletePengembalian.php?id=<?= $item["id_pengembalian"]; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus riwayat ini?');">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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