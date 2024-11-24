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

// Aktifkan mode error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (isset($_GET['detail_id'])) {
    $id_pengguna = $_GET['detail_id'];

    // Query dengan JOIN untuk mengambil data pengguna dan nama negara
    $query = "SELECT pengguna.*, negara.nama_negara 
              FROM pengguna 
              JOIN negara ON pengguna.id_negara = negara.id_negara 
              WHERE pengguna.id_pengguna = ?";

    $stmt = mysqli_prepare($koneksi, $query);
    
    if (!$stmt) {
        die("Error dalam persiapan statement: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt, 'i', $id_pengguna);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($data = mysqli_fetch_assoc($result)) {
        echo "<p>Username: " . htmlspecialchars($data['username']) . "</p>";
        echo "<p>Nama Asli: " . htmlspecialchars($data['nama_asli']) . "</p>";
        echo "<p>Email: " . htmlspecialchars($data['email']) . "</p>";
        echo "<p>No. Telp: " . htmlspecialchars($data['no_telp']) . "</p>";
        echo "<p>Alamat: " . htmlspecialchars($data['alamat']) . "</p>";
        echo "<p>Role: " . htmlspecialchars($data['role']) . "</p>";
        echo "<p>Negara: " . htmlspecialchars($data['nama_negara']) . "</p>";
    } else {
        echo "<p>Data pengguna tidak ditemukan atau gagal memuat detail pengguna.</p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p>ID pengguna tidak ditemukan di URL.</p>";
}
?>
