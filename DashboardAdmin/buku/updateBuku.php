<?php
session_start();

if (!isset($_SESSION["signIn"])) {
    header("Location: ../../sign/admin/sign_in.php");
    exit;
}
require "../../config/config.php";

// Ambil data dari url dengan aman
if (!isset($_GET["id"])) {
    header("Location: daftarBuku.php");
    exit;
}
$idBuku = $_GET["id"];
// Menggunakan prepared statement untuk mengambil data awal (lebih aman)
$stmt = mysqli_prepare($connection, "SELECT * FROM buku WHERE id_buku = ?");
mysqli_stmt_bind_param($stmt, "s", $idBuku);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$buku = mysqli_fetch_assoc($result);


// Data kategori buku
$kategori = queryReadData("SELECT * FROM kategori_buku");

if (isset($_POST["update"])) {
    if (updateBuku($_POST) > 0) {
        echo "<script>
                alert('Data buku berhasil diupdate!');
                document.location.href = 'daftarBuku.php';
              </script>";
    } else {
        echo "<script>
                alert('Data buku gagal diupdate atau tidak ada perubahan!');
                document.location.href = 'daftarBuku.php';
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Data Buku | Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { width: 260px; background: #2c3e50; color: #ecf0f1; position: fixed; height: 100%; padding: 20px; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar-nav a { color: #bdc3c7; text-decoration: none; display: flex; align-items: center; padding: 12px 15px; border-radius: 8px; margin-bottom: 8px; transition: all 0.3s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background-color: #34495e; color: #ffffff; }
        .sidebar-nav a i { margin-right: 15px; width: 20px; text-align: center; }
        .main-content { margin-left: 260px; padding: 2.5rem; }
        .image-upload-box { border: 2px dashed #ccc; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: background 0.3s; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column; }
        .image-upload-box:hover { background-color: #f0f0f0; }
        .image-upload-box img { max-width: 100%; max-height: 250px; border-radius: 4px; object-fit: cover; }
        #cover[type="file"] { display: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="../dashboardAdmin.php" class="mb-4 d-block"><img src="../../assets/LogoPerpus.png" alt="logo" style="width: 150px;"></a>
    <nav class="sidebar-nav">
        <a href="../dashboardAdmin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="../member/member.php"><i class="fas fa-users"></i> Kelola Anggota</a>
        <a href="daftarBuku.php" class="active"><i class="fas fa-book"></i> Kelola Buku</a>
        <a href="../peminjaman/peminjamanBuku.php"><i class="fas fa-hand-holding-heart"></i> Peminjaman</a>
        <a href="../pengembalian/pengembalianBuku.php"><i class="fas fa-undo-alt"></i> Pengembalian</a>
        <a href="../denda/daftarDenda.php"><i class="fas fa-dollar-sign"></i> Kelola Denda</a>
    </nav>
</div>

<div class="main-content">
    <h1 class="h2 fw-bold">Edit Data Buku</h1>
    <p class="text-muted">Anda sedang mengubah detail untuk buku: <strong><?= htmlspecialchars($buku["judul"]); ?></strong></p>
    
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_buku" value="<?= htmlspecialchars($buku["id_buku"]); ?>">
                <input type="hidden" name="coverLama" value="<?= htmlspecialchars($buku["cover"]); ?>">

                <div class="row">
                    <div class="col-lg-4">
                        <label for="cover" class="form-label fw-bold">Cover Buku</label>
                        <label for="cover" class="image-upload-box">
                            <img id="imagePreview" src="../../imgDB/<?= htmlspecialchars($buku["cover"]); ?>" alt="Preview Gambar">
                            <p id="uploadText" class="text-muted small mt-2">Klik untuk ganti gambar</p>
                        </label>
                        <input type="file" id="cover" name="cover" accept="image/png, image/jpeg">
                    </div>

                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Buku</label>
                            <input type="text" class="form-control" id="judul" name="judul" value="<?= htmlspecialchars($buku["judul"]); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori" required>
                                <option value="<?= htmlspecialchars($buku["kategori"]); ?>" selected><?= htmlspecialchars($buku["kategori"]); ?></option>
                                <?php foreach ($kategori as $item) : ?>
                                    <?php if ($item["kategori"] != $buku["kategori"]) : ?>
                                        <option value="<?= htmlspecialchars($item["kategori"]); ?>"><?= htmlspecialchars($item["kategori"]); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="pengarang" class="form-label">Pengarang</label><input type="text" class="form-control" id="pengarang" name="pengarang" value="<?= htmlspecialchars($buku["pengarang"]); ?>" required></div>
                            <div class="col-md-6 mb-3"><label for="penerbit" class="form-label">Penerbit</label><input type="text" class="form-control" id="penerbit" name="penerbit" value="<?= htmlspecialchars($buku["penerbit"]); ?>" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="tahun_terbit" class="form-label">Tahun Terbit</label><input type="date" class="form-control" id="tahun_terbit" name="tahun_terbit" value="<?= htmlspecialchars($buku["tahun_terbit"]); ?>" required></div>
                            <div class="col-md-6 mb-3"><label for="jumlah_halaman" class="form-label">Jumlah Halaman</label><input type="number" class="form-control" id="jumlah_halaman" name="jumlah_halaman" value="<?= htmlspecialchars($buku["jumlah_halaman"]); ?>" required></div>
                        </div>
                        <div class="mb-3">
                            <label for="buku_deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="buku_deskripsi" name="buku_deskripsi" rows="3"><?= htmlspecialchars($buku["buku_deskripsi"]); ?></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="daftarBuku.php" class="btn btn-secondary">Batal</a>
                            <button type="submit" name="update" class="btn btn-primary">Update Data</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const coverInput = document.getElementById('cover');
    const imagePreview = document.getElementById('imagePreview');
    coverInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
</script>
</body>
</html>