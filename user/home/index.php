<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['session_username'])) {
    // Jika tidak ada username di session, arahkan pengguna ke halaman login
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

// Hitung total admin
$sql_user = "SELECT COUNT(*) as total_user FROM pengguna WHERE role = 'user'";
$result_user = mysqli_query($koneksi, $sql_user); // Corrected line
$total_user = $result_user ? mysqli_fetch_assoc($result_user)['total_user'] : 0;

// Hitung total produk
$sql_product = "SELECT COUNT(*) as total_product FROM produk";
$result_product = mysqli_query($koneksi, $sql_product);
$total_products = $result_product ? mysqli_fetch_assoc($result_product)['total_product'] : 0;

// Hitung total suplier
$sql_riwayat = "SELECT COUNT(*) as total_riwayat FROM riwayat"; // Corrected line
$result_riwayat = mysqli_query($koneksi, $sql_riwayat);
$total_riwayat = $result_riwayat ? mysqli_fetch_assoc($result_riwayat)['total_riwayat'] : 0;

// Hitung total suplier
$sql_seller = "SELECT COUNT(*) as total_seller FROM pengguna WHERE role = 'seller'";
$result_seller = mysqli_query($koneksi, $sql_seller); // Corrected line
$total_seller = $result_seller ? mysqli_fetch_assoc($result_seller)['total_seller'] : 0;

// Hitung total negara
$sql_negara = "SELECT COUNT(*) as total_negara FROM negara"; // Mengganti 'orders' menjadi 'negara'
$result_negara = mysqli_query($koneksi, $sql_negara);
$total_negara = $result_negara ? mysqli_fetch_assoc($result_negara)['total_negara'] : 0;
;



// Tutup koneksi
mysqli_close($koneksi);
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
         body {
    background-color: whitesmoke;
}

/* Bagian statistik */
.stats-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    width: 80%;
}
.stat-box {
    flex: 1;
    padding: 20px;
    color: #ffffff;
    border-radius: 10px;
    margin: 0 5px;
}
.suppliers {
    background-color: #3b5ba9;
}
.products {
    background-color: #0a1f5b;
}
.market-info {
    background-color: #b71c1c;
}
.events {
    background-color: #e64a19;
}
.representative {
    background-color: #d4d700;
}
.stat-number {
    font-size: 36px;
    font-weight: bold;
}
.stat-label {
    font-size: 18px;
}

/* Produk */
.product-card {
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    background-color: #161d6f;
    margin-bottom: 20px;
    transition: transform 0.2s ease-in-out;
}
.product-card:hover {
    transform: scale(1.05);
}
.product-card img {
    width: 100%;
    height: auto;
}
.product-card .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(22, 29, 111, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
}
.product-card .overlay h5 {
    color: white;
    font-size: 20px;
    font-weight: bold;
}

/* Bagian judul */
.hero {
    background-color: whitesmoke;
    color: black;
    text-align: center;
    padding: 50px 0;
}
.hero h1 {
    font-size: 48px;
}
.hero1 {
    font-size: 100px;
    background-color: whitesmoke;
    color: black;
    text-align: center;
    padding: 50px 0;
}

/* Animasi */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.container-fluid {
            padding: 50px 30px;
        }
        .section-header {
            font-size: 24px;
            font-weight: bold;
            color: #161D6F;
            margin: 40px 0 20px;
        }

        .section-header a {
            color: #161D6F;
        }

        .section-header a:hover {
            text-decoration: underline;
        }
        .p1 {
            color: #FCCD2A;
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
                <li class="nav-item active">
                    <a class="nav-link active" href="../home/index.php">Home</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="../produk/index.php">Products</a>
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
                    <li><a class="dropdown-item " href="../../login/logout.php"><i  class="fas fa-sign-out"></i> Log Out</a></li>
                </ul>
            </div>
        </div>
    </nav>
<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="kapal.png" class="d-blosk w-100" alt="Image 1"> <!-- Ensure to have different images for each slide -->
            </div>
            <div class="carousel-item">
                <img src="kapal.png" class="d-block w-100" alt="Image 2">
            </div>
            <div class="carousel-item">
                <img src="kapal.png" class="d-block w-100" alt="Image 3">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Ekspor and Import Is Easy Right Now</h1>
        <p class="p1">Send Your Items Now </p>
    </div>

    <!-- Stats Section -->
     <div class="hero1">
        <H1>Our Data</H1>
     </div>
    <div class="stats-container">
        <div class="stat-box suppliers">
            <div class="stat-number"><?= $total_user ?></div>
            <div class="stat-label">Users</div>
        </div>
        <div class="stat-box products">
            <div class="stat-number"><?= $total_products ?></div>
            <div class="stat-label">Products</div>
        </div>
        <div class="stat-box market-info">
            <div class="stat-number"><?= $total_seller ?></div>
            <div class="stat-label">Suppliers</div>
        </div>
        <div class="stat-box events">
            <div class="stat-number"><?= $total_negara ?></div>
            <div class="stat-label">Country</div>
        </div>
        <div class="stat-box representative">
            <div class="stat-number"><?= $total_riwayat?></div>
            <div class="stat-label">Total Orders</div>
        </div>
    </div>
    <div class = "Warna">

    </div>


    <div class="container-fluid">
        <!-- Agricultural Products Section -->
        <div class="section-header">Products</div>
        <div class="row">
            <div class="col-md-4">
                <div class="product-card">
                    <img src="gopix.png" alt="Product Image 1">
                    <div class="overlay">
                        <h5>Google Pixel</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="product-card">
                    <img src="bmw.png" alt="Product Image 2">
                    <div class="overlay">
                        <h5>BMW Series</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="product-card">
                    <img src="buah.png" alt="Product Image 3">
                    <div class="overlay">
                        <h5>Fruits</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Apparel Section -->
        <div class="section-header">Produk <a href="../produk/index.php">View All</a></div>
        <div class="row">
            <div class="col-md-4">
                <div class="product-card">
                    <img src="kopi.png" alt="Product Image 4">
                    <div class="overlay">
                        <h5>Elepanth black Coffee</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="product-card">
                    <img src="GTR.png" alt="Product Image 5">
                    <div class="overlay">
                        <h5>Nissan GTR-R34</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="product-card">
                    <img src="stussy.png" alt="Product Image 6">
                    <div class="overlay">
                        <h5>Stussy</h5>
                    </div>
                </div>
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
