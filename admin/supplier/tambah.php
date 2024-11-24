<?php
session_start();

// Atur koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Variabel untuk menyimpan pesan error dan data supplier
$err = "";
$nama_supplier = "";
$alamat_supplier = "";
$email = "";
$no_telp = "";
$id_negara = ""; // Variabel untuk negara
$gambar = ""; // Variabel untuk menyimpan nama gambar

// Tangani pengiriman formulir
if (isset($_POST['add_supplier'])) {
    $nama_supplier = $_POST['nama_supplier'];
    $alamat_supplier = $_POST['alamat_supplier'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp']; // Ambil nomor telepon dari input
    $id_negara = $_POST['id_negara']; // Ambil negara dari input

    // Validasi input kosong
    if ($nama_supplier == '' || $alamat_supplier == '' || $email == '' || $no_telp == '' || $id_negara == '') {
        $err = "Silakan masukkan semua data.";
    } else {
        // Proses upload gambar
        if (!empty($_FILES['gambar']['name'])) {
            $gambar = $_FILES['gambar']['name'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($gambar);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Cek apakah file gambar sebenarnya
            $check = getimagesize($_FILES['gambar']['tmp_name']);
            if ($check === false) {
                $err = "File yang diunggah bukan gambar.";
                $uploadOk = 0;
            }

            // Cek ukuran file
            if ($_FILES['gambar']['size'] > 500000) { // Batas ukuran 500KB
                $err = "Ukuran file terlalu besar.";
                $uploadOk = 0;
            }

            // Cek jenis file
            if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                $err = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
                $uploadOk = 0;
            }

            // Jika tidak ada kesalahan, pindahkan file ke direktori uploads
            if ($uploadOk) {
                if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                    $err = "Gagal mengunggah gambar.";
                }
            }
        }

        // Insert data supplier baru
        $sql = "INSERT INTO supplier (nama_supplier, alamat_supplier, email, no_telp, id_negara, gambar) 
                VALUES ('$nama_supplier', '$alamat_supplier', '$email', '$no_telp', '$id_negara', '$gambar')";
        $q = mysqli_query($koneksi, $sql);

        if ($q) {
            echo "<script>alert('Supplier berhasil ditambahkan!');window.location.href = 'index.php';</script>";
        } else {
            $err = "Gagal menambahkan supplier, silakan coba lagi.";
        }
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
    <title>Tambah Supplier</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Tambah Supplier</h2>
        <?php if ($err) { ?>
            <div class="alert alert-danger">
                <?php echo $err; ?>
            </div>
        <?php } ?>
        <form action="" method="post" enctype="multipart/form-data"> <!-- Menambahkan enctype untuk upload file -->
            <div class="mb-3">
                <label for="nama_supplier" class="form-label">Nama Supplier</label>
                <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" value="<?php echo htmlspecialchars($nama_supplier); ?>" required>
            </div>
            <div class="mb-3">
                <label for="alamat_supplier" class="form-label">Alamat Supplier</label>
                <input type="text" class="form-control" id="alamat_supplier" name="alamat_supplier" value="<?php echo htmlspecialchars($alamat_supplier); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-3">
                <label for="no_telp" class="form-label">No. Telepon</label>
                <input type="text" class="form-control" id="no_telp" name="no_telp" value="<?php echo htmlspecialchars($no_telp); ?>" required>
            </div>
            <div class="mb-3">
                <label for="id_negara" class="form-label">Negara</label>
                <select class="form-select" id="id_negara" name="id_negara" required>
                    <option value="">Pilih Negara</option>
                    <?php while ($negara = mysqli_fetch_assoc($negaraResult)) { ?>
                        <option value="<?= $negara['id_negara'] ?>"><?= htmlspecialchars($negara['nama_negara']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Unggah Gambar</label>
                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengunggah gambar.</small>
            </div>
            <button type="submit" name="add_supplier" class="btn btn-primary">Tambah Supplier</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
