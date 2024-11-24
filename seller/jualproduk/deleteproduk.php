<?php
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

// Validasi parameter `id_produk`
if (!isset($_GET['id_produk']) || empty($_GET['id_produk'])) {
    die("Parameter 'id_produk' tidak ditemukan atau kosong.");
}

$id_produk = intval($_GET['id_produk']); // Mengonversi ke integer untuk keamanan

// Cek apakah produk ini digunakan dalam tabel `order`
$check_order = "SELECT COUNT(*) AS count FROM `order` WHERE id_produk = $id_produk";
$result = mysqli_query($koneksi, $check_order);

if (!$result) {
    die("Kesalahan query: " . mysqli_error($koneksi));
}

$row = mysqli_fetch_assoc($result);

if ($row['count'] > 0) {
    // Jika produk digunakan dalam order, hapus data terkait di tabel order
    $delete_order = "DELETE FROM `order` WHERE id_produk = $id_produk";
    if (mysqli_query($koneksi, $delete_order)) {
        echo "Data terkait di tabel order telah dihapus.<br>";
    } else {
        echo "Gagal menghapus data terkait di tabel order: " . mysqli_error($koneksi);
        exit;
    }
}

// Hapus data produk dari tabel produk
$delete_produk = "DELETE FROM `produk` WHERE id_produk = $id_produk";
if (mysqli_query($koneksi, $delete_produk)) {
    echo "Produk berhasil dihapus.";
    header("Location: index.php"); // Redirect kembali ke halaman utama
    exit;
} else {
    echo "Gagal menghapus produk: " . mysqli_error($koneksi);
}
?>
