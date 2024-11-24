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

// Variabel untuk menyimpan data supplier
$id_supplier = isset($_GET['id_supplier']) ? intval($_GET['id_supplier']) : 0;
$err = "";
$nama_supplier = "";
$alamat_supplier = "";
$email = "";
$no_telp = "";
$id_negara = "";
$gambar_lama = ""; // Untuk menyimpan gambar lama

// Ambil data supplier berdasarkan ID
if ($id_supplier > 0) {
    $sql = "SELECT * FROM supplier WHERE id_supplier = $id_supplier";
    $result = mysqli_query($koneksi, $sql);
    
    if ($result) {
        $data = mysqli_fetch_assoc($result);
        if ($data) {
            // Ambil data
            $nama_supplier = $data['nama_supplier'];
            $alamat_supplier = $data['alamat_supplier'];
            $email = $data['email'];
            $no_telp = $data['no_telp']; // Ambil nomor telepon
            $id_negara = $data['id_negara']; // Ambil ID negara
            $gambar_lama = $data['gambar']; // Ambil gambar lama
        } else {
            $err = "Data supplier dengan ID $id_supplier tidak ditemukan.";
        }
    } else {
        $err = "Gagal menjalankan query: " . mysqli_error($koneksi);
    }
} else {
    $err = "ID Supplier tidak ditemukan.";
}

// Proses pengeditan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_supplier = mysqli_real_escape_string($koneksi, $_POST['nama_supplier']);
    $alamat_supplier = mysqli_real_escape_string($koneksi, $_POST['alamat_supplier']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']); // Ambil nomor telepon
    $id_negara = mysqli_real_escape_string($koneksi, $_POST['id_negara']); // Ambil ID negara

    $gambar_baru = $_FILES['gambar']['name'];
    $gambar_tmp = $_FILES['gambar']['tmp_name'];

    // Jika gambar baru diunggah, proses upload
    if (!empty($gambar_baru)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($gambar_baru);

        // Pindahkan file ke folder uploads
        if (move_uploaded_file($gambar_tmp, $target_file)) {
            // Update query dengan gambar baru
            $updateQuery = "UPDATE supplier SET 
                            nama_supplier='$nama_supplier', 
                            alamat_supplier='$alamat_supplier', 
                            email='$email', 
                            no_telp='$no_telp', 
                            id_negara='$id_negara', 
                            gambar='$gambar_baru' 
                            WHERE id_supplier=$id_supplier";
        } else {
            $err = "Gagal mengunggah gambar.";
        }
    } else {
        // Jika tidak ada gambar baru, gunakan gambar lama
        $updateQuery = "UPDATE supplier SET 
                        nama_supplier='$nama_supplier', 
                        alamat_supplier='$alamat_supplier', 
                        email='$email', 
                        no_telp='$no_telp', 
                        id_negara='$id_negara' 
                        WHERE id_supplier=$id_supplier";
    }

    if (isset($updateQuery) && mysqli_query($koneksi, $updateQuery)) {
        header("Location: index.php"); // Arahkan kembali setelah edit
        exit();
    } else {
        $err = "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}

// Ambil data negara untuk dropdown
$negaraQuery = "SELECT * FROM negara";
$negaraResult = mysqli_query($koneksi, $negaraQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Supplier</h2>
    
    <?php if ($err): ?>
        <div class="alert alert-danger"><?= $err ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data"> <!-- Menambahkan enctype untuk upload file -->
        <div class="mb-3">
            <label for="nama_supplier" class="form-label">Nama Supplier</label>
            <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" value="<?= htmlspecialchars($nama_supplier) ?>" required>
        </div>
        <div class="mb-3">
            <label for="alamat_supplier" class="form-label">Alamat Supplier</label>
            <input type="text" class="form-control" id="alamat_supplier" name="alamat_supplier" value="<?= htmlspecialchars($alamat_supplier) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        <div class="mb-3">
            <label for="no_telp" class="form-label">No. Telepon</label>
            <input type="text" class="form-control" id="no_telp" name="no_telp" value="<?= htmlspecialchars($no_telp) ?>" required>
        </div>
        <div class="mb-3">
            <label for="id_negara" class="form-label">Negara</label>
            <select class="form-select" id="id_negara" name="id_negara" required>
                <option value="">Pilih Negara</option>
                <?php while ($negara = mysqli_fetch_assoc($negaraResult)) { ?>
                    <option value="<?= $negara['id_negara'] ?>" <?= ($negara['id_negara'] == $id_negara) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($negara['nama_negara']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="gambar" class="form-label">Unggah Gambar</label>
            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
            <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
            <?php if ($gambar_lama): ?>
                <img src="uploads/<?= htmlspecialchars($gambar_lama) ?>" alt="Gambar Supplier" width="100" class="mt-2">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
