<?php 
session_start();

// Atur koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Variabel untuk menyimpan pesan error dan data produk
$err = "";
$nama_produk = "";
$harga = "";
$stok = "";
$berat = "";
$deskripsi = "";
$gambar = "";
$id_negara = "";
$id_kategori = "";
$panjang = "";
$tinggi = "";
$lebar = "";
$id_bahan = "";
$id_pengguna = "";



// Ambil data pengguna dari database
$penggunaQuery = "SELECT * FROM pengguna";
$penggunaResult = mysqli_query($koneksi, $penggunaQuery);


// Ambil data negara dari database
$negaraQuery = "SELECT * FROM negara";
$negaraResult = mysqli_query($koneksi, $negaraQuery);

// Ambil data kategori dari database
$kategoriQuery = "SELECT * FROM kategori";
$kategoriResult = mysqli_query($koneksi, $kategoriQuery);

// Ambil data bahan dari database
$bahanQuery = "SELECT * FROM bahan";
$bahanResult = mysqli_query($koneksi, $bahanQuery);

// Tangani pengiriman formulir
if (isset($_POST['add_product'])) {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $berat = $_POST['berat'];
    $deskripsi = $_POST['deskripsi'];
    $id_negara = $_POST['id_negara']; // Ambil id_negara dari input
    $id_kategori = $_POST['id_kategori']; // Ambil id_kategori dari input
    $panjang = $_POST['panjang'];
    $tinggi = $_POST['tinggi'];
    $lebar = $_POST['lebar'];
    $id_bahan = $_POST['id_bahan'];
    $id_pengguna = $_POST['id_pengguna'];

    // Menangani upload file gambar
    $gambar = $_FILES['gambar']['name'];
    $target_dir = "C:/xampp/htdocs/ekspor/gambar/gambar_produk/";
    $target_file = $target_dir . basename($gambar);
    
    // Validasi input kosong
    if ($nama_produk == '' || $harga == '' || $stok == '' || $berat == '' || $deskripsi == '' || $gambar == '' || $id_negara == '' || $id_kategori == '' || $panjang == '' || $tinggi == '' || $lebar == '' || $id_bahan == '') {
        $err = "Silakan masukkan semua data.";
    } else {
        // Upload gambar dan simpan data produk termasuk kategori
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            // Insert data produk baru
            $sql = "INSERT INTO produk (nama_produk, harga, stok, berat, deskripsi, gambar, id_negara, id_kategori, panjang, tinggi, lebar, id_bahan, id_pengguna) 
                    VALUES ('$nama_produk', '$harga', '$stok', '$berat', '$deskripsi', '$gambar', '$id_negara', '$id_kategori', '$panjang', '$tinggi', '$lebar', '$id_bahan', '$id_pengguna')";
            $q = mysqli_query($koneksi, $sql);

            if ($q) {
                echo "<script>alert('Produk berhasil ditambahkan!');window.location.href = 'index.php';</script>";
            } else {
                $err = "Gagal menambahkan produk, silakan coba lagi.";
            }
        } else {
            $err = "Gagal mengunggah gambar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Tambah Produk</h2>
        <?php if ($err) { ?>
            <div class="alert alert-danger">
                <?php echo $err; ?>
            </div>
        <?php } ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?php echo htmlspecialchars($nama_produk); ?>" required>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" value="<?php echo htmlspecialchars($harga); ?>" required>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stok" name="stok" value="<?php echo htmlspecialchars($stok); ?>" required>
            </div>
            <div class="mb-3">
                <label for="berat" class="form-label">Berat (gram)</label>
                <input type="number" class="form-control" id="berat" name="berat" value="<?php echo htmlspecialchars($berat); ?>" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi Produk</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" required><?php echo htmlspecialchars($deskripsi); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Upload Gambar</label>
                <input type="file" class="form-control" id="gambar" name="gambar" required>
                <label for="gambar" class="form-label">*Gunakan Gambar 4:3</label>
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
                <label for="panjang" class="form-label">Panjang (cm)</label>
                <input type="number" class="form-control" id="panjang" name="panjang" value="<?php echo htmlspecialchars($panjang); ?>" required>
            </div>
            <div class="mb-3">
                <label for="tinggi" class="form-label">Tinggi (cm)</label>
                <input type="number" class="form-control" id="tinggi" name="tinggi" value="<?php echo htmlspecialchars($tinggi); ?>" required>
            </div>
            <div class="mb-3">
                <label for="lebar" class="form-label">Lebar (cm)</label>
                <input type="number" class="form-control" id="lebar" name="lebar" value="<?php echo htmlspecialchars($lebar); ?>" required>
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
            <div class="mb-3">
                <label for="id_pengguna" class="form-label">Pengguna</label>
                <select class="form-select" id="id_pengguna" name="id_pengguna" required>
                    <option value="">Pilih Pengguna</option>
                    <?php while ($pengguna = mysqli_fetch_assoc($penggunaResult)) { ?>
                        <option value="<?php echo $pengguna['id_pengguna']; ?>"><?php echo htmlspecialchars($pengguna['username']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="add_product">Tambah Produk</button>
            <a href="index.php" class="btn btn-secondary mt-2">Kembali</a>
        </form>
    </div>
</body>

</html>
