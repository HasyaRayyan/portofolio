<?php
header('Content-Type: application/json');

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

// Ambil data pengguna dengan role 'user'
$sql = "SELECT nama_asli, email, no_telp, alamat FROM pengguna WHERE role = 'user'";
$result = mysqli_query($koneksi, $sql);

// Cek apakah query berhasil
if ($result) {
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($users);
} else {
    echo json_encode([]);
}

// Tutup koneksi
mysqli_close($koneksi);
?>
