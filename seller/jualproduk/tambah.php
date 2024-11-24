<?php 
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['session_username'])) {
    header("Location: ../../login/login.php");
    exit();
}

// Variabel ID pengguna
$username = $_SESSION['session_username'];

// Koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil ID pengguna dari tabel pengguna berdasarkan username
$query = "SELECT id_pengguna FROM pengguna WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $id_pengguna = $row['id_pengguna'];
} else {
    die("ID pengguna tidak ditemukan.");
}

// Ambil data negara dari database
$negaraQuery = "SELECT * FROM negara";
$negaraResult = mysqli_query($koneksi, $negaraQuery);

// Ambil data kategori dari database
$kategoriQuery = "SELECT * FROM kategori";
$kategoriResult = mysqli_query($koneksi, $kategoriQuery);

// Ambil data bahan dari database
$bahanQuery = "SELECT * FROM bahan";
$bahanResult = mysqli_query($koneksi, $bahanQuery);

// Menambahkan produk
if (isset($_POST['submit'])) {
    // Ambil data dari form
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $berat = $_POST['berat'];
    $deskripsi = $_POST['deskripsi'];
    $id_kategori = $_POST['id_kategori'];
    $id_negara = $_POST['id_negara'];
    $id_bahan = $_POST['id_bahan'];

    // Ambil panjang, tinggi, lebar dari form, dengan nilai default 0 jika kosong
    $panjang = !empty($_POST['panjang']) ? $_POST['panjang'] : 0;
    $tinggi = !empty($_POST['tinggi']) ? $_POST['tinggi'] : 0;
    $lebar = !empty($_POST['lebar']) ? $_POST['lebar'] : 0;

    // Mengupload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "C:/xampp/htdocs/ekspor/gambar/gambar_produk/"; // Perbarui path dengan direktori yang benar
        $target_file = $target_dir . basename($_FILES['gambar']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Cek apakah file gambar valid
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $valid_extensions)) {
            // Pindahkan gambar ke folder upload
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                // Simpan nama file gambar ke dalam database
                $gambar_path = basename($_FILES['gambar']['name']);
            } else {
                $gambar_path = ''; // Gagal upload
            }
        } else {
            $gambar_path = ''; // Format gambar tidak valid
        }
    } else {
        $gambar_path = ''; // Tidak ada gambar yang diupload
    }

    // Query untuk menambah produk
    $sql = "INSERT INTO produk (nama_produk, harga, stok, berat, deskripsi, gambar, id_kategori, id_pengguna, panjang, tinggi, lebar, id_bahan, id_negara) 
            VALUES ('$nama_produk', '$harga', '$stok', '$berat', '$deskripsi', '$gambar_path', '$id_kategori', '$id_pengguna', '$panjang', '$tinggi', '$lebar', '$id_bahan', '$id_negara')";

    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Produk berhasil ditambahkan');window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Menutup koneksi database
mysqli_close($koneksi);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h2 {
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control, .form-select {
            border-radius: 4px;
            padding: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-secondary {
            background-color: gray;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Produk</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama_produk">Nama Produk</label>
                <input type="text" class="form-control" name="nama_produk" placeholder="Nama Produk" required>
            </div>
            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" step="0.01" class="form-control" name="harga" placeholder="Harga" required>
            </div>
            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" class="form-control" name="stok" placeholder="Stok" required>
            </div>
            <div class="form-group">
                <label for="berat">Berat</label>
                <input type="number" step="0.01" class="form-control" name="berat" placeholder="Berat" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" placeholder="Deskripsi"></textarea>
            </div>
            <div class="form-group">
                <label for="gambar">Gambar Produk</label>
                <input type="file" class="form-control" name="gambar" required>
            </div>
            <div class="form-group">
                <label for="panjang">Panjang (cm)</label>
                <input type="number" step="0.01" class="form-control" name="panjang" placeholder="Panjang">
            </div>
            <div class="form-group">
                <label for="tinggi">Tinggi (cm)</label>
                <input type="number" step="0.01" class="form-control" name="tinggi" placeholder="Tinggi">
            </div>
            <div class="form-group">
                <label for="lebar">Lebar (cm)</label>
                <input type="number" step="0.01" class="form-control" name="lebar" placeholder="Lebar">
            </div>
            <div class="mb-3">
                <label for="id_kategori" class="form-label">Kategori</label>
                <select class="form-select" id="id_kategori" name="id_kategori" required>
                    <option value="">Pilih Kategori</option>
                    <?php while ($kategori = mysqli_fetch_assoc($kategoriResult)) { ?>
                        <option value="<?php echo $kategori['id_kategori']; ?>"><?php echo htmlspecialchars($kategori['nama_kategori']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_negara" class="form-label">Negara</label>
                <select class="form-select" id="id_negara" name="id_negara" required>
                    <option value="">Pilih Negara</option>
                    <?php while ($negara = mysqli_fetch_assoc($negaraResult)) { ?>
                        <option value="<?php echo $negara['id_negara']; ?>"><?php echo htmlspecialchars($negara['nama_negara']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_bahan" class="form-label">Bahan</label>
                <select class="form-select" id="id_bahan" name="id_bahan" required>
                    <option value="">Pilih Bahan</option>
                    <?php while ($bahan = mysqli_fetch_assoc($bahanResult)) { ?>
                        <option value="<?php echo $bahan['id_bahan']; ?>"><?php echo htmlspecialchars($bahan['bahan']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Tambah Produk</button>
            <button type="button" class="btn btn-secondary" onclick="window.history.back();">Kembali</button>

        </form>
    </div>
</body>
</html>
