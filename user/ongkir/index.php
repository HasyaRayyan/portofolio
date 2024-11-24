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

// Ambil id_pengguna dari session
$id_pengguna = $_SESSION['session_id_pengguna'];

// Proses pengambilan data negara untuk dropdown
$result_negara = $koneksi->query("SELECT id_negara, nama_negara FROM negara");

// Proses cek ongkir
// Proses cek ongkir
$pesan = '';
$total_biaya = 0;
$ongkos = 0;
$beacukai = 0; // Inisialisasi beacukai
if (isset($_POST['cek_ongkir'])) {
    $id_negara_asal = $_POST['id_negara_asal'];
    $id_negara_tujuan = $_POST['id_negara_tujuan'];
    $harga_barang = $_POST['harga_barang'];
    $berat_barang = $_POST['berat_barang']; // Ambil berat barang dari input

    // Ambil nama negara asal dan tujuan
    $stmt_negara_asal = $conn->prepare("SELECT nama_negara FROM negara WHERE id_negara = ?");
    $stmt_negara_asal->bind_param("i", $id_negara_asal);
    $stmt_negara_asal->execute();
    $stmt_negara_asal->bind_result($nama_negara_asal);
    $stmt_negara_asal->fetch();
    $stmt_negara_asal->close();

    $stmt_negara_tujuan = $conn->prepare("SELECT nama_negara FROM negara WHERE id_negara = ?");
    $stmt_negara_tujuan->bind_param("i", $id_negara_tujuan);
    $stmt_negara_tujuan->execute();
    $stmt_negara_tujuan->bind_result($nama_negara_tujuan);
    $stmt_negara_tujuan->fetch();
    $stmt_negara_tujuan->close();

    // Ambil ongkos kirim dan bea cukai dari tabel ongkir
    $stmt_ongkos = $conn->prepare("SELECT ongkos, beacukai FROM ongkir WHERE id_negara_asal = ? AND id_negara_tujuan = ?");
    $stmt_ongkos->bind_param("ii", $id_negara_asal, $id_negara_tujuan);
    $stmt_ongkos->execute();
    $stmt_ongkos->bind_result($ongkos, $beacukai);
    $stmt_ongkos->fetch();
    $stmt_ongkos->close();

    if ($ongkos) {
        // Hitung biaya total per kg
        $biaya_ongkir_per_kg = $ongkos * $berat_barang;

        // Hitung total biaya termasuk bea cukai
        $biaya_beacukai = $harga_barang * $beacukai / 100; // Hitung bea cukai
        $total_biaya = $biaya_ongkir_per_kg + $biaya_beacukai; // Total biaya
        $pesan = "Total biaya untuk pengiriman dari $nama_negara_asal ke $nama_negara_tujuan adalah: $ " . number_format($total_biaya, 2);
        $pesan .= "<br>Detail Bea Cukai: $ " . number_format($biaya_beacukai, 2) . " (Dari harga barang $ " . number_format($harga_barang, 2) . " dengan bea cukai " . $beacukai . "%)";
        $pesan .= "<br>Biaya Ongkir ($berat_barang kg): $ " . number_format($biaya_ongkir_per_kg, 2);
    } else {
        $pesan = "Ongkos kirim untuk kombinasi $nama_negara_asal ke $nama_negara_tujuan tidak ditemukan.";
    }
}


$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>44INC</title>
    <link href = "logo1.png" rel = "website Icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            max-width: 300px; /* Mengatur lebar maksimum gambar menjadi 300px */
            height: auto; /* Mempertahankan proporsi gambar */
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
         /* main conten */


h1 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 30px;
}

form {
    background-color: #ffffff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    max-width: 800px; /* Lebarkan form */
    width: 100%; /* Pastikan form selalu memenuhi lebar kontainer */
    margin: auto;
    transition: all 0.3s ease-in-out;
}


form:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

