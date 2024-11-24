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
    $id_negara_asal = mysqli_real_escape_string($koneksi, $_POST['id_negara_asal']);
    $id_negara_tujuan = mysqli_real_escape_string($koneksi, $_POST['id_negara_tujuan']);
    $ongkos = mysqli_real_escape_string($koneksi, $_POST['ongkos']);
    $beacukai = mysqli_real_escape_string($koneksi, $_POST['beacukai']);

    $query = "INSERT INTO ongkir (id_negara_asal, id_negara_tujuan, ongkos, beacukai) 
              VALUES ('$id_negara_asal', '$id_negara_tujuan', '$ongkos', '$beacukai')";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// Ambil data negara
$queryNegara = "SELECT id_negara, nama_negara FROM negara";
$resultNegara = mysqli_query($koneksi, $queryNegara);
if (!$resultNegara) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Ongkir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Tambah Data Ongkir</h2>
    <form action="" method="POST">
        <div class="mb-3">
            <label for="id_negara_asal" class="form-label">Negara Asal</label>
            <select name="id_negara_asal" id="id_negara_asal" class="form-select" required>
                <option value="">Pilih Negara Asal</option>
                <?php while ($negara = mysqli_fetch_assoc($resultNegara)): ?>
                    <option value="<?= $negara['id_negara'] ?>"><?= htmlspecialchars($negara['nama_negara']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="id_negara_tujuan" class="form-label">Negara Tujuan</label>
            <select name="id_negara_tujuan" id="id_negara_tujuan" class="form-select" required>
                <option value="">Pilih Negara Tujuan</option>
                <?php
                // Reset pointer hasil query
                mysqli_data_seek($resultNegara, 0);
                while ($negara = mysqli_fetch_assoc($resultNegara)): ?>
                    <option value="<?= $negara['id_negara'] ?>"><?= htmlspecialchars($negara['nama_negara']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="ongkos" class="form-label">Ongkos : $</label>
            <input type="number" step="0.01" name="ongkos" id="ongkos" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="beacukai" class="form-label">Bea Cukai : %</label>
            <input type="number" step="0.01" name="beacukai" id="beacukai" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
