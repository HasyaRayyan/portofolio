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

// Ambil id pengguna dari session
$id_pengguna = $_SESSION['session_id_pengguna'];

// Ambil data riwayat berdasarkan id_pengguna
$sql = "SELECT r.*, n_asal.nama_negara AS asal, n_tujuan.nama_negara AS tujuan 
        FROM riwayat r
        LEFT JOIN negara n_asal ON r.id_negara_asal = n_asal.id_negara
        LEFT JOIN negara n_tujuan ON r.id_negara_tujuan = n_tujuan.id_negara
        WHERE r.id_pengguna = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_pengguna);
$stmt->execute();
$result = $stmt->get_result();

// Array status dan fungsi
$statusMap = [
    0 => "Pending",
    1 => "Diproses",
    2 => "Selesai",
    3 => "Dibatalkan"
];
function getStatusText($status, $statusMap) {
    return isset($statusMap[$status]) ? $statusMap[$status] : "Status tidak diketahui";
}
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
            font-family: 'Roboto', sans-serif;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            
 
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


         .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            width: calc(33% - 40px); /* 3 card per row */
            box-sizing: border-box;
        }
        .card h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .card p {
            margin: 5px 0;
        }

    </style>
</head>

<body>
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
                <li class="nav-item">
                    <a class="nav-link" href="../ongkir/index.php">Delivery Cost</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link active" href="../riwayat/index.php">History</a>
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
            <li class="breadcrumb-item active" aria-current="page">Riwayat</li>
        </ol>
    </nav>
    <center><h1>Riwayat Pembelian</h1></center>
<div class="container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <h2><?php echo htmlspecialchars($row['nama_produk']); ?></h2>
                <p><strong>Jumlah:</strong> <?php echo htmlspecialchars($row['jumlah']); ?></p>
                <p><strong>Alamat:</strong> <?php echo htmlspecialchars($row['alamat']); ?></p>
                <p><strong>Asal:</strong> <?php echo htmlspecialchars($row['asal']); ?></p>
                <p><strong>Tujuan:</strong> <?php echo htmlspecialchars($row['tujuan']); ?></p>
                <p><strong>No Resi:</strong> <?php echo htmlspecialchars($row['resi']); ?></p>
                <p><strong>Nama Penerima:</strong> <?php echo htmlspecialchars($row['nama_penerima']); ?></p>
                <p><strong>Total Harga:</strong> $ <?php echo number_format($row['total_harga'], 2, ',', '.'); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars(getStatusText($row['status'], $statusMap)); ?></p>
                <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Tidak ada data riwayat yang ditemukan.</p>
    <?php endif; ?>
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
