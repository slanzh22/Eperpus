<?php
session_start();

if (!isset($_SESSION["signInMember"])) {
    header("Location: ../../sign/member/sign_in.php");
    exit;
}
require "../../config/config.php";

// Amankan ID Peminjaman dari URL
if (!isset($_GET["id"])) {
    header("Location: TransaksiPeminjaman.php");
    exit;
}
$idPeminjaman = $_GET["id"];

// Gunakan prepared statement untuk mengambil data peminjaman
$stmt = mysqli_prepare($connection, "
    SELECT peminjaman.*, buku.judul, buku.cover, member.nama 
    FROM peminjaman
    INNER JOIN buku ON peminjaman.id_buku = buku.id_buku
    INNER JOIN member ON peminjaman.npm = member.npm
    WHERE peminjaman.id_peminjaman = ?
");
mysqli_stmt_bind_param($stmt, "i", $idPeminjaman);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    echo "Data peminjaman tidak ditemukan.";
    exit;
}

// === PERHITUNGAN DENDA OTOMATIS DI PHP (SERVER-SIDE) ===
$tgl_kembali_seharusnya = new DateTime($item["tgl_pengembalian"]);
$tgl_kembali_sekarang = new DateTime(date('Y-m-d'));
$keterlambatan = 0;
if ($tgl_kembali_sekarang > $tgl_kembali_seharusnya) {
    $diff = $tgl_kembali_seharusnya->diff($tgl_kembali_sekarang);
    $keterlambatan = $diff->days;
}
$denda_per_hari = 1000; // Misal denda Rp 1.000 per hari
$total_denda = $keterlambatan * $denda_per_hari;


// Proses form saat tombol "Kembalikan" diklik
if (isset($_POST["kembalikan"])) {
    // Kumpulkan data yang akan dikirim ke fungsi pengembalian()
    $dataPengembalian = [
        "id_peminjaman" => $item["id_peminjaman"],
        "id_buku" => $item["id_buku"],
        "npm" => $item["npm"],
        "id_admin" => $item["id_admin"],
        "tgl_pengembalian" => $item["tgl_pengembalian"], // Tenggat
        "buku_kembali" => date('Y-m-d'), // Tanggal kembali aktual
        "keterlambatan" => $keterlambatan,
        "denda" => $total_denda
    ];
    
    if (pengembalian($dataPengembalian) > 0) {
        echo "<script>
                alert('Terima kasih telah mengembalikan buku!');
                document.location.href = '../dashboardMember.php';
              </script>";
    } else {
        echo "<script>
                alert('Buku gagal dikembalikan, terjadi kesalahan.');
                history.back();
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
    <title>Form Pengembalian Buku | CupsLibs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { width: 260px; background: linear-gradient(180deg, #3498db, #2980b9); color: #ecf0f1; position: fixed; height: 100%; padding: 20px; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar a { color: #ecf0f1; text-decoration: none; display: block; padding: 10px 15px; border-radius: 8px; margin: 5px 0; transition: background 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #5dade2; }
        .sidebar-footer { margin-top: auto; }
        .main-content { margin-left: 260px; padding: 2.5rem; }
        .summary-card { border: none; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .book-cover { width: 150px; height: 220px; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .summary-item { display: flex; justify-content: space-between; padding: 0.85rem 0; border-bottom: 1px solid #eee; }
        .summary-item:last-child { border-bottom: none; }
        .summary-label { color: #6c757d; }
        .summary-value { font-weight: 500; }
        .denda-final { background-color: #fff3cd; border: 1px solid #ffe69c; }
    </style>
</head>
<body>

<div class="sidebar">
    <div>
        <a href="/perpustakaan/perpustakaan/DashboardMember/dashboardMember.php" class="mb-4 d-block"><img src="/perpustakaan/perpustakaan/assets/LogoPerpus.png" alt="logo" style="width: 150px;"></a>
        <a href="/perpustakaan/perpustakaan/DashboardMember/dashboardMember.php">...Dashboard</a>
        <a href="/perpustakaan/perpustakaan/DashboardMember/buku/daftarBuku.php">...Daftar Buku</a>
        <a href="/perpustakaan/perpustakaan/DashboardMember/formPeminjaman/TransaksiPeminjaman.php">...Peminjaman Saya</a>
        <a href="/perpustakaan/perpustakaan/DashboardMember/formPeminjaman/TransaksiPengembalian.php" class="active">...Pengembalian Saya</a>
    </div>
    </div>

<div class="main-content">
    <div data-aos="fade-down">
        <h1 class="h2 fw-bold">Konfirmasi Pengembalian</h1>
        <p class="text-muted">Pastikan buku dan data di bawah ini sudah sesuai.</p>
    </div>

    <form action="" method="post">
        <div class="row g-4 mt-2">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="card summary-card h-100">
                    <div class="card-body text-center">
                        <h4 class="mb-4">Buku yang Dikembalikan</h4>
                        <img src="/perpustakaan/perpustakaan/imgDB/<?= htmlspecialchars($item["cover"]); ?>" alt="Cover Buku" class="book-cover mb-3">
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($item["judul"]); ?></h5>
                        <p class="text-muted">ID Peminjaman: <?= htmlspecialchars($item["id_peminjaman"]); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7" data-aos="fade-left">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <h4 class="mb-4 text-center">Rincian Pengembalian</h4>
                        
                        <div class="summary-item">
                            <span class="summary-label">Nama Peminjam</span>
                            <span class="summary-value text-capitalize"><?= htmlspecialchars($item["nama"]); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Tanggal Pinjam</span>
                            <span class="summary-value"><?= date("d F Y", strtotime($item["tgl_peminjaman"])); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Tenggat Pengembalian</span>
                            <span class="summary-value"><?= date("d F Y", strtotime($item["tgl_pengembalian"])); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Tanggal Kembali Hari Ini</span>
                            <span class="summary-value fw-bold"><?= date("d F Y"); ?></span>
                        </div>
                        
                        <div class="p-3 mt-3 rounded denda-final text-center">
                            <span class="summary-label d-block">Keterlambatan</span>
                            <h3 class="fw-bold mb-0"><?= $keterlambatan; ?> Hari</h3>
                            <hr>
                            <span class="summary-label d-block">Total Denda</span>
                            <h2 class="fw-bold text-danger mb-0">Rp <?= number_format($total_denda, 0, ',', '.'); ?></h2>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="TransaksiPeminjaman.php" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-success" name="kembalikan">
                                <i class="fas fa-check-circle me-2"></i>Konfirmasi Pengembalian
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