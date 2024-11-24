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

// Update query untuk mendapatkan data produk bersama pengguna (seller)
$sql = "SELECT p.id_produk, p.nama_produk, p.harga, p.stok, p.berat, p.deskripsi, p.gambar, p.id_pengguna, u.nama_asli 
        FROM produk p
        JOIN pengguna u ON p.id_pengguna = u.id_pengguna
        WHERE u.role = 'seller'";
$result = $koneksi->query($sql);

// Tutup koneksi
mysqli_close($koneksi);
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
                /*Card*/
                .card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Lebih ringan */
        margin: 15px;
        overflow: hidden;
        transition: transform 0.3s ease-in-out; /* Smooth transition */
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Menempatkan konten secara merata */
    }

    .card:hover {
        transform: translateY(-5px); /* Sedikit efek hover */
    }

    .card img {
        height: 200px; /* Atur tinggi gambar agar konsisten */
        object-fit: cover; /* Memastikan gambar dipotong dengan proporsional */
    }

    .card-content {
        padding: 15px;
        flex-grow: 1; /* Agar card-content memenuhi ruang yang tersedia */
    }

    .card-content h2 {
        font-size: 1.2em;
        margin-bottom: 10px;
        color: #333;
    }

    .price {
        color: green;
        font-weight: bold;
        font-size: 1.1em;
        margin: 5px 0;
    }

    .stock {
        color: #555;
        margin-bottom: 10px;
    }

    .description {
        font-size: 0.9em;
        color: #666;
    }

    .card .available {
        color: green;
        font-weight: bold;
        margin-top: 10px;
    }

    .container {
        padding-left: 1px; /* Kurangi padding kiri */
        padding-right: 1px; /* Kurangi padding kanan */
    }
    .breadcrumb{
        position: -webkit-sticky; /* For Safari */
        
    }

            /* Custom style for section headers */
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

        .container-fluid {
            padding: 50px 30px;
        }

        .row {
            margin-bottom: 40px;
        }

        /* Menebalkan angka dalam stats-box */
        .stats-box .card-text {
            font-weight: bold;
             font-size: 20px; /* Optional: Bisa ditambah ukuran teks jika ingin lebih besar */
            color: black;
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
            <li class="breadcrumb-item active" aria-current="page">Produk</li>
        </ol>
    </nav>

    <!-- Main content -->
    <div class="container">
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                // Menampilkan data produk per card
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='col-md-3 d-flex align-items-stretch'>
                            <a href='detail.php?id_produk={$row['id_produk']}' class='card' style='text-decoration: none; color: inherit;'>
                                <img src='http://localhost/ekspor/gambar/gambar_produk/{$row['gambar']}' alt='Gambar Produk' class='card-img-top'>
                                <div class='card-content'>
                                    <h2>{$row['nama_produk']}</h2>
                                    <p class='price'>$ " . number_format($row['harga'], 2, ',', '.') . "</p>
                                    <p class='stock'>Available: {$row['stok']} In Stock</p>
                                    <p class='description'>Seller: {$row['nama_asli']}</p>
                                </div>
                            </a>
                        </div>";
                }
            } else {
                echo "<p>Tidak ada data produk.</p>";
            }
            ?>
        </div> <!-- Tutup div row -->
    </div> <!-- Tutup div container -->

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
