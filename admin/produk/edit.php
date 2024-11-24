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

// Variabel untuk menyimpan data produk
$id_produk = isset($_GET['id']) ? intval($_GET['id']) : 0;
$err = "";
$nama_produk = "";
$harga = "";
$stok = "";
$berat = "";
$deskripsi = "";
$gambar = "";
$id_pengguna = ""; // Variabel untuk menyimpan ID supplier
$id_kategori = ""; // Variabel untuk menyimpan ID kategori produk
$id_negara = ""; // Variabel untuk menyimpan ID negara
$id_bahan = ""; // Variabel untuk menyimpan ID bahan produk

// Ambil data produk berdasarkan ID
if ($id_produk) {
    $sql = "SELECT * FROM produk WHERE id_produk = $id_produk";
    $result = mysqli_query($koneksi, $sql);
    
    if ($result) {
        $data = mysqli_fetch_array($result);
        $nama_produk = $data['nama_produk'];
        $harga = $data['harga'];
        $stok = $data['stok'];
        $berat = $data['berat'];
        $deskripsi = $data['deskripsi'];
        $gambar = $data['gambar'];
        $id_pengguna = $data['id_pengguna']; // Ambil ID supplier
        $id_kategori = $data['id_kategori']; // Ambil ID kategori produk
        $id_negara = $data['id_negara']; // Ambil ID negara produk
        $id_bahan = $data['id_bahan']; // Ambil ID bahan produk
    } else {
        $err = "Data tidak ditemukan.";
    }
}

// Proses pengeditan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $berat = mysqli_real_escape_string($koneksi, $_POST['berat']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $id_pengguna = mysqli_real_escape_string($koneksi, $_POST['id_pengguna']);
    $id_kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $id_negara = mysqli_real_escape_string($koneksi, $_POST['id_negara']); // Ambil ID negara dari form
    $id_bahan = mysqli_real_escape_string($koneksi, $_POST['id_bahan']); // Ambil ID bahan dari form

    // Validasi panjang deskripsi
    if (strlen($deskripsi) < 10 || strlen($deskripsi) > 100) {
        $err = "Deskripsi harus antara 100 dan 300 karakter.";
    } else {
        // Penanganan gambar
        $target_dir = "C:/xampp/htdocs/ekspor/gambar/gambar_produk/";
        $new_image = $_FILES['gambar']['name'];
        $uploadOk = 1;

        if (!empty($new_image)) {
            // Validasi dan upload gambar baru
            $target_file = $target_dir . basename($new_image);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Cek apakah file gambar adalah gambar sebenarnya
            $check = getimagesize($_FILES['gambar']['tmp_name']);
            if ($check === false) {
                $err = "File yang diunggah bukan gambar.";
                $uploadOk = 0;
            }

            // Cek ukuran file
            if ($_FILES['gambar']['size'] > 500000) {
                $err = "Maaf, ukuran file terlalu besar.";
                $uploadOk = 0;
            }

            // Cek format file
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $err = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
                $uploadOk = 0;
            }

            // Jika tidak ada error, upload file
            if ($uploadOk) {
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                    $gambar = $new_image;
                } else {
                    $err = "Maaf, terjadi kesalahan saat mengunggah gambar.";
                }
            }
        }

        // Jika tidak ada error, lanjutkan dengan query update
        if (empty($err)) {
            $updateQuery = "UPDATE produk SET 
                nama_produk='$nama_produk', 
                harga='$harga', 
                stok='$stok', 
                berat='$berat', 
                deskripsi='$deskripsi', 
                id_kategori='$id_kategori', 
                id_negara='$id_negara', 
                id_pengguna='$id_pengguna',
                id_bahan='$id_bahan'"; // Update id_bahan
                

            if (!empty($gambar)) {
                $updateQuery .= ", gambar='$gambar'";
            }

            $updateQuery .= " WHERE id_produk=$id_produk";

            if (mysqli_query($koneksi, $updateQuery)) {
                header("Location: index.php");
                exit();
            } else {
                $err = "Gagal memperbarui data: " . mysqli_error($koneksi);
            }
        }
    }
}

// Ambil data kategori untuk dropdown
$kategoriQuery = "SELECT * FROM kategori";
$kategoriResult = mysqli_query($koneksi, $kategoriQuery);

// Ambil data negara untuk dropdown
$negaraQuery = "SELECT * FROM negara"; // Query untuk mengambil semua negara
$negaraResult = mysqli_query($koneksi, $negaraQuery);

// Ambil data bahan untuk dropdown
$bahanQuery = "SELECT * FROM bahan"; // Query untuk mengambil semua bahan
$bahanResult = mysqli_query($koneksi, $bahanQuery);

// Ambil data bahan untuk dropdown
$penggunaQuery = "SELECT * FROM pengguna"; // Query untuk mengambil semua bahan
$penggunaResult = mysqli_query($koneksi, $penggunaQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Produk</h2>
    
    <?php if ($err): ?>
        <div class="alert alert-danger"><?= $err ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nama_produk" class="form-label">Nama Produk</label>
            <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?= htmlspecialchars($nama_produk) ?>" required>
        </div>
        <div class="mb-3">
            <label for="harga" class="form-label">Harga</label>
            <input type="number" class="form-control" id="harga" name="harga" value="<?= htmlspecialchars($harga) ?>" required>
        </div>
        <div class="mb-3">
            <label for="stok" class="form-label">Stok</label>
            <input type="number" class="form-control" id="stok" name="stok" value="<?= htmlspecialchars($stok) ?>" required>
        </div>
        <div class="mb-3">
            <label for="berat" class="form-label">Berat</label>
            <input type="number" class="form-control" id="berat" name="berat" value="<?= htmlspecialchars($berat) ?>" required>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" required><?= htmlspecialchars($deskripsi) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar</label>
            <input type="file" class="form-control" id="gambar" name="gambar">
        </div>
        <div class="mb-3">
            <label for="id_kategori" class="form-label">Kategori</label>
            <select class="form-select" id="id_kategori" name="id_kategori" required>
                <option value="">Pilih Kategori</option>
                <?php while ($row = mysqli_fetch_assoc($kategoriResult)): ?>
                    <option value="<?= $row['id_kategori'] ?>" <?= $row['id_kategori'] == $id_kategori ? 'selected' : '' ?>><?= $row['nama_kategori'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="id_negara" class="form-label">Negara</label>
            <select class="form-select" id="id_negara" name="id_negara" required>
                <option value="">Pilih Negara</option>
                <?php while ($row = mysqli_fetch_assoc($negaraResult)): ?>
                    <option value="<?= $row['id_negara'] ?>" <?= $row['id_negara'] == $id_negara ? 'selected' : '' ?>><?= $row['nama_negara'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="id_bahan" class="form-label">Bahan</label>
            <select class="form-select" id="id_bahan" name="id_bahan" required>
                <option value="">Pilih Bahan</option>
                <?php while ($row = mysqli_fetch_assoc($bahanResult)): ?>
                    <option value="<?= $row['id_bahan'] ?>" <?= $row['id_bahan'] == $id_bahan ? 'selected' : '' ?>><?= $row['bahan'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="id_pengguna" class="form-label">Bahan</label>
            <select class="form-select" id="id_bahan" name="id_pengguna" required>
                <option value="">Pilih Pengguna</option>
                <?php while ($row = mysqli_fetch_assoc($penggunaResult)): ?>
                    <option value="<?= $row['id_pengguna'] ?>" <?= $row['id_pengguna'] == $id_pengguna ? 'selected' : '' ?>><?= $row['username'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Produk</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Tutup koneksi
mysqli_close($koneksi);
?>
