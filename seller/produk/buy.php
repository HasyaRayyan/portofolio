<?php  
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
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['session_username'])) {
    header("Location: ../../login/login.php");
    exit();
}

// Dapatkan ID produk dari URL dan pastikan sebagai integer
$id_produk = intval($_GET['id_produk']);

// Pastikan session telah berisi `session_id_pengguna`
$id_pengguna = $_SESSION['session_id_pengguna']; // Mengambil ID pengguna dari session

// Query untuk mendapatkan detail produk dan pengguna yang memiliki role seller
$sql = "SELECT p.nama_produk, p.harga, p.stok, p.berat, p.deskripsi, p.gambar, u.nama_asli AS nama_seller, u.gambar AS gambar_seller, k.nama_kategori, n.nama_negara AS negara_asal, p.id_negara, u.id_pengguna AS id_seller
        FROM produk p
        JOIN pengguna u ON p.id_pengguna = u.id_pengguna AND u.role = 'seller'
        JOIN kategori k ON p.id_kategori = k.id_kategori
        JOIN negara n ON p.id_negara = n.id_negara  
        WHERE p.id_produk = $id_produk";

$result = $koneksi->query($sql);

// Periksa apakah produk ditemukan
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger'>Produk tidak ditemukan!</div>";
    exit;
}

// Ambil id_pengguna dari session
$id_pengguna = $_SESSION['session_id_pengguna'];

// Ambil data negara untuk tujuan
$negara_sql = "SELECT id_negara, nama_negara FROM negara";
$negara_result = $koneksi->query($negara_sql);

// Ambil data ongkir dan bea cukai
$ongkir_sql = "SELECT o.id_pengiriman, o.ongkos, o.beacukai, n.nama_negara AS negara_tujuan
               FROM ongkir o
               JOIN negara n ON o.id_negara_tujuan = n.id_negara
               WHERE o.id_negara_asal = " . $row['id_negara'];
