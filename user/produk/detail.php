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

// Ambil id_pengguna dari session
$id_pengguna = $_SESSION['session_id_pengguna'];

// Dapatkan ID produk dari URL
$id_produk = $_GET['id_produk'];

// Query untuk mendapatkan detail produk dan supplier
$sql = "SELECT p.nama_produk, p.harga, p.stok, p.berat, p.panjang, p.tinggi, p.lebar, p.deskripsi, p.gambar, u.nama_asli AS nama_supplier, u.gambar AS gambar_supplier, k.nama_kategori
        FROM produk p
        JOIN pengguna u ON p.id_pengguna = u.id_pengguna
        JOIN kategori k ON p.id_kategori = k.id_kategori
        WHERE p.id_produk = $id_produk";




$result = $koneksi->query($sql);

// Periksa apakah produk ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Produk tidak ditemukan!";
    exit;
}

// Query untuk mendapatkan 3 produk acak (tidak termasuk produk yang sedang ditampilkan)
$sql_random = "SELECT id_produk, nama_produk, harga, gambar 
               FROM produk 
               WHERE id_produk != $id_produk 
               ORDER BY RAND() 
               LIMIT 3";
$result_random = $koneksi->query($sql_random);
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
                /*main conten*/
                body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .container {
            display: flex;
            flex-direction: row;
            padding: 20px;
            margin: 10 10%;
        }

        .left-section {
            flex: 2;
            text-align: center;
        }

        .left-section img {
            width: 80%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }

        .right-section {
            flex: 3;
            padding-left: 20px;
            margin-top: 20px;
        }

        .right-section h1 {
            font-size: 24px;
            margin: 0;
        }

        .right-section .details {
            margin-top: 10px;
        }

        .right-section .details span {
            display: block;
            margin-bottom: 5px;
        }

        .right-section .details .highlight {
            color: blue;
        }

        .right-section .send-inquiry {
            margin-top: 20px;
        }

        .right-section .send-inquiry button {
            background-color: #ffcc00;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .right-section .product-by {
            margin-top: 20px;
        }

        .right-section .product-by .company {
            display: flex;
            align-items: center;
            background-color: #f0f4ff;
            padding: 10px;
            border-radius: 5px;
        }

        .right-section .product-by .company img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .tabs {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            border-top: 1px solid #ddd;
        }

        .tabs a {
            padding: 10px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 2px solid transparent;
        }

        .tabs a.active {
            border-bottom: 2px solid #007bff;
            color: #007bff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            margin-left: 100px;
        }
        .buy-btn {
        display: inline-block;
        background-color: #28a745;
        color: #fff;
        padding: 12px 30px;
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        text-decoration: none;
        border-radius: 50px;
        transition: background-color 0.3s, transform 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .buy-btn:hover {
        background-color: #218838;
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .buy-btn:active {
        transform: translateY(0);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

            /* Style untuk menampilkan produk acak */
            .random-products {
    display: flex;
    justify-content: space-around;
    margin-top: 30px;
}

.random-products .product-card {
    border: 1px solid #ddd;
    padding: 15px;
    width: 25%; /* Sesuaikan ukuran card */
    text-align: center;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin: 0 10px;
}

.random-products .product-card img {
    width: 100%; /* Atur gambar agar menyesuaikan card */
    height: auto;
    border-radius: 8px;
}

.random-products .product-card h5 {
    margin-top: 10px;
    font-size: 18px;
    color: #333;
}

.random-products .product-card p {
    margin-top: 5px;
    font-size: 16px;
    color: #666;
}

.random-products .product-card:hover {
    transform: translateY(-5px); /* Efek hover */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
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
                    <li><a class="dropdown-item " href="../../login/logout.php"><i  class="fas fa-sign-out"></i> Log Out</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../home/index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="../produk/index.php">produk</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail</li>
        </ol>
    </nav>

    <!-- Main content -->
    <div class="container">
        <div class="left-section">
            <img alt="Product Image" class="product-image" src="http://localhost/ekspor/gambar/gambar_produk/<?php echo $row['gambar']; ?>" />
        </div>
        <div class="right-section">
            <h1><?php echo $row['nama_produk']; ?></h1>
            <div class="details">
                <span>Harga : <span class="highlight">$ <?php echo number_format($row['harga'], 2, ',', '.'); ?></span></span>
                <span>Min. Order : <span class="highlight">1</span></span>
                <span>Stock : <span class="highlight"><?php echo $row['stok']; ?></span></span>
                <span>Kategori : <span class="highlight"><?php echo $row['nama_kategori']; ?></span></span>
            </div>
            <div class="send-inquiry">
                <a href="buy.php?id_produk=<?php echo $id_produk; ?>" id="buy-button" class="buy-btn">Buy Now</a>
            </div>
            <div class="product-by">
                <strong>Product by :</strong>
                <div class="company">
                    <img src="http://localhost/ekspor/detailuser/gambar/gambar_profil/<?php echo $row['gambar_supplier']; ?>" alt="Supplier Logo">
                    <span><?php echo $row['nama_supplier']; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- detail & spek -->
    <div class="tabs">
        <a class="active" href="#" data-tab="description">Description</a>
        <a href="#" data-tab="specification">Specification</a>
    </div>
    <div class="tab-content active" id="description">
        <h2>Description</h2>
        <p><?php echo $row['deskripsi']; ?></p>
    </div>
    <div class="tab-content" id="specification">
        <h2>Specification</h2>
        <p>Length :<?php echo $row['panjang']; ?></p>
        <p>Height : <?php echo $row['tinggi']; ?></p>
        <p>Width : <?php echo $row['lebar']; ?></p>
        <p>Weight : <?php echo $row['berat']; ?></p>
    </div>
    


    <!-- Footer -->
    <footer class="text-center mt-4">
    <div>
    <p class="copyright">Copyright © 2024 - 2024 44Ekspor & Import.</p>        </div>
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
