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

// Tangani penghapusan pengguna
if (isset($_GET['id'])) {
    $id_pengguna = intval($_GET['id']);

    // Query untuk menghapus pengguna
    $deleteQuery = "DELETE FROM pengguna WHERE id_pengguna = $id_pengguna";
    if (mysqli_query($koneksi, $deleteQuery)) {
        // Penghapusan berhasil
        echo "<script>
                alert('Pengguna berhasil dihapus.');
                window.location.href = '../pengguna/index.php';
              </script>";
    } else {
        // Penghapusan gagal
        echo "<script>
                alert('Terjadi kesalahan saat menghapus pengguna: " . mysqli_error($koneksi) . "');
                window.location.href = '../pengguna/index.php';
              </script>";
    }
} else {
    // Jika tidak ada ID yang diberikan
    echo "<script>
            alert('ID pengguna tidak valid.');
            window.location.href = '../pengguna/index.php';
          </script>";
}

// Tutup koneksi
mysqli_close($koneksi);
?>
