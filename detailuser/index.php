<?php
// Mulai session
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['session_username'])) {
    header("Location: ../../login/login.php");
    exit();
}

// Ambil id_pengguna dari session
$id_pengguna = $_SESSION['session_id_pengguna'];

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

// Cek apakah parameter 'id' ada di URL
if (isset($_GET['id'])) {
    $id_pengguna = $_GET['id']; // Mengambil id_pengguna dari parameter URL
} else {
    echo "ID pengguna tidak ditemukan.";
    exit;
}

// Query untuk mengambil data pengguna
$sql = "SELECT * FROM pengguna WHERE id_pengguna = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_pengguna);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah pengguna ditemukan
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "Pengguna tidak ditemukan.";
    exit;
}

// Ambil URL asal (halaman sebelumnya) jika tidak ada URL sebelumnya gunakan halaman default
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Detail Pengguna</h1>

        <table class="table table-bordered">
            <tr>
                <th>ID Pengguna</th>
                <td><?php echo $user['id_pengguna']; ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?php echo $user['username']; ?></td>
            </tr>
            <tr>
                <th>Password</th>
                <td><?php echo $user['password']; ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo $user['email']; ?></td>
            </tr>
            <tr>
                <th>No. Telp</th>
                <td><?php echo $user['no_telp']; ?></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><?php echo $user['alamat']; ?></td>
            </tr>
            <tr>
                <th>Nama Asli</th>
                <td><?php echo $user['nama_asli']; ?></td>
            </tr>
            <tr>
                <th>Negara</th>
                <td>
                    <?php
                    $negara_sql = "SELECT nama_negara FROM negara WHERE id_negara = ?";
                    $negara_stmt = $koneksi->prepare($negara_sql);
                    $negara_stmt->bind_param("i", $user['id_negara']);
                    $negara_stmt->execute();
                    $negara_result = $negara_stmt->get_result();
                    if ($negara_result->num_rows > 0) {
                        $negara = $negara_result->fetch_assoc();
                        echo $negara['nama_negara'];
                    } else {
                        echo "Negara tidak ditemukan.";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Role</th>
                <td><?php echo $user['role']; ?></td>
            </tr>
            <tr>
                <th>Gambar</th>
                <td>
                    <?php if ($user['gambar']) { ?>
                        <img src="gambar/gambar_profil/<?php echo $user['gambar']; ?>" alt="Gambar Pengguna" width="100">
                    <?php } else { ?>
                        Tidak ada gambar.
                    <?php } ?>
                </td>
            </tr>
        </table>

        <!-- Tombol Kembali menggunakan referer jika ada -->
        <a href="<?php echo $redirect_url; ?>" class="btn btn-secondary">Kembali</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Tutup koneksi
$stmt->close();
$koneksi->close();
?>
