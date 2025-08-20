<?php
session_start();

if(!isset($_SESSION["signIn"]) ) {
  header("Location: ../../sign/admin/sign_in.php");
  exit;
}
require "../../config/config.php";

// --- LOGIKA UNTUK PROSES FORM TAMBAH PEMINJAMAN ---
if(isset($_POST["pinjam"])) {
    // Diasumsikan Anda memiliki fungsi peminjamanManual() di config.php
    if(peminjamanManual($_POST) > 0) {
        echo "<script>
                alert('Peminjaman baru berhasil ditambahkan!');
                document.location.href = 'peminjamanBuku.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan peminjaman!');
                document.location.href = 'peminjamanBuku.php';
              </script>";
    }
}

// Query untuk menampilkan daftar peminjaman di tabel
$dataPeminjam = queryReadData("
    SELECT 
        peminjaman.id_peminjaman, buku.judul, member.nama, peminjaman.npm, 
        peminjaman.tgl_peminjaman, peminjaman.tgl_pengembalian,
        pengembalian.id_peminjaman AS status_kembali
    FROM peminjaman
    INNER JOIN member ON peminjaman.npm = member.npm
    INNER JOIN buku ON peminjaman.id_buku = buku.id_buku
    LEFT JOIN pengembalian ON peminjaman.id_peminjaman = pengembalian.id_peminjaman
    ORDER BY peminjaman.tgl_peminjaman DESC
");

$currentFile = basename($_SERVER['PHP_SELF']);
$id_admin_login = $_SESSION['admin']['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Peminjaman | Admin Dashboard</title>
    
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
        .table-custom tbody tr:hover { background-color: #f1f1f1; }
        .table-custom td, .table-custom th { vertical-align: middle; }
        .member-name { display: block; font-weight: 500; }
        .member-npm { font-size: 0.85rem; color: #6c757d; }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="../dashboardAdmin.php" class="mb-4 d-block"><img src="../../assets/LogoPerpus.png" alt="logo" style="width: 150px;"></a>
    <nav class="sidebar-nav">
        <a href="../dashboardAdmin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="../member/member.php"><i class="fas fa-users"></i> Kelola Anggota</a>
        <a href="../buku/daftarBuku.php"><i class="fas fa-book"></i> Kelola Buku</a>
        <a href="peminjamanBuku.php" class="active"><i class="fas fa-hand-holding-heart"></i> Peminjaman</a>
        <a href="../pengembalian/pengembalianBuku.php"><i class="fas fa-undo-alt"></i> Pengembalian</a>
        <a href="../denda/daftarDenda.php"><i class="fas fa-dollar-sign"></i> Kelola Denda</a>
    </nav>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="h2 fw-bold">Kelola Peminjaman Buku</h1>
            <p class="text-muted">Monitor dan kelola semua transaksi peminjaman buku.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPeminjamanModal">
                <i class="fas fa-plus me-2"></i>Peminjaman Baru
            </button>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Judul Buku</th>
                            <th>Peminjam</th>
                            <th>Tgl Pinjam</th>
                            <th>Tenggat Kembali</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataPeminjam as $item) : ?>
                            <?php
                                $isReturned = !is_null($item['status_kembali']);
                                $isOverdue = !$isReturned && (strtotime($item['tgl_pengembalian']) < time());
                                $statusText = $isReturned ? 'Dikembalikan' : ($isOverdue ? 'Terlambat' : 'Dipinjam');
                                $badgeClass = $isReturned ? 'bg-success' : ($isOverdue ? 'bg-danger' : 'bg-warning text-dark');
                            ?>
                        <tr>
                            <td><?= htmlspecialchars($item["judul"]); ?></td>
                            <td>
                                <span class="member-name"><?= htmlspecialchars($item["nama"]); ?></span>
                                <span class="member-npm"><?= htmlspecialchars($item["npm"]); ?></span>
                            </td>
                            <td><?= date('d M Y', strtotime($item["tgl_peminjaman"])); ?></td>
                            <td><?= date('d M Y', strtotime($item["tgl_pengembalian"])); ?></td>
                            <td class="text-center"><span class="badge <?= $badgeClass; ?>"><?= $statusText; ?></span></td>
                            <td class="text-center">
                                <?php if (!$isReturned) : ?>
                                    <a href="../pengembalian/pengembalianBuku.php?id=<?= $item['id_peminjaman']; ?>" class="btn btn-sm btn-primary">Proses Pengembalian</a>
                                <?php else : ?>
                                    <span class="text-muted">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahPeminjamanModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Form Peminjaman Manual</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="" method="post" id="peminjamanForm">
            <input type="hidden" name="id_admin" value="<?= $id_admin_login; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="id_buku" class="form-label">ID Buku</label>
                    <input type="text" class="form-control" id="id_buku" name="id_buku" placeholder="Contoh: inf01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="npm" class="form-label">NPM Member</label>
                    <input type="number" class="form-control" id="npm" name="npm" placeholder="Masukkan NPM member" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tgl_peminjaman" class="form-label">Tanggal Peminjaman</label>
                    <input type="date" class="form-control" id="tgl_peminjaman" name="tgl_peminjaman" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tgl_pengembalian" class="form-label">Tenggat Pengembalian</label>
                    <input type="date" class="form-control" id="tgl_pengembalian" name="tgl_pengembalian" readonly>
                </div>
            </div>
            <div class="alert alert-info mt-2">Tenggat pengembalian diatur otomatis 7 hari setelah tanggal pinjam.</div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="pinjam" form="peminjamanForm" class="btn btn-primary">Tambah Peminjaman</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });

    document.getElementById('tgl_peminjaman').addEventListener('change', function() {
        if (this.value) {
            let tglPinjam = new Date(this.value);
            tglPinjam.setDate(tglPinjam.getDate() + 7);
            document.getElementById('tgl_pengembalian').value = tglPinjam.toISOString().split('T')[0];
        }
    });
</script>
</body>
</html>