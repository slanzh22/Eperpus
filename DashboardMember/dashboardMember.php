    <?php
    session_start();

    if (!isset($_SESSION["signInMember"])) { 
        header("Location: ../sign/member/sign_in.php");
        exit;
    }

    require "../config/config.php";

    // Ambil data member dari session
    $npm_member = $_SESSION['member']['npm'];
    $nama_member = $_SESSION['member']['nama'];

    // --- SEMUA QUERY YANG SUDAH DIPERBAIKI ---

    // 1. Data untuk Kartu Peminjaman Aktif (di atas)
    $pinjaman_aktif_card = queryReadData("
        SELECT p.*, b.judul, b.cover 
        FROM peminjaman p
        INNER JOIN buku b ON p.id_buku = b.id_buku
        WHERE p.npm = '$npm_member' AND p.id_peminjaman NOT IN (SELECT id_peminjaman FROM pengembalian)
        LIMIT 1
    ");

    // 2. Data untuk Kartu Denda Aktif (di atas)
    $denda_aktif_card = queryReadData("SELECT SUM(denda) as total_denda FROM pengembalian WHERE npm = '$npm_member' AND denda > 0");
    $total_denda = $denda_aktif_card[0]['total_denda'] ?? 0;

    // 3. Data untuk Tabel di Tab "Peminjaman Aktif"
    $dataPeminjam = queryReadData("
        SELECT p.*, b.judul, m.nama 
        FROM peminjaman p
        INNER JOIN buku b ON p.id_buku = b.id_buku
        INNER JOIN member m ON p.npm = m.npm
        WHERE p.npm = '$npm_member' AND p.id_peminjaman NOT IN (SELECT id_peminjaman FROM pengembalian)
        ORDER BY p.tgl_peminjaman DESC
    ");

    // 4. Data untuk Tabel di Tab "Riwayat Pengembalian"
    $dataPengembalian = queryReadData("
        SELECT pengembalian.*, buku.judul, member.nama FROM pengembalian
        INNER JOIN buku ON pengembalian.id_buku = buku.id_buku
        INNER JOIN member ON pengembalian.npm = member.npm
        WHERE pengembalian.npm = '$npm_member'
        ORDER BY pengembalian.buku_kembali DESC
    ");

    // 5. Data untuk Tabel di Tab "Denda Aktif"
    $dataDenda = queryReadData("
        SELECT pengembalian.id_pengembalian, buku.judul, member.nama, pengembalian.keterlambatan, pengembalian.denda
        FROM pengembalian
        INNER JOIN buku ON pengembalian.id_buku = buku.id_buku
        INNER JOIN member ON pengembalian.npm = member.npm
        WHERE pengembalian.npm = '$npm_member' AND pengembalian.denda > 0
        ORDER BY pengembalian.buku_kembali DESC
    ");

    // Ambil nama file saat ini untuk menandai link sidebar yang aktif
    $currentFile = basename($_SERVER['PHP_SELF']);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard | CupsLibs</title>
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/de8de52639.js"></script>
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
            body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
            .sidebar { width: 260px; background: linear-gradient(180deg, #b8dff9ff, #2980b9); color: #ecf0f1; position: fixed; height: 100%; padding: 20px; display: flex; flex-direction: column; }
            .sidebar a { color: #ecf0f1; text-decoration: none; display: block; padding: 10px 15px; border-radius: 8px; margin: 5px 0; transition: background 0.3s; }
            .sidebar a:hover, .sidebar a.active { background-color: #5dade2; }
            .sidebar-footer { margin-top: auto; }
            .main-content { margin-left: 260px; padding: 2.5rem; }
            .info-card { background-color: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
            .borrowed-book-card { display: flex; align-items: center; }
            .borrowed-book-card img { width: 70px; height: 100px; object-fit: cover; border-radius: 4px; margin-right: 1.5rem; }
            .table-custom thead th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; }
            .table-custom tbody tr:hover { background-color: #f1f1f1; }
            .table-custom td, .table-custom th { vertical-align: middle; }
            .nav-tabs .nav-link { color: #495057; }
            .nav-tabs .nav-link.active { color: #0d6efd; font-weight: 500; border-color: #dee2e6 #dee2e6 #fff; }

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
        
        <div class="mb-4" data-aos="fade-down">
            <h1 class="h2 fw-bold">Selamat Datang, <span class="text-capitalize"><?= htmlspecialchars($nama_member); ?>!</span></h1>
            <p class="text-muted"><?= strftime('%A, %d %B %Y'); ?></p>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-md-6" data-aos="fade-right">
                <div class="info-card h-100">
                    <h4 class="mb-3">Buku yang Sedang Dipinjam</h4>
                    <?php if (!empty($pinjaman_aktif_card)) : $pinjaman = $pinjaman_aktif_card[0]; ?>
                        <div class="borrowed-book-card">
                            <img src="/perpustakaan/perpustakaan/imgDB/<?= htmlspecialchars($pinjaman['cover']); ?>" alt="Cover Buku">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($pinjaman['judul']); ?></h5>
                                <p class="text-muted mb-1">Harus kembali sebelum:</p>
                                <p class="fw-bold"><?= date('d F Y', strtotime($pinjaman['tgl_pengembalian'])); ?></p>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="text-center text-muted py-4"><i class="fas fa-book-reader fa-3x mb-3"></i><p>Anda sedang tidak meminjam buku.</p></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <div class="info-card h-100 text-center">
                    <i class="fas fa-dollar-sign fa-3x text-white bg-danger p-3 rounded-circle mb-3"></i>
                    <h5>Total Denda Aktif</h5>
                    <h2 class="display-5 fw-bold">Rp <?= number_format($total_denda, 0, ',', '.'); ?></h2>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist" data-aos="fade-up">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="peminjaman-tab" data-bs-toggle="tab" data-bs-target="#peminjaman-pane" type="button" role="tab">Peminjaman Aktif</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pengembalian-tab" data-bs-toggle="tab" data-bs-target="#pengembalian-pane" type="button" role="tab">Riwayat Pengembalian</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="denda-tab" data-bs-toggle="tab" data-bs-target="#denda-pane" type="button" role="tab">Denda Aktif</button>
            </li>
        </ul>
        <div class="tab-content card border-top-0 rounded-bottom-3 shadow-sm" id="myTabContent" data-aos="fade-up" data-aos-delay="100">
            <div class="tab-pane fade show active p-4" id="peminjaman-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover table-custom">
                        <thead><tr><th>Judul Buku</th><th>Tgl Pinjam</th><th>Tenggat Kembali</th><th class="text-center">Status</th></tr></thead>
                        <tbody>
                            <?php if(empty($dataPeminjam)) echo '<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada peminjaman aktif.</td></tr>'; ?>
                            <?php foreach ($dataPeminjam as $item) : ?>
                            <tr>
                                <td><?= htmlspecialchars($item["judul"]); ?></td>
                                <td><?= date('d M Y', strtotime($item["tgl_peminjaman"])); ?></td>
                                <td><?= date('d M Y', strtotime($item["tgl_pengembalian"])); ?></td>
                                <td class="text-center"><span class="badge <?= (strtotime($item['tgl_pengembalian']) < time()) ? 'bg-danger' : 'bg-warning text-dark'; ?>"><?= (strtotime($item['tgl_pengembalian']) < time()) ? 'Terlambat' : 'Dipinjam'; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade p-4" id="pengembalian-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover table-custom">
                        <thead><tr><th>Judul Buku</th><th>Tgl Kembali</th><th>Denda</th><th class="text-center">Status Denda</th></tr></thead>
                        <tbody>
                            <?php if(empty($dataPengembalian)) echo '<tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat pengembalian.</td></tr>'; ?>
                            <?php foreach ($dataPengembalian as $item) : ?>
                            <tr>
                                <td><?= htmlspecialchars($item["judul"]); ?></td>
                                <td><?= date('d M Y', strtotime($item["buku_kembali"])); ?></td>
                                <td>Rp <?= number_format($item["denda"], 0, ',', '.'); ?></td>
                                <td class="text-center"><span class="badge <?= ((int)$item['denda'] > 0) ? 'bg-danger' : 'bg-success'; ?>"><?= ((int)$item['denda'] > 0) ? 'Belum Lunas' : 'Lunas'; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade p-4" id="denda-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover table-custom">
                        <thead><tr><th>Judul Buku</th><th>Keterlambatan</th><th>Jumlah Denda</th><th class="text-center">Aksi</th></tr></thead>
                        <tbody>
                            <?php if(empty($dataDenda)) echo '<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada denda aktif.</td></tr>'; ?>
                            <?php foreach ($dataDenda as $item) : ?>
                            <tr>
                                <td><?= htmlspecialchars($item["judul"]); ?></td>
                                <td><?= htmlspecialchars($item["keterlambatan"]); ?> Hari</td>
                                <td class="fw-bold text-danger">Rp <?= number_format($item["denda"], 0, ',', '.'); ?></td>
                                <td class="text-center"><a href="formPeminjaman/formBayarDenda.php?id=<?= $item['id_pengembalian']; ?>" class="btn btn-sm btn-warning">Bayar Denda</a></td>
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