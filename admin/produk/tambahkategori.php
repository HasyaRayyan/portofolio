<?php
// Pastikan pengguna sudah login
if (!isset($_SESSION['session_username'])) {

    exit();
}

// Atur koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Inisialisasi variabel pesan
$message = '';

// Memeriksa apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menangkap input dari form
    $nama_kategori = $_POST['nama_kategori'];

    // Menyiapkan query untuk memasukkan kategori ke database
    $sql = "INSERT INTO kategori (nama_kategori) VALUES (:nama_kategori)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nama_kategori', $nama_kategori, PDO::PARAM_STR);

    // Menjalankan query dan memberikan pesan jika berhasil
    if ($stmt->execute()) {
        $message = 'Kategori berhasil ditambahkan!';
    } else {
        $message = 'Terjadi kesalahan saat menambahkan kategori.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori</title>
    <!-- Link Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 400px; /* Membatasi lebar form */
            margin: auto;    /* Agar form berada di tengah */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Tambah Kategori</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Form dibatasi dengan class "form-container" -->
    <div class="form-container mt-4">
        <form method="post" action="">
            <div class="mb-3">
                <label for="nama_kategori" class="form-label">Nama Kategori:</label>
                <input type="text" id="nama_kategori" name="nama_kategori" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">Tambah Kategori</button>
            <a href="index.php" class="btn btn-secondary w-100">Kembali</a>
        </form>
    </div>
</div>

<!-- Script Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