$ongkir_result = $koneksi->query($ongkir_sql);

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_penerima = mysqli_real_escape_string($koneksi, $_POST['nama_penerima']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $jumlah = intval($_POST['jumlah']);
    $tujuan = mysqli_real_escape_string($koneksi, $_POST['tujuan']);

    // Pastikan semua data telah diisi
    if (empty($nama_penerima) || empty($alamat) || empty($no_telp) || empty($jumlah) || empty($tujuan)) {
        echo "<div class='alert alert-danger'>Data pembelian tidak lengkap.</div>";
        exit;
    }

    // Cek ketersediaan stok
    if ($jumlah > $row['stok']) {
        echo "<script>
                    alert('Stok kurang');
                    window.location.href = 'buy.php?id_produk=$id_produk';
                  </script>";
        exit();
    }

    // Ambil negara asal dari produk
    $asal = $row['id_negara']; 
    $total_harga = $row['harga'] * $jumlah; 
    $resi = uniqid('44'); 

    // Hitung ongkir dan bea cukai sesuai negara tujuan
    $ongkos = 0;
    $beacukai = 0;
    while ($ongkir_row = $ongkir_result->fetch_assoc()) {
        if ($ongkir_row['negara_tujuan'] == $tujuan) {
            $ongkos = $ongkir_row['ongkos'] * $row['berat']; // Kalikan ongkos dengan berat barang
            $beacukai = $ongkir_row['beacukai'];
            break;
        }
    }

    // Ambil negara tujuan dari dropdown
    $tujuan_id_sql = "SELECT id_negara FROM negara WHERE nama_negara = '$tujuan'";
    $tujuan_id_result = $koneksi->query($tujuan_id_sql);
    $tujuan_row = $tujuan_id_result->fetch_assoc();
    $id_negara_tujuan = $tujuan_row['id_negara'];

    // Hitung total keseluruhan
    $totalKeseluruhan = $total_harga + $ongkos + $beacukai;

    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['bukti_pembayaran']['tmp_name'];
        $file_name = basename($_FILES['bukti_pembayaran']['name']);
        $upload_dir = "uploads/";
        $file_path = $upload_dir . uniqid() . "_" . $file_name;

        // Pastikan direktori ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Pindahkan file ke direktori tujuan
        if (move_uploaded_file($file_tmp, $file_path)) {
            $bukti_pembayaran = $file_path; // Simpan path ke database
        } else {
            echo "<div class='alert alert-danger'>Gagal mengunggah bukti pembayaran.</div>";
            exit();
        }
    } else {
        echo "<div class='alert alert-danger'>Harap unggah bukti pembayaran.</div>";
        exit();
    }

    // Siapkan query untuk menyimpan data ke tabel riwayat
    $insert_sql = "INSERT INTO riwayat (nama_produk, jumlah, alamat, resi, nama_penerima, total_harga, status, created_at, id_negara_asal, id_negara_tujuan, id_pengguna, beacukai, ongkos, no_telp, id_produk, bukti_pembayaran)
    VALUES ('" . mysqli_real_escape_string($koneksi, $row['nama_produk']) . "', $jumlah, '$alamat', '$resi', '$nama_penerima', $totalKeseluruhan, 'Proses', NOW(), $asal, $id_negara_tujuan, $id_pengguna, $beacukai, $ongkos, '$no_telp', $id_produk, '$bukti_pembayaran')";

    if (mysqli_query($koneksi, $insert_sql)) {
        // Ambil id_riwayat dari insert terakhir
        $id_riwayat = mysqli_insert_id($koneksi);

        // Kurangi stok produk
        $new_stok = $row['stok'] - $jumlah;
        $update_stok_sql = "UPDATE produk SET stok = $new_stok WHERE id_produk = $id_produk";

        if (mysqli_query($koneksi, $update_stok_sql)) {
            // Simpan data ke tabel order
            $insert_order_sql = "INSERT INTO `order` (id_riwayat, id_produk, id_seller, jumlah, ongkos, tanggal_order, status_order, total_harga)
                VALUES ($id_riwayat, $id_produk, " . $row['id_seller'] . ", $jumlah, $ongkos, NOW(), 'pending', $totalKeseluruhan)";

            if (mysqli_query($koneksi, $insert_order_sql)) {
                echo "<script>
                        alert('Pembelian berhasil dan data order telah disimpan!');
                        window.location.href = 'detail.php?id_produk=$id_produk';
                    </script>";
            } else {
                echo "<div class='alert alert-danger'>Error saat menyimpan data order: " . mysqli_error($koneksi) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error saat memperbarui stok: " . mysqli_error($koneksi) . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($koneksi) . "</div>";
    }

}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>44INC</title>
    <link href="logo1.png" rel="website Icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: whitesmoke;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .custom-navbar {
            background: linear-gradient(45deg, #161D6F, #243A73);
            position: sticky;
            top: 0;
            z-index: 1020;
            padding: 10px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }



        .nav-link {
            font-size: 18px;
            font-weight: 400;
            transition: color 0.3s ease;
            color: #ddd;
        }

        
        .nav-link:hover {
            color: #FFF;
        }

        .navbar-toggler {
            border: none;
        }

        .btn-logout {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px; /* Perbesar padding untuk tombol lonjong */
            font-size: 16px;
            border-radius: 25px; /* Tambahkan border-radius untuk membuat tombol lonjong */
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            text-align: center; /* Untuk memastikan teks berada di tengah */
        }

        .btn-logout:hover {
            background-color: #c82333;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
        }

        .container {
            padding-left: 1px;
            padding-right: 1px;
        }

        .breadcrumb {
            position: -webkit-sticky;
        }

        /* Card Profil */
        .profile-card {
            margin: 10px;
            padding: 15px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        

        /* Footer */
        footer {
            background-color: #243A73;
            color: #fff;
            padding: 20px 0;
        }

        footer h5 {
            color: #FCCD2A;
            margin-bottom: 15px;
        }

        footer a {
            color: #FCCD2A;
            text-decoration: none;
        }

        footer a:hover {
            color: #e6b800;
            text-decoration: underline;
        }

        .copyright {
            margin-top: 20px;
            font-size: 14px;
            color: #ddd;
        }
        /*gambar */
        .navbar-brand img {
        width: 200px; /* Perbesar ukuran logo */
        margin-right: 10px;
    }
    /* main content */
    .product-image {
        max-width: 500px; /* Mengatur lebar maksimum gambar menjadi 300px */
        height: auto; /* Mempertahankan proporsi gambar */
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .form-group{

        padding-right: px;
    }
    .form-group label {
        font-weight: bold;
  
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
    .alert {
        margin-top: 20px;
    }   
    
    </style>
</head>

<body>
    <!-- Navbar -->
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
        <a class="navbar-brand" href="#"><img src="logo.png" alt="Logo"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item ">
                    <a class="nav-link " href="../home/index.php">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link active" href="../produk/index.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../ongkir/index.php">Delivery Cost</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../riwayat/index.php">History</a>
                </li>
            </ul>
        </div>

        <!-- Profil User Dropdown -->
        <div class="dropdown">
                <a class="nav-link dropdown-toggle text-light " href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class ="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['session_username']); ?></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="http://localhost/ekspor/detailuser/index.php?id=<?php echo $id_pengguna; ?>">Detail Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="http://localhost/ekspor/detailuser/edit.php?id=<?php echo $id_pengguna; ?>">Edit Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../dasboard/index.php">Jual Produk</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item " href="../../login/logout.php"><i  class="fas fa-sign-out"></i> Log Out</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../home/index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="../produk/index.php">Produk</a></li>
        <li class="breadcrumb-item"><a href="../produk/detail.php?id_produk=<?php echo $id_produk; ?>">Detail</a></li>
        <li class="breadcrumb-item active" aria-current="page">Buy</li>
    </ol>
</nav>
<div class="container">
    <div class="row">
    <div class="col-md-6">
    <h2>Nama Produk : <?php echo $row['nama_produk']; ?></h2>
    <p><strong>Harga:</strong> $ <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
    <p><strong>Sisa Stok:</strong> <?php echo $row['stok']; ?></p>
    <p><strong>Berat:</strong> <?php echo $row['berat']; ?> kg</p>
    <p><strong>Asal Produk:</strong> <?php echo $row['negara_asal']; ?></p>
    <p><strong>Seller:</strong> <?php echo $row['nama_seller']; ?></p>
</div>

    </div>

    <h3 class="mt-4">Form Pembelian</h3>
    <form method="post" enctype="multipart/form-data">
    <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Total Harga Barang</th>
                    <th>Tujuan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $row['nama_produk']; ?></td>
                    <td>$ <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td><input type="number" class="form-control" name="jumlah" required min="1" max="<?php echo $row['stok']; ?>" id="jumlah" oninput="updateTotal()"></td>
                    <td><span id="totalHarga">$ 0</span></td>
                    <td>
                        <select name="tujuan" class="form-control" required id="tujuan" onchange="updateOngkir()">
                            <option value="" disabled selected>Pilih tujuan</option>
                            <?php while ($negara = $negara_result->fetch_assoc()): ?>
                                <option value="<?php echo $negara['nama_negara']; ?>"><?php echo $negara['nama_negara']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

                <!-- Tampilkan ongkir dan bea cukai di bawah tabel -->
                <div class="row mt-4">
            <div class="col-md-6">
                <h4>Detail Biaya Tambahan</h4>
                <div class="form-group">
                    <label>Ongkir:</label>
                    <span id="ongkirHarga">$ 0</span>
                </div>
                <div class="form-group">
                    <label>Bea Cukai:</label>
                    <span id="beacukaiHarga">$ 0</span>
                </div>
                <div class="form-group">
                    <label>Total Harga Keseluruhan:</label>
                    <span id="totalHargaOngkir">$ 0</span>
                </div>
            </div>
        </div>
        <!-- Data penerima dan alamat -->
        <div class="form-group">
            <label for="nama_penerima">Nama Penerima:</label>
            <input type="text" class="form-control" name="nama_penerima" required>
        </div>
        <div class="form-group">
            <label for="alamat">Alamat:</label>
            <input type="text" class="form-control" name="alamat" required>
        </div>
        <div class="form-group">
            <label for="no_telp">Nomor Telepon:</label>
            <input type="text" class="form-control" name="no_telp" required>
        </div>
        <label for="bukti_pembayaran">Silahkan TF Ke: 0989-9778-8778-9889</label>

        <div class="form-group">
            <label for="bukti_pembayaran">Unggah Bukti Pembayaran:</label>
            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-success">Beli</button>
    </form>

    <script>
        function updateTotal() {
            var jumlah = document.getElementById("jumlah").value;
            var harga = <?php echo $row['harga']; ?>;
            var totalHarga = harga * jumlah;
            document.getElementById("totalHarga").textContent = "$ " + totalHarga.toLocaleString();
            updateOngkir();
        }

        function updateOngkir() {
            var tujuan = document.getElementById("tujuan").value;
            var berat = <?php echo $row['berat']; ?>;
            var ongkos = 0;
            var beacukai = 0;

            <?php while ($ongkir_row = $ongkir_result->fetch_assoc()): ?>
                if (tujuan === "<?php echo $ongkir_row['negara_tujuan']; ?>") {
                    ongkos = <?php echo $ongkir_row['ongkos']; ?>;
                    beacukai = <?php echo $ongkir_row['beacukai']; ?>;
                }
            <?php endwhile; ?>

            var totalongkir1 = (ongkos * berat) * document.getElementById("jumlah").value;
            var hargaBarang = <?php echo $row['harga']; ?> * document.getElementById("jumlah").value;
            var totalOngkir = (hargaBarang) + (ongkos * berat * document.getElementById("jumlah").value) + (hargaBarang * beacukai / 100);
            document.getElementById("ongkirHarga").textContent = "$ " + totalongkir1.toFixed(2);
            document.getElementById("beacukaiHarga").textContent = "$ " + (hargaBarang * beacukai / 100).toFixed(2);
            document.getElementById("totalHargaOngkir").textContent = "$ " + totalOngkir.toFixed(2);
        }
    </script>


        </div>
    </div>
</div>

    <!-- Footer -->
    <footer class="text-center mt-4">
        <div>
        <p class="copyright">Copyright © 2024 - 2024 44Ekspor & Import.</p>
            </div>
    </footer>
    <!-- Skrip JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script>
        // Script untuk mengatur tab
        $(document).ready(function() {
            $('.tabs a').click(function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                $('.tab-content').removeClass('active');
                $('.tabs a').removeClass('active');
                $(this).addClass('active');
                $('#' + tab).addClass('active');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.btn-logout').on('click', function(e) {
                e.preventDefault(); // Mencegah aksi default
                var href = $(this).attr('href');
                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: 'Anda akan keluar dari sesi ini!',
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, logout!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href; // Arahkan ke halaman logout
                    }
                });
            });
        });
    </script>
</body>

</html>
<?php 
// Tutup koneksi database
mysqli_close($koneksi); 
?>