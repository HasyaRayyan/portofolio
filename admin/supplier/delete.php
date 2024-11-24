<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['session_username'])) {
    // Jika tidak ada username di session, arahkan pengguna ke halaman login
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

// Cek apakah ID supplier ada di parameter URL
if (isset($_GET['id_supplier'])) {
    $id_supplier = intval($_GET['id_supplier']); // Konversi ke integer untuk keamanan

    // Query untuk menghapus supplier berdasarkan ID
    $sql = "DELETE FROM supplier WHERE id_supplier = $id_supplier";
    
    if (mysqli_query($koneksi, $sql)) {
        // Jika berhasil, kembali ke halaman supplier dengan pesan sukses
        header("Location: index.php?delete_success=true");
    } else {
        // Jika gagal, tampilkan pesan kesalahan
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    // Jika ID supplier tidak ditemukan di URL, kembali ke halaman supplier
    header("Location: index.php");
}

// Tutup koneksi
mysqli_close($koneksi);
?>
