<?php
session_start();

if (!isset($_SESSION["signIn"])) {
    header("Location: ../../sign/admin/sign_in.php");
    exit;
}
require "../../config/config.php";

// Logika untuk TAMBAH MEMBER
if (isset($_POST["tambah"])) {
    // Diasumsikan Anda memiliki fungsi signUp() atau fungsi serupa di config.php
    // Kita akan menggunakan fungsi signUp yang sudah pernah dibuat
    if (signUp($_POST) > 0) {
        echo "<script>
                alert('Member baru berhasil ditambahkan!');
                document.location.href = 'member.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan member!');
              </script>";
    }
}

// Logika untuk menampilkan dan mencari member
$member = queryReadData("SELECT * FROM member");
if (isset($_POST["search"])) {
    $member = searchMember($_POST["keyword"]);
}

$currentFile = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Member | Admin Dashboard</title>
    
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
        .main-content { margin-left: 260px; padding: 2.5rem; }
        .table-custom tbody tr { background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .table-custom tbody tr:hover { transform: translateY(-3px); box-shadow: 0 6px 15px rgba(0,0,0,0.08); transition: all 0.2s ease; }
    </style>
</head>
<body>

<div class="sidebar">
    <div>
        <a href="../dashboardAdmin.php" class="mb-4 d-block">
            <img src="../../assets/LogoPerpus.png" alt="logo" style="width: 150px;">
        </a>
        <nav class="sidebar-nav">
            <a href="../dashboardAdmin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="member.php" class="active"><i class="fas fa-users"></i> Kelola Anggota</a>
            <a href="../buku/daftarBuku.php"><i class="fas fa-book"></i> Kelola Buku</a>
            <a href="../peminjaman/peminjamanBuku.php"><i class="fas fa-hand-holding-heart"></i> Peminjaman</a>
            <a href="../pengembalian/pengembalianBuku.php"><i class="fas fa-undo-alt"></i> Pengembalian</a>
            <a href="../denda/daftarDenda.php"><i class="fas fa-dollar-sign"></i> Kelola Denda</a>
        </nav>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="h2 fw-bold">Kelola Data Member</h1>
            <p class="text-muted">Daftar member yang terdaftar di perpustakaan.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahMemberModal">
                <i class="fas fa-plus me-2"></i>Tambah Member
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-body">
            <form action="" method="post" class="d-flex justify-content-end mb-3">
                <div class="input-group" style="width: 300px;">
                    <input class="form-control" type="text" name="keyword" placeholder="Cari NPM atau Nama...">
                    <button class="btn btn-outline-secondary" type="submit" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-borderless table-hover table-custom text-center">
                    <thead>
                        <tr>
                            <th>NPM</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Jurusan</th>
                            <th>No Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($member as $item) : ?>
                        <tr>
                            <td><?=htmlspecialchars($item["npm"]);?></td>
                            <td><?=htmlspecialchars($item["nama"]);?></td>
                            <td><?=htmlspecialchars($item["jenis_kelamin"]);?></td>
                            <td><?=htmlspecialchars($item["jurusan"]);?></td>
                            <td><?=htmlspecialchars($item["no_tlp"]);?></td>
                            <td>
                                <a href="deleteMember.php?id=<?= $item["npm"]; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data member ini?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahMemberModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Form Tambah Member Baru</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="" method="post">
            <input type="hidden" name="tgl_pendaftaran" value="<?= date('Y-m-d'); ?>">
            <div class="row">
                <div class="col-md-6 mb-3"><label for="npm" class="form-label">NPM</label><input type="number" class="form-control" name="npm" id="npm" required></div>
                <div class="col-md-6 mb-3"><label for="nama" class="form-label">Nama Lengkap</label><input type="text" class="form-control" name="nama" id="nama" required></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label for="password" class="form-label">Password</label><input type="password" class="form-control" id="password" name="password" required></div>
                <div class="col-md-6 mb-3"><label for="confirmPw" class="form-label">Konfirmasi Password</label><input type="password" class="form-control" id="confirmPw" name="confirmPw" required></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label for="jenis_kelamin" class="form-label">Jenis Kelamin</label><select class="form-select" name="jenis_kelamin" id="jenis_kelamin" required><option value="" disabled selected>Pilih...</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
                <div class="col-md-6 mb-3"><label for="jurusan" class="form-label">Jurusan</label><input type="text" class="form-control" name="jurusan" id="jurusan" required></div>
            </div>
            <div class="mb-3"><label for="no_tlp" class="form-label">No. Telepon</label><input type="tel" class="form-control" name="no_tlp" id="no_tlp" required></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" name="tambah">Tambah Member</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>
</body>
</html>