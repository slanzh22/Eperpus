<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CupsLibs - Perpustakaan Digital Modern</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de8de52639.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="icon" href="assets/logoUrl.png" type="image/png">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@400;500&display=swap');
        
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --background-light: #f8f9fa;
            --background-white: #ffffff;
            --text-dark: #212529;
            --text-light: #f8f9fa;
        }

        body { font-family: 'Roboto', sans-serif; }
        h1, h2, h3, h4, h5, h6, .navbar-brand, .nav-link, .btn { font-family: 'Poppins', sans-serif; }
        .navbar { transition: background-color 0.4s ease-in-out, box-shadow 0.4s ease-in-out; }
        .navbar-transparent { background-color: transparent !important; }
        .navbar-scrolled { background-color: var(--background-white) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-nav .nav-link { position: relative; padding: 0.5rem 0; margin: 0 1rem; color: var(--text-dark); font-weight: 500; }
        .navbar-nav .nav-link::after { content: ''; position: absolute; bottom: -5px; left: 0; width: 0; height: 2px; background-color: var(--primary-color); transition: width 0.3s ease-in-out; }
        .navbar-nav .nav-link:hover::after, .navbar-nav .nav-link.active::after { width: 100%; }
        
        .hero-section {
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.8)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=2100&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            padding: 120px 0;
        }
        
        .section-title { margin-bottom: 4rem; }
        .section-title h2 { font-weight: 700; }
        .section-title p { max-width: 600px; margin: auto; }

        .book-card { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.08); transition: all 0.3s ease; }
        .book-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); }
        .book-card img { height: 300px; object-fit: cover; }
        
        .step-card { text-align: center; padding: 2rem; }
        .step-icon { width: 80px; height: 80px; background-color: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; font-size: 2rem; }

        .testimonial-card { background-color: var(--background-white); border-radius: 12px; padding: 2rem; box-shadow: 0 8px 24px rgba(0,0,0,0.05); text-align: center; }
        .testimonial-card img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin: -60px auto 1rem auto; border: 4px solid var(--background-white); }
        .testimonial-card .name { font-weight: 600; }
    </style>
</head>
<body>

    <nav class="navbar fixed-top navbar-expand-lg navbar-light">
        <div class="container">
            <a href="#homeSection"><img src="assets/LogoPerpus.png" alt="logo" width="120px"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#homeSection">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#aboutSection">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#popularBooksSection">Buku Populer</a></li>
                    <li class="nav-item"><a class="nav-link" href="#howToSection">Cara Pinjam</a></li>
                    <li class="nav-item"><a class="nav-link" href="#testimonialSection">Testimoni</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="homeSection" class="hero-section">
        <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-lg-6 text-center text-lg-start" data-aos="fade-right">
                    <h1 class="display-4 fw-bold mb-3">Buka Jendela Dunia, Mulai dari Sini.</h1>
                    <p class="lead mb-4">Perpustakaan Digital CupsLibs menyediakan ribuan koleksi buku, jurnal, dan sumber pengetahuan lainnya yang bisa Anda akses kapan saja dan di mana saja.</p>
                    <a class="btn btn-primary btn-lg" href="sign/link_login.html">Mulai Membaca</a>
                </div>
                <div class="col-lg-6 d-none d-lg-block" data-aos="fade-left">
                    <img src="assets/logoDashboard-transformed.jpeg" class="img-fluid" alt="Hero Image">
                </div>
            </div>
        </div>
    </section>

    <section id="aboutSection" class="py-5">
        <div class="container" data-aos="fade-up">
            <div class="section-title text-center">
                <h2>Tentang CupsLibs</h2>
                <p class="text-muted">Kami percaya bahwa pengetahuan adalah kekuatan. Misi kami adalah menyediakan akses yang mudah dan tak terbatas ke dunia literasi untuk semua orang.</p>
            </div>
            <div class="row text-center">
                <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="100"><i class="fas fa-book-open fa-3x text-primary mb-3"></i><h4 class="fw-bold">Koleksi Lengkap</h4><p>Akses ribuan judul buku dari berbagai genre.</p></div>
                <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="200"><i class="fas fa-search fa-3x text-success mb-3"></i><h4 class="fw-bold">Pencarian Canggih</h4><p>Temukan buku yang Anda inginkan dengan cepat.</p></div>
                <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="300"><i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i><h4 class="fw-bold">Akses Multi-Platform</h4><p>Baca di mana saja melalui perangkat favorit Anda.</p></div>
            </div>
        </div>
    </section>

