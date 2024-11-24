<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['session_username'])) {
    header("Location: ../../login/login.php");
    exit();
}

// Atur koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Tangani penghapusan produk
if (isset($_GET['id'])) {
    $id_produk = intval($_GET['id']);
    
    // Ambil nama gambar sebelum dihapus
    $query = "SELECT gambar FROM produk WHERE id_produk = $id_produk";
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $gambar = $data['gambar'];

        // Hapus produk dari database
        $deleteQuery = "DELETE FROM produk WHERE id_produk = $id_produk";
        if (mysqli_query($koneksi, $deleteQuery)) {
            // Hapus file gambar jika ada
            if (!empty($gambar)) {
                unlink("uploads/$gambar"); // Hapus file gambar dari server
            }
            $_SESSION['message'] = 'Produk berhasil dihapus.';
        } else {
            $_SESSION['message'] = 'Gagal menghapus produk: ' . mysqli_error($koneksi);
        }
    } else {
        $_SESSION['message'] = 'Produk tidak ditemukan.';
    }
}

mysqli_close($koneksi);
header("Location: index.php"); // Arahkan kembali ke halaman produk
exit();
?>
