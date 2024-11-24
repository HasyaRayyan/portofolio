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

// Ambil ID produk dari permintaan GET
$detail_id = isset($_GET['detail_id']) ? intval($_GET['detail_id']) : 0;

if ($detail_id) {
    $sql = "SELECT * FROM produk WHERE id_produk = $detail_id";
    $result = mysqli_query($koneksi, $sql);
    
    if ($result) {
        $data = mysqli_fetch_array($result);
        $nama_produk = htmlspecialchars($data['nama_produk']);
        $harga = htmlspecialchars($data['harga']);
        $stok = htmlspecialchars($data['stok']);
        $berat = htmlspecialchars($data['berat']);
        $deskripsi = htmlspecialchars($data['deskripsi']);
        $gambar = htmlspecialchars($data['gambar']);
    } else {
        echo "<p>Data tidak ditemukan.</p>";
        exit();
    }
} else {
    echo "<p>Data tidak ditemukan.</p>";
    exit();
}
?>

<div class="modal-header">
    <h5 class="modal-title" id="detailModalLabel"><?= $nama_produk ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <img src="uploads/<?= $gambar ?>" alt="Gambar Produk" class="img-fluid mb-3">
    <p><strong>Harga:</strong> $ <?= $harga ?></p>
    <p><strong>Stok:</strong> <?= $stok ?></p>
    <p><strong>Berat:</strong> <?= $berat ?> kg</p>
    <p><strong>Deskripsi:</strong> <?= $deskripsi ?></p>
</div>