select, input[type="number"], button {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 5px;
    border: 1px solid #bdc3c7;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

select:focus, input[type="number"]:focus {
    border-color: #3498db;
    outline: none;
}

button {
    background-color: #3498db;
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #2980b9;
}

.message {
    background-color: #dff0d8;
    padding: 20px;
    border-radius: 5px;
    margin-top: 20px;
    color: #3c763d;
    border-left: 5px solid #3c763d;
}

.error {
    background-color: #f2dede;
    padding: 20px;
    border-radius: 5px;
    margin-top: 20px;
    color: #a94442;
    border-left: 5px solid #a94442;
}

.stat-box {
    margin-top: 10px;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.stat-number {
    font-size: 14px;
    font-weight: bold;
    color: #2c3e50;
}

.button{
    margin-top: 15px;
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
                <li class="nav-item ">
                    <a class="nav-link " href="../produk/index.php">Products</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link active" href="../ongkir/index.php">Delivery Cost</a>
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
            <li class="breadcrumb-item active" aria-current="page">Ongkir</li>
        </ol>
    </nav>
     <!-- main conten -->
     <h1>Cek Ongkos Kirim</h1>
    <form method="post">
        <label for="id_negara_asal">Pilih Negara Asal:</label>
        <select name="id_negara_asal" id="id_negara_asal" required>
            <?php while ($row = $result_negara->fetch_assoc()): ?>
                <option value="<?= $row['id_negara']; ?>"><?= $row['nama_negara']; ?></option>
            <?php endwhile; ?>
        </select>
        
        <label for="id_negara_tujuan">Pilih Negara Tujuan:</label>
        <select name="id_negara_tujuan" id="id_negara_tujuan" required>
            <?php 
            // Reset pointer hasil untuk dropdown negara tujuan
            $result_negara->data_seek(0);
            while ($row = $result_negara->fetch_assoc()): ?>
                <option value="<?= $row['id_negara']; ?>"><?= $row['nama_negara']; ?></option>
            <?php endwhile; ?>
        </select>
        
        <label for="harga_barang">Harga Barang ($): </label>
        <input type="number" name="harga_barang" id="harga_barang" value="<?= isset($harga_barang) ? htmlspecialchars($harga_barang) : ''; ?>" required>

        <label for="berat_barang">Berat Barang (kg): </label>
    <input type="number" name="berat_barang" id="berat_barang" value="<?= isset($berat_barang) ? htmlspecialchars($berat_barang) : ''; ?>" required>


        <div class="stat-box events">
            <div class="stat-number">
                Biaya Beacukai :
                <?php if (isset($biaya_beacukai)) {
                    echo " $ " . number_format($biaya_beacukai, 2);
                } ?>
            </div>
        </div>
        <div class="stat-box events">
            <div class="stat-number">
                Beacukai :
                <?php if (isset($biaya_beacukai)) {
                    echo "  " . number_format($beacukai) ; echo " % ";
                } ?>
            </div>
        </div>
        <div class="stat-box events">
            <div class="stat-number">
                Harga Ongkir : <? echo " ". number_format($berat_barang) ?>
                <?php if (isset($ongkos)) {
                    echo " $ " . number_format($ongkos) ;
                } ?>
            </div>
        </div>
        <div class="stat-box events">
            <div class="stat-number">
                Total Biaya Ongkir : <? echo " ". number_format($berat_barang) ?>
                <?php if (isset($biaya_ongkir_per_kg)) {
                    echo " $ " . number_format($biaya_ongkir_per_kg) ;
                } ?>
            </div>
        </div>
        <div class="stat-box events">
            <div class="stat-number">
                total Biaya :
                <?php if (isset($total_biaya)) {
                    echo " $ " . number_format($total_biaya) ;
                } ?>
            </div>
        </div>

        <div class="button">
            <button  type="submit" name="cek_ongkir">Cek Ongkos Kirim</button>
        </div>

    </form>

    <?php if ($pesan): ?>
        <div class="message">
            <h2>Note:</h2>
            <p><?= $pesan; ?></p>
        </div>
    <?php endif; ?>
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
