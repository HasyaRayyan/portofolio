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
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['session_username'])) {
    header("Location: ../../login/login.php");
    exit();
}
// Ambil data bukti pembayaran berdasarkan ID transaksi
$id_riwayat = $_GET['id_riwayat']; // Pastikan `id_transaksi` dikirim melalui URL
$sql = "SELECT bukti_pembayaran FROM riwayat WHERE id_riwayat = $id_riwayat";
$result = mysqli_query($koneksi, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $bukti_pembayaran = $row['bukti_pembayaran'];
} else {
    echo "<div class='alert alert-danger'>Data tidak ditemukan!</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Bukti Pembayaran</h1>
    <div class="card mt-4">
        <div class="card-body text-center">
            <?php if (!empty($bukti_pembayaran)) : ?>
                <img src="<?php echo $bukti_pembayaran; ?>" alt="Bukti Pembayaran" class="img-fluid" style="max-width: 500px; border: 1px solid #ddd; border-radius: 5px;">
            <?php else : ?>
                <p class="text-danger">Bukti pembayaran tidak tersedia.</p>
            <?php endif; ?>
        </div>
        <div class="card-footer text-center">
            <a href="riwayat_transaksi.php" class="btn btn-primary">Kembali</a>
        </div>
    </div>
</div>
</body>
</html>
