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

// Ambil data berdasarkan ID
$id = intval($_GET['id']);
$query = "SELECT * FROM ongkir WHERE id_pengiriman = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

// Update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_negara_asal = intval($_POST['id_negara_asal']);
    $id_negara_tujuan = intval($_POST['id_negara_tujuan']);
    $ongkos = floatval($_POST['ongkos']);
    $beacukai = floatval($_POST['beacukai']);

    $updateQuery = "UPDATE ongkir 
                    SET id_negara_asal = $id_negara_asal, 
                        id_negara_tujuan = $id_negara_tujuan, 
                        ongkos = $ongkos, 
                        beacukai = $beacukai 
                    WHERE id_pengiriman = $id";

    if (mysqli_query($koneksi, $updateQuery)) {
        echo "<script>alert('Data berhasil diperbarui.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ongkir</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Data Ongkir</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="id_negara_asal" class="form-label">Negara Asal</label>
            <input type="text" name="id_negara_asal" id="id_negara_asal" value="<?php echo htmlspecialchars($data['id_negara_asal']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="id_negara_tujuan" class="form-label">Negara Tujuan</label>
            <input type="text" name="id_negara_tujuan" id="id_negara_tujuan" value="<?php echo htmlspecialchars($data['id_negara_tujuan']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="ongkos" class="form-label">Ongkos : $</label>
            <input type="number" step="0.01" name="ongkos" id="ongkos" value="<?php echo htmlspecialchars($data['ongkos']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="beacukai" class="form-label">Bea Cukai : %</label>
            <input type="number" step="0.01" name="beacukai" id="beacukai" value="<?php echo htmlspecialchars($data['beacukai']); ?>" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