<section id="popularBooksSection" class="py-5 bg-light">
    <div class="container" data-aos="fade-up">
        <div class="section-title text-center">
            <h2>Buku Populer</h2>
            <p class="text-muted">Temukan judul-judul yang sedang hangat dibaca oleh para member kami.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card book-card">
                    <img src="/perpustakaan/perpustakaan/assets/tereliye-bumi.jpeg" class="card-img-top" alt="Bumi">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Bumi</h5>
                        <p class="card-text text-muted">Tere Liye</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card book-card">
                    <img src="https://i.gr-assets.com/images/S/compressed.photo.goodreads.com/books/1474154022l/3._SY475_.jpg" class="card-img-top" alt="Harry Potter">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Harry Potter and the Sorcerer's Stone</h5>
                        <p class="card-text text-muted">J.K. Rowling</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="card book-card">
                    <img src="/perpustakaan/perpustakaan/assets/filosofiteras.jpeg" class="card-img-top" alt="Filosofi Teras">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Filosofi Teras</h5>
                        <p class="card-text text-muted">Henry Manampiring</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="card book-card">
                    <img src="/perpustakaan/perpustakaan/assets/greatsgasby.jpeg" class="card-img-top" alt="The Great Gatsby">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">The Great Gatsby</h5>
                        <p class="card-text text-muted">F. Scott Fitzgerald</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
        
    <section id="howToSection" class="py-5">
        <div class="container" data-aos="fade-up">
            <div class="section-title text-center">
                <h2>Proses Peminjaman Mudah</h2>
                <p class="text-muted">Hanya dengan tiga langkah sederhana, buku favorit sudah ada di tangan Anda.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-card"><div class="step-icon"><i class="fas fa-user-plus"></i></div><h4 class="fw-bold">1. Daftar Akun</h4><p>Buat akun member Anda dalam beberapa menit secara gratis.</p></div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-card"><div class="step-icon"><i class="fas fa-search"></i></div><h4 class="fw-bold">2. Cari & Pilih Buku</h4><p>Jelajahi koleksi kami dan temukan buku yang ingin Anda baca.</p></div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-card"><div class="step-icon"><i class="fas fa-check-circle"></i></div><h4 class="fw-bold">3. Pinjam & Baca</h4><p>Klik tombol pinjam dan nikmati buku Anda secara digital.</p></div>
                </div>
            </div>
        </div>
    </section>
    
    <section id="testimonialSection" class="py-5 bg-light">
        <div class="container" data-aos="fade-up">
            <div class="section-title text-center">
                <h2>Apa Kata Mereka?</h2>
                <p class="text-muted">Kami bangga bisa menjadi bagian dari perjalanan literasi para member kami.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card"><img src="https://randomuser.me/api/portraits/women/68.jpg" alt="User"><p class="fst-italic">"Koleksinya lengkap banget! Sekarang saya bisa baca buku-buku baru tanpa harus keluar rumah. Sangat membantu untuk tugas kuliah."</p><p class="name">Aulia R.</p><small class="text-muted">Mahasiswa</small></div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card"><img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User"><p class="fst-italic">"Antarmukanya bersih dan mudah digunakan. Fitur pencariannya juga sangat akurat. Pengalaman terbaik membaca buku digital."</p><p class="name">Budi Santoso</p><small class="text-muted">Pegawai Swasta</small></div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="testimonial-card"><img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User"><p class="fst-italic">"Sebagai pecinta novel, CupsLibs adalah surga. Selalu ada judul baru yang menarik setiap minggunya. Highly recommended!"</p><p class="name">Citra Lestari</p><small class="text-muted">Pecinta Novel</small></div>
                </div>
            </div>
        </div>
    </section>

    <footer id="footer" class="p-5 bg-dark text-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0 text-center text-md-start"><img src="assets/logoPerpus.png" class="img-fluid" alt="Logo Footer" width="200"></div>
                <div class="col-md-6 text-center text-md-end"><h3 class="fs-5">Alamat</h3><p class="text-white-50 fs-6">Jl. KH. Noer Ali, RT.005/RW.006A, Jakasampurna, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17145</p></div>
            </div>
            <hr><div class="d-flex justify-content-center gap-4 my-4"><a href="#" class="fs-3 text-light"><i class="fa-brands fa-github"></i></a><a href="" class="fs-3 text-light"><i class="fa-brands fa-telegram"></i></a><a href="https://www.instagram.com/muhammad_arsln/" class="fs-3 text-light"><i class="fa-brands fa-instagram"></i></a></div>
            <p class="text-center text-white-50">Made with  Cups © 2025</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
        const nav = document.querySelector('.navbar');
        function updateNavbar() {
            if (window.scrollY > 50) {
                nav.classList.add('navbar-scrolled');
                nav.classList.remove('navbar-transparent');
            } else {
                nav.classList.add('navbar-transparent');
                nav.classList.remove('navbar-scrolled');
            }
        }
        document.addEventListener('DOMContentLoaded', updateNavbar);
        window.addEventListener('scroll', updateNavbar);
    </script>
</body>
</html>