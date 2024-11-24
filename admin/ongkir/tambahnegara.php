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

// Tangani form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_negara = mysqli_real_escape_string($koneksi, $_POST['nama_negara']);

    // Cek apakah nama negara sudah ada
    $cekNegara = "SELECT * FROM negara WHERE nama_negara = '$nama_negara'";
    $hasilCek = mysqli_query($koneksi, $cekNegara);

    if (mysqli_num_rows($hasilCek) > 0) {
        echo "<script>alert('Nama negara sudah ada!'); window.location.href='tambahnegara.php';</script>";
    } else {
        $query = "INSERT INTO negara (nama_negara) VALUES ('$nama_negara')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Data negara berhasil ditambahkan!'); window.location.href='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Negara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Tambah Data Negara</h2>
    <form action="" method="POST">
        <div class="mb-3">
            <label for="nama_negara" class="form-label">Nama Negara</label>
            <input type="text" name="nama_negara" id="nama_negara" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
