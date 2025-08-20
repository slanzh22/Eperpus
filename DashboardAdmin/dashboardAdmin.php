<?php
session_start();
// 1. KONEKSI KE DATABASE
require __DIR__ . '/../loginSystem/connect.php';

// Cek jika user belum login
if(!isset($_SESSION["signIn"]) ) {
  header("Location: ../sign/admin/sign_in.php");
  exit;
}

// Total Buku
$queryBuku = mysqli_query($connect, "SELECT COUNT(*) as total_buku FROM buku");
$totalBuku = mysqli_fetch_assoc($queryBuku)['total_buku'];

// Total Anggota
$queryAnggota = mysqli_query($connect, "SELECT COUNT(*) as total_anggota FROM member");
$totalAnggota = mysqli_fetch_assoc($queryAnggota)['total_anggota'];

// Buku yang Sedang Dipinjam
$queryPinjam = mysqli_query($connect, "SELECT COUNT(*) as total_pinjam FROM peminjaman WHERE tgl_pengembalian IS NULL");
$bukuDipinjam = mysqli_fetch_assoc($queryPinjam)['total_pinjam'];

// Denda Aktif
$queryDenda = mysqli_query($connect, "SELECT COUNT(*) as total_denda FROM pengembalian WHERE denda > 0");
$dendaAktif = mysqli_fetch_assoc($queryDenda)['total_denda'];

