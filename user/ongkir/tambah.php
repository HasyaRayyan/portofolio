<?php
// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'ekspor';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses tambah ongkir dengan validasi duplikasi
if (isset($_POST['tambah_ongkir'])) {
    $id_negara_asal = $_POST['id_negara_asal'];
    $id_negara_tujuan = $_POST['id_negara_tujuan'];
    $ongkos = $_POST['ongkos'];
    $beacukai = $_POST['beacukai'];

    // Cek apakah data sudah ada
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM ongkir WHERE id_negara_asal = ? AND id_negara_tujuan = ?");
    $stmt_check->bind_param("ii", $id_negara_asal, $id_negara_tujuan);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        $pesan = "Data ongkir untuk kombinasi negara asal dan tujuan tersebut sudah ada.";
    } else {
        // Masukkan data ke tabel ongkir
        $stmt = $conn->prepare("INSERT INTO ongkir (id_negara_asal, id_negara_tujuan, ongkos, beacukai) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $id_negara_asal, $id_negara_tujuan, $ongkos, $beacukai);

        if ($stmt->execute()) {
            $pesan = "Data ongkir berhasil ditambahkan.";
        } else {
            $pesan = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$result_negara = $conn->query("SELECT id_negara, nama_negara FROM negara");
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Ongkir</title>
</head>
<body>
    <h1>Tambah Ongkir</h1>
    <form method="post">
        <label for="id_negara_asal">Pilih Negara Asal:</label>
        <select name="id_negara_asal" id="id_negara_asal" required>
            <?php while ($row = $result_negara->fetch_assoc()): ?>
                <option value="<?= $row['id_negara']; ?>"><?= $row['nama_negara']; ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label for="id_negara_tujuan">Pilih Negara Tujuan:</label>
        <select name="id_negara_tujuan" id="id_negara_tujuan" required>
            <?php 
            // Reset pointer hasil untuk dropdown negara tujuan
            $result_negara->data_seek(0);
            while ($row = $result_negara->fetch_assoc()): ?>
                <option value="<?= $row['id_negara']; ?>"><?= $row['nama_negara']; ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label for="ongkos">Ongkos Kirim (Rp):</label>
        <input type="number" name="ongkos" id="ongkos" required step="0.01" required>
        <br><br>

        <label for="beacukai">Bea Cukai (%):</label>
        <input type="number" name="beacukai" id="beacukai" required step="0.01" min="0">
        <br><br>

        <button type="submit" name="tambah_ongkir">Tambah Ongkir</button>
    </form>

    <?php if (isset($pesan)): ?>
        <h2>Pesan:</h2>
        <p><?= $pesan; ?></p>
    <?php endif; ?>
</body>
</html>
