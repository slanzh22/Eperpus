<?php
// --- PENGATURAN KONEKSI DATABASE ---
$host = "127.0.0.1";
$username = "root";
$password = "";
$database_name = "perpustakaan";
$connection = mysqli_connect($host, $username, $password, $database_name);

if (!$connection) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

// === FUNGSI DASAR ===

/**
 * Menjalankan query SELECT dan mengembalikan hasilnya dalam bentuk array.
 */
function queryReadData($query) {
  global $connection;
  $result = mysqli_query($connection, $query);
  $items = [];
  if ($result) {
      while($item = mysqli_fetch_assoc($result)) {
        $items[] = $item;
      }
  }
  return $items;
}

/**
 * Mengelola upload file gambar cover buku.
 */
function upload() {
  if (!isset($_FILES["cover"]) || $_FILES["cover"]["error"] === 4) {
    return 'no_image'; // User tidak memilih file baru
  }

  $namaFile = $_FILES["cover"]["name"];
  $ukuranFile = $_FILES["cover"]["size"];
  $tmpName = $_FILES["cover"]["tmp_name"];
  $formatGambarValid = ['jpg', 'jpeg', 'png'];
  $ekstensiGambar = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

  if (!in_array($ekstensiGambar, $formatGambarValid)) {
    echo "<script>alert('Format file tidak sesuai (hanya jpg, jpeg, png)');</script>";
    return false;
  }
  if ($ukuranFile > 2000000) { // Maks 2MB
    echo "<script>alert('Ukuran file terlalu besar (maks 2MB)!');</script>";
    return false;
  }

  $namaFileBaru = uniqid() . '.' . $ekstensiGambar;
  // Asumsi: file config.php ada di folder 'config/', dan imgDB sejajar dengan 'config/'
  move_uploaded_file($tmpName, '../imgDB/' . $namaFileBaru);
  return $namaFileBaru;
}

// === FUNGSI UNTUK ADMIN ===

/**
 * Menambahkan buku baru ke database.
 */
function tambahBuku($data) {
    global $connection;
    
    $cover = upload();
    if (!$cover || $cover == 'no_image') {
        echo "<script>alert('Cover buku wajib diupload!');</script>";
        return 0;
    }
    
    $idBuku = htmlspecialchars($data["id_buku"]);
    $kategoriBuku = htmlspecialchars($data["kategori"]);
    $judulBuku = htmlspecialchars($data["judul"]);
    $pengarangBuku = htmlspecialchars($data["pengarang"]);
    $penerbitBuku = htmlspecialchars($data["penerbit"]);
    $tahunTerbit = htmlspecialchars($data["tahun_terbit"]);
    $jumlahHalaman = htmlspecialchars($data["jumlah_halaman"]);
    $deskripsiBuku = htmlspecialchars($data["buku_deskripsi"]);

    $query = "INSERT INTO buku (cover, id_buku, kategori, judul, pengarang, penerbit, tahun_terbit, jumlah_halaman, buku_deskripsi) 
              VALUES ('$cover', '$idBuku', '$kategoriBuku', '$judulBuku', '$pengarangBuku', '$penerbitBuku', '$tahunTerbit', '$jumlahHalaman', '$deskripsiBuku')";
    
    mysqli_query($connection, $query);
    return mysqli_affected_rows($connection);
}

/**
 * Memperbarui data buku yang ada.
 */
function updateBuku($data) {
    global $connection;

    // Ambil semua data dari form $_POST
    $idBuku = htmlspecialchars($data["id_buku"]);
    $gambarLama = htmlspecialchars($data["coverLama"]);
    $kategoriBuku = htmlspecialchars($data["kategori"]);
    $judulBuku = htmlspecialchars($data["judul"]);
    $pengarangBuku = htmlspecialchars($data["pengarang"]);
    $penerbitBuku = htmlspecialchars($data["penerbit"]);
    $tahunTerbit = htmlspecialchars($data["tahun_terbit"]);
    $jumlahHalaman = htmlspecialchars($data["jumlah_halaman"]);
    $deskripsiBuku = htmlspecialchars($data["buku_deskripsi"]);
  
    $cover = upload();
    
    if ($cover === 'no_image') {
        $cover = $gambarLama; // Jika tidak ada gambar baru, gunakan yang lama
    } elseif ($cover === false) {
        return 0; // Jika upload error, hentikan
    }

    // Menggunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($connection, "UPDATE buku SET 
        cover = ?, kategori = ?, judul = ?, pengarang = ?, penerbit = ?, 
        tahun_terbit = ?, jumlah_halaman = ?, buku_deskripsi = ?
        WHERE id_buku = ?");
    
    mysqli_stmt_bind_param($stmt, "ssssssiss", 
        $cover, $kategoriBuku, $judulBuku, $pengarangBuku, $penerbitBuku,
        $tahunTerbit, $jumlahHalaman, $deskripsiBuku, $idBuku
    );
    
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_affected_rows($stmt);
}

/**
 * Menghapus data buku beserta file gambarnya.
 */
function deleteBuku($idBuku) {
    global $connection;
    
    $result = mysqli_query($connection, "SELECT cover FROM buku WHERE id_buku = '$idBuku'");
    if ($file = mysqli_fetch_assoc($result)) {
        @unlink('../imgDB/' . $file['cover']);
    }
    
    mysqli_query($connection, "DELETE FROM buku WHERE id_buku = '$idBuku'");
    return mysqli_affected_rows($connection);
}

