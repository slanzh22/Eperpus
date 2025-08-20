<?php 
session_start();

if(!isset($_SESSION["signIn"]) ) {
  header("Location: ../../sign/admin/sign_in.php");
  exit;
}
require "../../config/config.php";

$kategori = queryReadData("SELECT * FROM kategori_buku");

if(isset($_POST["tambah"]) ) {
  if(tambahBuku($_POST) > 0) {
    echo "<script>
            alert('Data buku berhasil ditambahkan!');
            document.location.href = 'daftarBuku.php';
          </script>";
  } else {
    // Pesan error dari fungsi upload/tambah akan muncul dari config.php
    echo "<script>
            alert('Data buku gagal ditambahkan!');
          </script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Buku | Admin Dashboard</title>
    
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
        .image-upload-box {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .image-upload-box:hover { background-color: #f0f0f0; }
        .image-upload-box img { max-width: 100%; max-height: 250px; border-radius: 4px; object-fit: cover; }
        .image-upload-box .upload-text { color: #888; }
        #cover[type="file"] { display: none; } /* Sembunyikan input file asli */
    </style>
</head>
<body>

<div class="sidebar">
    <a href="../dashboardAdmin.php" class="mb-4">
        <img src="../../assets/LogoPerpus.png" alt="logo" style="width: 150px;">
    </a>
    <a href="../dashboardAdmin.php"><i class="fas fa-tachometer-alt fa-fw me-2"></i>Dashboard</a>
    <a href="../member/member.php"><i class="fas fa-users fa-fw me-2"></i>Kelola Member</a>
    <a href="daftarBuku.php" class="active"><i class="fas fa-book fa-fw me-2"></i>Kelola Buku</a>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold">Tambah Buku Baru</h1>
            <p class="text-muted">Isi detail buku dan unggah gambar cover.</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-4" data-aos="fade-right">
                        <label for="cover" class="form-label fw-bold">Cover Buku</label>
                        <label for="cover" class="image-upload-box">
                            <img id="imagePreview" src="#" alt="Preview Gambar" style="display:none;">
                            <div id="uploadText" class="upload-text">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-2"></i>
                                <p>Klik untuk memilih gambar</p>
                                <small>(JPG, PNG - Maks 2MB)</small>
                            </div>
                        </label>
                        <input type="file" id="cover" name="cover" accept="image/png, image/jpeg" required>
                    </div>

                    <div class="col-lg-8" data-aos="fade-left">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="judul" class="form-label">Judul Buku</label>
                                <input type="text" class="form-control" id="judul" name="judul" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="id_buku" class="form-label">ID Buku</label>
                                <input type="text" class="form-control" id="id_buku" name="id_buku" placeholder="Contoh: inf01" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori" required>
                                <option value="" selected disabled>Pilih Kategori...</option>
                                <?php foreach ($kategori as $item) : ?>
                                <option value="<?= htmlspecialchars($item["kategori"]); ?>"><?= htmlspecialchars($item["kategori"]); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pengarang" class="form-label">Pengarang</label>
                                <input type="text" class="form-control" id="pengarang" name="pengarang" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="penerbit" class="form-label">Penerbit</label>
                                <input type="text" class="form-control" id="penerbit" name="penerbit" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                                <input type="date" class="form-control" id="tahun_terbit" name="tahun_terbit" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_halaman" class="form-label">Jumlah Halaman</label>
                                <input type="number" class="form-control" id="jumlah_halaman" name="jumlah_halaman" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="buku_deskripsi" class="form-label">Deskripsi / Sinopsis</label>
                            <textarea class="form-control" id="buku_deskripsi" name="buku_deskripsi" rows="4"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="daftarBuku.php" class="btn btn-secondary">Batal</a>
                            <button type="submit" name="tambah" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Tambah Buku</button>
                        </div>
                    </div>
                </div>
            </form>
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

    // JavaScript untuk Preview Gambar Interaktif
    const coverInput = document.getElementById('cover');
    const imagePreview = document.getElementById('imagePreview');
    const uploadText = document.getElementById('uploadText');

    coverInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            
            uploadText.style.display = 'none'; // Sembunyikan teks
            imagePreview.style.display = 'block'; // Tampilkan gambar
            
            reader.addEventListener('load', function() {
                imagePreview.setAttribute('src', this.result);
            });
            
            reader.readAsDataURL(file);
        }
    });
</script>
</body>
</html>