// Data untuk Grafik
$queryChart = mysqli_query($connect, "
    SELECT DATE_FORMAT(tgl_peminjaman, '%b') as bulan, COUNT(*) as jumlah 
    FROM peminjaman 
    WHERE tgl_peminjaman >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
    GROUP BY bulan 
    ORDER BY MIN(tgl_peminjaman)
");

$chartLabels = [];
$chartData = [];
if ($queryChart) {
    while($row = mysqli_fetch_assoc($queryChart)) {
        $chartLabels[] = $row['bulan'];
        $chartData[] = $row['jumlah'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard | CupsLibs</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/de8de52639.js"></script>
  
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; display: flex; min-height: 100vh; }
    .sidebar { width: 260px; background: #2c3e50; color: #ecf0f1; position: fixed; height: 100%; padding: 20px; display: flex; flex-direction: column; transition: all 0.3s; }
    .sidebar-header { text-align: center; margin-bottom: 30px; }
    .sidebar-header img { max-width: 150px; }
    .sidebar-nav a { color: #bdc3c7; text-decoration: none; display: flex; align-items: center; padding: 12px 15px; border-radius: 8px; margin-bottom: 8px; transition: all 0.3s; }
    .sidebar-nav a:hover, .sidebar-nav a.active { background-color: #34495e; color: #ffffff; }
    .sidebar-nav a i { margin-right: 15px; width: 20px; text-align: center; }
    .sidebar-footer { margin-top: auto; text-align: center; font-size: 0.8rem; color: #7f8c8d;}
    .main-content { margin-left: 260px; flex-grow: 1; padding: 2.5rem; transition: all 0.3s; }
    .header { display: flex; justify-content: space-between; align-items: center; background-color: #ffffff; padding: 1rem 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 2rem; }
    .header-greeting h1 { font-size: 1.5rem; font-weight: 600; margin: 0; color: #2c3e50;}
    .header-greeting p { margin: 0; color: #6c757d; }
    .profile-dropdown .dropdown-toggle::after { display: none; }
    .profile-dropdown img { width: 45px; height: 45px; object-fit: cover; border-radius: 50%; }
    
    /* === CSS BARU UNTUK ANIMASI KARTU === */
    .stat-card {
        background-color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: bounceIn 0.8s ease forwards;
        opacity: 0; /* Mulai dari transparan */
    }
    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }
    .stat-card .icon {
        font-size: 2.2rem;
        padding: 20px;
        border-radius: 12px;
        margin-right: 20px;
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        transition: transform 0.3s ease;
    }
    .stat-card:hover .icon {
        transform: rotate(-15deg);
    }
    .stat-card h5 { margin-bottom: 5px; font-weight: 500; color: #6c757d; font-size: 0.9rem; text-transform: uppercase;}
    .stat-card p { margin: 0; font-size: 2rem; font-weight: 700; color: #2c3e50; }
    
    /* Delay animasi untuk setiap kartu */
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }

    @keyframes bounceIn {
        0% { opacity: 0; transform: scale(0.3); }
        50% { opacity: 1; transform: scale(1.05); }
        70% { transform: scale(0.9); }
        100% { opacity: 1; transform: scale(1); }
    }
    /* === BATAS CSS BARU === */
    
    .data-container { background-color: #ffffff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .data-container h3 { font-weight: 600; margin-bottom: 1.5rem; color: #2c3e50;}
  </style>
</head>
<body>
  
  <div class="sidebar">
    <div class="sidebar-header">
      <img src="../assets/LogoPerpus.png" alt="logo">
    </div>
    <nav class="sidebar-nav">
      <a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="member/member.php"><i class="fas fa-users"></i> Kelola Anggota</a>
      <a href="buku/daftarBuku.php"><i class="fas fa-book"></i> Kelola Buku</a>
      <a href="peminjaman/peminjamanBuku.php"><i class="fas fa-hand-holding-heart"></i> Peminjaman</a>
      <a href="pengembalian/pengembalianBuku.php"><i class="fas fa-undo-alt"></i> Pengembalian</a>
      <a href="denda/daftarDenda.php"><i class="fas fa-dollar-sign"></i> Kelola Denda</a>
    </nav>
    <div class="sidebar-footer">
      <p>CupsLibs &copy; 2025</p>
    </div>
  </div>

  <div class="main-content">
    <header class="header">
        <div class="header-greeting">
            <h1>Selamat Datang, <span class="text-capitalize"><?php echo htmlspecialchars($_SESSION['admin']['nama_admin']); ?></span>!</h1>
            <p><?php setlocale(LC_TIME, 'id_ID.UTF-8'); echo strftime('%A, %d %B %Y'); ?></p>
        </div>
        <div class="dropdown profile-dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="../assets/adminLogo.png" alt="adminLogo">
            </button>
            <ul class="dropdown-menu dropdown-menu-end mt-2 p-2 shadow-lg border-0" style="width: 250px;">
                <li class="d-flex flex-column align-items-center mb-2">
                    <img src="../assets/adminLogo.png" alt="adminLogo" width="60px" class="mb-2">
                    <h6 class="mb-0 text-capitalize"><?php echo htmlspecialchars($_SESSION['admin']['nama_admin']); ?></h6>
                    <small class="text-muted">Administrator</small>
                </li>
                <hr class="my-1">
                <li><a class="dropdown-item text-danger text-center p-2 mt-2 bg-danger-subtle rounded" href="signOut.php"><i class="fa-solid fa-right-to-bracket me-2"></i>Sign Out</a></li>
            </ul>
        </div>
    </header>
    
    <div class="row g-4 mb-4">
      <div class="col-xl-3 col-md-6">
        <div class="stat-card">
          <div class="icon" style="background-color: #4e73df;"><i class="fas fa-book"></i></div>
          <div><h5>Total Buku</h5><p><?php echo $totalBuku; ?></p></div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="stat-card">
          <div class="icon" style="background-color: #1cc88a;"><i class="fas fa-users"></i></div>
          <div><h5>Total Anggota</h5><p><?php echo $totalAnggota; ?></p></div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="stat-card">
          <div class="icon" style="background-color: #f6c23e;"><i class="fas fa-hand-holding-heart"></i></div>
          <div><h5>Buku Dipinjam</h5><p><?php echo $bukuDipinjam; ?></p></div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="stat-card">
          <div class="icon" style="background-color: #e74a3b;"><i class="fas fa-dollar-sign"></i></div>
          <div><h5>Denda Aktif</h5><p><?php echo $dendaAktif; ?></p></div>
        </div>
      </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="data-container">
                <h3>Grafik Peminjaman Buku (6 Bulan Terakhir)</h3>
                <canvas id="loanChart"></canvas>
            </div>
        </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('loanChart');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
          label: 'Jumlah Peminjaman',
          data: <?php echo json_encode($chartData); ?>,
          backgroundColor: '#4e73df',
          borderColor: '#4e73df',
          borderWidth: 1,
          borderRadius: 5,
          tension: 0.4 // Membuat garis sedikit melengkung jika tipe 'line'
        }]
      },
      options: {
        responsive: true,
        scales: { 
            y: { 
                beginAtZero: true,
                grid: {
                    drawBorder: false,
                }
            },
            x: {
                grid: {
                    display: false,
                }
            }
        },
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: '#2c3e50',
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 },
                padding: 10,
                cornerRadius: 8,
            }
        },
        // === PENAMBAHAN ANIMASI GRAFIK ===
        animation: {
            duration: 1000,
            easing: 'easeInOutQuart',
            onComplete: () => {
                console.log('Chart animation complete');
            }
        }
      }
    });
  </script>
</body>
</html>