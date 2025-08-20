<?php 
require "../../loginSystem/connect.php";
if(isset($_POST["signUp"]) ) {
  if(signUp($_POST) > 0) {
    echo "<script>
            alert('Sign Up berhasil! Silakan login.');
            document.location.href = 'sign_in.php';
          </script>";
  } else {
    // Pesan error lebih spesifik sudah ada di dalam fungsi signUp
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up | Member CupsLibs</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body, html {
            height: 100%; margin: 0; font-family: 'Poppins', sans-serif;
        }
        .signup-container {
            display: flex; min-height: 100vh; width: 100%;
        }
        .background-section {
            flex: 1.2;
            background: url('https://images.unsplash.com/photo-1532012197267-da84d127e765?q=80&w=1887&auto=format&fit=crop') no-repeat center center;
            background-size: cover; position: relative;
        }
        .background-section::after {
            content: 'Join Our Community';
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.7), rgba(0,0,0,0.3));
            color: white; font-size: 3rem; font-weight: 600;
            display: flex; align-items: center; justify-content: center; text-align: center; padding: 2rem;
        }
        .form-section {
            flex: 1; display: flex; align-items: center; justify-content: center;
            background-color: #f0f2f5; padding: 2rem 0;
        }
        .signup-card {
            width: 95%; max-width: 42rem; padding: 2.5rem; background: rgba(255, 255, 255, 0.9);
            border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeIn 0.8s ease-in-out;
            max-height: 95vh; overflow-y: auto;
        }
        .form-control, .form-select {
            border: 1px solid #ced4da; padding: 0.75rem 1rem; border-radius: 8px;
        }
        .btn-primary {
            background-color: #27ae60; border: none; padding: 0.8rem;
            border-radius: 8px; font-weight: 500; transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #229954; transform: translateY(-2px);
        }
        .form-section-title {
            font-weight: 600;
            color: #34495e;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @media (max-width: 992px) {
            .background-section { display: none; }
            .form-section { padding: 1rem; }
            .signup-card { width: 100%; max-height: 100vh; }
        }
    </style>
</head>
<body>

<div class="signup-container">
    <div class="background-section"></div>
    <div class="form-section">
        <div class="signup-card">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Buat Akun Member</h2>
                <p class="text-muted">Lengkapi data diri Anda di bawah ini.</p>
            </div>
            
            <form action="" method="post" class="needs-validation" novalidate>
                <input type="hidden" name="tgl_pendaftaran" value="<?= date('Y-m-d'); ?>">
                
                <h5 class="form-section-title">Informasi Akun</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="npm" class="form-label">NPM</label>
                        <input type="number" class="form-control" name="npm" id="npm" placeholder="Masukkan NPM Anda" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan Nama Lengkap" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirmPw" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="confirmPw" name="confirmPw" required>
                    </div>
                </div>

                <h5 class="form-section-title">Data Diri</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select class="form-select" name="jenis_kelamin" id="jenis_kelamin" required>
                            <option value="" disabled selected>Pilih...</option>
                            <option value="Laki laki">Laki-Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jurusan" class="form-label">Jurusan</label>
                        <select class="form-select" id="jurusan" name="jurusan" required>
                            <option value="" disabled selected>Pilih Jurusan...</option>
                            <option value="Sistem Informasi">Sistem Informasi</option>
                            <option value="Teknik Informatika">Teknik Informatika</option>
                            <option value="Teknik Otomotif">Teknik Otomotif</option>
                            <option value="Management Informatika">Management Informatika</option>
                            <option value="Teknik Elektro">Teknik Elektro</option>
                            <option value="Teknik Industri">Teknik Industri</option>
                        </select>
                    </div>
                </div>

                <h5 class="form-section-title">Kontak</h5>
                <div class="mb-4">
                    <label for="no_tlp" class="form-label">No. Telepon</label>
                    <input type="tel" class="form-control" name="no_tlp" id="no_tlp" placeholder="Contoh: 08123456789" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="submit" name="signUp">Daftar Akun</button>
                    <input type="reset" class="btn btn-outline-secondary" value="Reset Form">
                </div>
            </form>
            <div class="text-center mt-4">
                <p class="text-muted">Sudah punya akun? <a href="sign_in.php" class="text-decoration-none fw-bold text-primary">Login di sini</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
      'use strict'
      const forms = document.querySelectorAll('.needs-validation')
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
    })()
</script>
</body>
</html>