<?php
session_start();

if (isset($_SESSION["signIn"])) {
    header("Location: ../../DashboardAdmin/dashboardAdmin.php");
    exit;
}

require "../../loginSystem/connect.php"; // Pastikan path ini benar

$error = false;
if (isset($_POST["signIn"])) {
    // Menggunakan nama_admin sesuai dengan input form dan kolom database
    $nama_admin = strtolower($_POST["nama_admin"]); 
    $password = $_POST["password"];

    // Query menggunakan 'nama_admin'
    $stmt = mysqli_prepare($connect, "SELECT id, nama_admin, password FROM admin WHERE nama_admin = ?");
    mysqli_stmt_bind_param($stmt, "s", $nama_admin);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // === PERUBAHAN DI SINI: Membandingkan password sebagai teks biasa (tidak aman) ===
        if ($password === $row["password"]) {
            // SET SESSION
            $_SESSION["signIn"] = true;
            $_SESSION["admin"]["id"] = $row['id'];
            $_SESSION["admin"]["nama_admin"] = $row['nama_admin'];
            header("Location: ../../DashboardAdmin/dashboardAdmin.php");
            exit;
        }
    }
    // Jika query gagal atau password tidak cocok
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In | Admin CupsLibs</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }
        .login-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }
        .background-section {
            flex: 1.2;
            background: url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=1854&auto=format&fit=crop') no-repeat center center;
            background-size: cover;
            position: relative;
        }
        .background-section::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.6), rgba(0,0,0,0.2));
        }
        .form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f2f5;
        }
        .login-card {
            width: 26rem;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeIn 0.8s ease-in-out;
        }
        .logo { max-width: 60px; margin-bottom: 1rem; }
        .form-control {
            background-color: rgba(255, 255, 255, 0.5);
            border: 1px solid #ced4da;
            padding: 0.8rem 1rem;
            border-radius: 8px;
        }
        .form-control:focus {
            background-color: #fff;
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn-primary {
            background-color: #2c3e50;
            border: none;
            padding: 0.8rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #34495e;
            transform: translateY(-2px);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @media (max-width: 992px) {
            .background-section { display: none; }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="background-section"></div>
    <div class="form-section">
        <div class="login-card">
            <div class="text-center mb-4">
                <img src="../../assets/adminLogo.png" class="logo" alt="adminLogo">
                <h2 class="fw-bold">Admin Sign In</h2>
                <p class="text-muted">Selamat datang kembali, silakan masuk.</p>
            </div>
            
            <?php if(isset($error) && $error === true) : ?>
                <div class="alert alert-danger" role="alert">Nama Admin atau Password Salah!</div>
            <?php endif; ?>

            <form action="" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="nama_admin" class="form-label">Nama Admin</label>
                    <input type="text" class="form-control" name="nama_admin" id="nama_admin" required>
                    <div class="invalid-feedback">
                        Masukkan Nama Admin Anda!
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="invalid-feedback">
                        Masukkan Password Anda!
                    </div>
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary" type="submit" name="signIn">Sign In</button>
                </div>
            </form>
            <div class="text-center mt-4">
                <a class="text-decoration-none" href="../link_login.html">Bukan Admin?</a>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>