/**
 * Mencari data buku berdasarkan keyword.
 */
function searchBuku($keyword) {
    global $connection;
    $keyword = mysqli_real_escape_string($connection, $keyword);
    $querySearch = "SELECT * FROM buku WHERE judul LIKE '%$keyword%' OR kategori LIKE '%$keyword%' OR pengarang LIKE '%$keyword%'";
    return queryReadData($querySearch);
}

/**
 * Mencari data member berdasarkan keyword.
 */
function searchMember($keyword) {
    global $connection;
    $keyword = mysqli_real_escape_string($connection, $keyword);
    $searchMember = "SELECT * FROM member WHERE npm LIKE '%$keyword%' OR nama LIKE '%$keyword%' OR jurusan LIKE '%$keyword%'";
    return queryReadData($searchMember);
}

/**
 * Menghapus data member.
 */
function deleteMember($npm) {
  global $connection;
  mysqli_query($connection, "DELETE FROM member WHERE npm = '$npm'");
  return mysqli_affected_rows($connection);
}


// === FUNGSI UNTUK MEMBER ===

/**
 * Registrasi member baru.
 */
function signUp($data) {
    global $connection;
    
    $npm = htmlspecialchars($data["npm"]);
    $nama = htmlspecialchars(strtolower($data["nama"]));
    $password = mysqli_real_escape_string($connection, $data["password"]);
    $confirmPw = mysqli_real_escape_string($connection, $data["confirmPw"]);
    $jk = htmlspecialchars($data["jenis_kelamin"]);
    $jurusan = htmlspecialchars($data["jurusan"]);
    $noTlp = htmlspecialchars($data["no_tlp"]);
    $tglDaftar = $data["tgl_pendaftaran"];
    
    $npmResult = mysqli_query($connection, "SELECT npm FROM member WHERE npm = '$npm'");
    if(mysqli_fetch_assoc($npmResult)) {
        echo "<script>alert('NPM sudah terdaftar!');</script>";
        return 0;
    }
    
    if($password !== $confirmPw) {
        echo "<script>alert('Konfirmasi password tidak sesuai!');</script>";
        return 0;
    }
    
    $password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = mysqli_prepare($connection, "INSERT INTO member (npm, nama, password, jenis_kelamin, jurusan, no_tlp, tgl_pendaftaran) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssssss", $npm, $nama, $password, $jk, $jurusan, $noTlp, $tglDaftar);
    mysqli_stmt_execute($stmt);
    
    return mysqli_stmt_affected_rows($stmt);
}

/**
 * Memproses peminjaman buku oleh member.
 */
function pinjamBuku($data) {
  global $connection;
  
  $idBuku = $data["id_buku"];
  $npm = $data["npm"];
  $idAdmin = $data["id"];
  $tglPinjam = $data["tgl_peminjaman"];
  $tglKembali = $data["tgl_pengembalian"];
  
  $cekDenda = mysqli_query($connection, "SELECT denda FROM pengembalian WHERE npm = '$npm' AND denda > 0");
  if(mysqli_num_rows($cekDenda) > 0) {
      echo "<script>alert('Anda belum melunasi denda!');</script>";
      return 0;
  }
  
  $npmResult = mysqli_query($connection, "SELECT npm FROM peminjaman WHERE npm = '$npm' AND id_peminjaman NOT IN (SELECT id_peminjaman FROM pengembalian)");
  if(mysqli_num_rows($npmResult) > 0) {
      echo "<script>alert('Anda sudah meminjam buku lain!');</script>";
      return 0;
  }
  
  $queryPinjam = "INSERT INTO peminjaman (id_buku, npm, id_admin, tgl_peminjaman, tgl_pengembalian) VALUES('$idBuku', '$npm', '$idAdmin', '$tglPinjam', '$tglKembali')";
  mysqli_query($connection, $queryPinjam);
  return mysqli_affected_rows($connection);
}

/**
 * Memproses pengembalian buku.
 */
function pengembalian($data) {
  global $connection;
  
  $idPeminjaman = $data["id_peminjaman"];
  $idBuku = $data["id_buku"];
  $npm = $data["npm"];
  $idAdmin = $data["id_admin"];
  $bukuKembali = $data["buku_kembali"];
  $keterlambatan = $data["keterlambatan"];
  $denda = $data["denda"];
  
  $queryPengembalian = "INSERT INTO pengembalian (id_peminjaman, id_buku, npm, id_admin, buku_kembali, keterlambatan, denda) VALUES('$idPeminjaman', '$idBuku', '$npm', '$idAdmin', '$bukuKembali', '$keterlambatan', '$denda')";
  
  mysqli_query($connection, $queryPengembalian);
  return mysqli_affected_rows($connection);
}

/**
 * Memproses pembayaran denda.
 */
function bayarDenda($data) {
  global $connection;
  $idPengembalian = $data["id_peminjaman"];
  $jmlDenda = $data["denda"];
  $jmlDibayar = $data["bayarDenda"];
  $calculate = $jmlDenda - $jmlDibayar;
  
  $bayarDenda = "UPDATE pengembalian SET denda = '$calculate' WHERE id_pengembalian = '$idPengembalian'";
  mysqli_query($connection, $bayarDenda);
  return mysqli_affected_rows($connection);
}
?>