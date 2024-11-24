<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['session_username'])) {
    header("Location: ../../login/login.php");
    exit();
}

// Koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Hapus data
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM ongkir WHERE id_pengiriman = $id";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location.href='index.php';</script>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
