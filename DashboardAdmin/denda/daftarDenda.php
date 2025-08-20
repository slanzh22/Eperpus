<?php 
session_start();

if(!isset($_SESSION["signIn"]) ) {
  header("Location: ../../sign/admin/sign_in.php");
  exit;
}
require "../../config/config.php";

// Query hanya akan mengambil data pengembalian yang memiliki denda > 0
$dataDenda = queryReadData("
    SELECT 
        pengembalian.id_pengembalian, 
        pengembalian.id_buku, 
        buku.judul, 
        pengembalian.npm, 
        member.nama, 
        pengembalian.keterlambatan, 
        pengembalian.denda
    FROM pengembalian
    INNER JOIN buku ON pengembalian.id_buku = buku.id_buku
    INNER JOIN member ON pengembalian.npm = member.npm
    WHERE pengembalian.denda > 0
    ORDER BY pengembalian.buku_kembali DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Denda | Admin Dashboard</title>
    
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
        .table-custom thead th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; }
        .table-custom tbody tr:hover { background-color: #f1f1f1; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: all 0.2s ease-in-out; }
        .table-custom td, .table-custom th { vertical-align: middle; }
        .table-custom .book-title { font-weight: 500; }
        .table-custom .member-name { display: block; font-weight: 500; }
        .table-custom .member-npm { font-size: 0.85rem; color: #6c757d; }
        .denda-amount { font-weight: 600; color: #e74a3b; }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="../dashboardAdmin.php" class="mb-4">
        <img src="../../assets/LogoPerpus.png" alt="logo" style="width: 150px;">
    </a>
    <a href="../dashboardAdmin.php"><i class="fas fa-tachometer-alt fa-fw me-2"></i>Dashboard</a>
    <a href="../member/member.php"><i class="fas fa-users fa-fw me-2"></i>Kelola Member</a>
    <a href="../buku/daftarBuku.php"><i class="fas fa-book fa-fw me-2"></i>Kelola Buku</a>
    <a href="../peminjaman/peminjamanBuku.php"><i class="fas fa-hand-holding-heart fa-fw me-2"></i>Kelola Peminjaman</a>
    <a href="../pengembalian/pengembalianBuku.php"><i class="fas fa-undo-alt fa-fw me-2"></i> Pengembalian</a>
    <a href="daftarDenda.php" class="active"><i class="fas fa-dollar-sign fa-fw me-2"></i>Kelola Denda</a>
</div>

<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="h2 fw-bold">Kelola Denda Aktif</h1>
            <p class="text-muted">Berikut adalah daftar denda yang belum dilunasi oleh member.</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Nama Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Keterlambatan</th>
                            <th>Jumlah Denda</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($dataDenda)) : ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h5 class="mb-0">Tidak Ada Denda Aktif</h5>
                                    <p class="text-small">Semua denda sudah lunas.</p>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($dataDenda as $item) : ?>
                        <tr>
                            <td>
                                <span class="member-name"><?= htmlspecialchars($item["nama"]); ?></span>
                                <span class="member-npm"><?= htmlspecialchars($item["npm"]); ?></span>
                            </td>
                            <td>
                                <span class="book-title"><?= htmlspecialchars($item["judul"]); ?></span>
                            </td>
                            <td><?= htmlspecialchars($item["keterlambatan"]); ?> Hari</td>
                            <td class="denda-amount">Rp <?= number_format($item["denda"], 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <span class="badge bg-danger">Belum Lunas</span>
                            </td>
                            <td class="text-center">
                                <a href="formBayarDenda.php?id=<?= $item['id_pengembalian']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-money-bill-wave me-2"></i>Bayar Denda
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <footer class="mt-4">
        <p class="text-center text-muted">Created by Cups @2025</p>
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