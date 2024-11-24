<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['session_username'])) {
    // Jika tidak ada username di session, arahkan pengguna ke halaman login
    header("Location: ../../login/login.php");
    exit();
}

// Ambil id_pengguna dari session
$id_pengguna = $_SESSION['session_id_pengguna'];

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

// Tangani pengiriman formulir pencarian
$searchQuery = "SELECT * FROM produk WHERE id_pengguna = '$id_pengguna'"; // Default query hanya produk pengguna

if (isset($_POST['search'])) {
    $searchValue = mysqli_real_escape_string($koneksi, $_POST['searchInput']);
    $searchQuery = "SELECT * FROM produk WHERE id_pengguna = '$id_pengguna' AND (nama_produk LIKE '%$searchValue%' OR deskripsi LIKE '%$searchValue%')";
}

$ambildata = mysqli_query($koneksi, $searchQuery);

if (!$ambildata) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="style.css">
    <style>

    </style>
</head>
<style>
    /* Styling for the sidebar */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f4f4;
}

.sidebar {
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    background-color: #161D6F;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.3s;
}

.sidebar .nav-link {
    font-size: 1.1rem;
    padding: 1rem;
    color: #ffffff;
    display: block;
    transition: all 0.3s;
}

.sidebar .nav-link:hover {
    background-color: black;
    color: #FFD700;
}

.sidebar .nav-link.active {
    background-color: #FFD700;
    color: #161D6F;
}

.sidebar .sidebar-header {
    padding: 1rem;
    text-align: center;
    color: white;
    font-size: 1.5rem;
}

.sidebar .user-info {
    padding: 1rem;
    text-align: center;
    color: #fff;
}

.sidebar .user-info img {
    width: 14   0px;
    height: 50px;
    margin-bottom: 10px;
}

.logout-section {
    padding: 1rem;
    text-align: center;
    display: flex;
    justify-content: center; /* Pusatkan tombol secara horizontal */
    align-items: center; /* Pusatkan tombol secara vertikal */
    height: 70px; /* Sesuaikan tinggi jika perlu */
    border-top: 1px solid #ddd; /* Tambahkan garis atas jika perlu */
}


.content {
    margin-left: 250px;
    padding: 20px;
}

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }
    .content {
        margin-left: 0;
    }
}

/* Styling for the table */
.table-container {
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

table thead {
    background-color: #343a40;
    color: white;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tbody tr:hover {
    background-color: #f1f1f1;
}

table tbody tr:last-child td {
    border-bottom: 0;
}

.table-container h2 {
    padding: 20px;
    background-color: #343a40;
    color: white;
    border-bottom: 2px solid #FFD700;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 16px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 8px 16px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.btn-danger:hover {
    background-color: #c82333;
}

.btn-info {
    background-color: #17a2b8;
    color: white;
    border: none;
    padding: 8px 16px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.btn-info:hover {
    background-color: #117a8b;
}

.btn-sm {
    font-size: 0.875rem;
    padding: 4px 8px;
}

/* Styling for the table */
table {
width: 100%;
border-collapse: collapse; /* Membuat garis antar sel menyatu */
margin: 20px 0;
border: 1px solid #ddd; /* Garis tepi tabel */
}

table th, table td {
padding: 12px 15px;
text-align: left;
border: 1px solid #ddd; /* Garis tepi untuk setiap sel */
}

table thead {
background-color: #343a40;
color: white;
}

table tbody tr:nth-child(even) {
background-color: #f9f9f9;
}

table tbody tr:hover {
background-color: #f1f1f1;
}

table tbody tr:last-child td {
border-bottom: 0;
}



.btn-info {
background-color: #17a2b8;
color: white;
border: none;
}

.btn-info:hover {
background-color: #117a8b;
box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); /* Tambahkan efek hover dengan bayangan */
}

.btn-danger {
background-color: #dc3545;
color: white;
border: none;
}

.btn-danger:hover {
background-color: #c82333;
box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); /* Tambahkan efek hover dengan bayangan */
}

.btn-primary {
background-color: #007bff;
color: white;
border: none;
}

.btn-primary:hover {
background-color: #0056b3;
box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); /* Tambahkan efek hover dengan bayangan */
}

/* Ukuran kecil untuk tombol */
.btn-sm {
font-size: 0.875rem;
padding: 6px 10px;
border-radius: 4px;
}

/* Untuk memperbaiki penampilan ketika tombol digunakan bersama */
.btn-group {
display: flex;
justify-content: flex-start;
gap: 5px;
}

/* main content */
.btn-search {
background-color: #FFD700;
color: white;
border: none;
}

.btn-search:hover {
background-color: #e5c100;
color: white;
}

/* Styling for Registrasi Pengguna button */
.btn-register {
background-color: #FFD700; /* Warna emas */
color: white;
border: none;
padding: 10px 20px;
font-size: 1rem;
border-radius: 5px;
transition: background-color 0.3s ease;
position: absolute; right: 20px;"
}

.btn-register:hover {
background-color: #e5c100; /* Warna hover */
}

.dropdown-toggle {
        font-size: 1.5rem; /* Ukuran font lebih besar */
    }
</style>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <div class="sidebar-header">
                <h3 class="bold">Admin Panel</h3>
            </div>
            <div class="user-info">
                <img src="../logo.png" alt="User">
                <p class="">Welcome <?php echo htmlspecialchars($_SESSION['session_username']); ?></p>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link " href="../dasboard/index.php"><i class="fas fa-home"></i> Dasboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="../jualproduk/index.php"><i class="fas fa-box"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="../order/index.php"><i class="fas fa-chart-bar"></i> Orders</a>
                </li>
            </ul>
        </div>
    </div>
    
    
<!-- Main Content -->
    <main class="content">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Data Produk</h1>
            <!-- Profil User Dropdown -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle text-dark " href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class ="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['session_username']); ?></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="http://localhost/ekspor/detailuser/index.php?id=<?php echo $id_pengguna; ?>">Detail Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="http://localhost/ekspor/detailuser/edit.php?id=<?php echo $id_pengguna; ?>">Edit Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../home/index.php">Back To Home</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item " href="../../login/logout.php"><i  class="fas fa-sign-out"></i> Log Out</a></li>
                </ul>
            </div>
        </div>
        
        
        <div class="row">
            <!-- Tombol Pencarian Produk -->
            <div class="col-lg-4">
                <form class="d-flex mb-4" role="search" method="post">
                    <input id="searchInput" class="form-control me-2" type="search" name="searchInput" placeholder="Cari produk" aria-label="Search">
                    <button class="btn btn-search" type="submit" name="search">Cari</button>
                </form>
            </div>
            <div class="col-lg-8 d-flex justify-content-end" >
                <a href="tambah.php" class="btn btn-sm btn-primary btn-register">Tambah Produk</a>
            </div>
        </div>

        <!-- Tabel Data Produk -->
        <div class="row">
    <?php
    $i = 1; // Penomoran
    if ($ambildata && mysqli_num_rows($ambildata) > 0) {
        while ($data = mysqli_fetch_array($ambildata)) {
            $id_produk = $data['id_produk'];
            $nama_produk = $data['nama_produk'];
            $harga = $data['harga'];
            $stok = $data['stok'];
            $berat = $data['berat'];
            $deskripsi = $data['deskripsi'];
            $gambar = $data['gambar']; // Jika ada gambar produk
    ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <img src="<?= $gambar ? 'http://localhost/ekspor/gambar/gambar_produk/' . $gambar : 'default_image_path.jpg' ?>" class="card-img-top" alt="gambar produk">
                <div class="card-body">
                    <h5 class="card-title"><?= $nama_produk ?></h5>
                    <p class="card-text"><?= substr($deskripsi, 0, 100) . '...' ?></p>
                    <ul class="list-unstyled">
                        <li><strong>Harga:</strong> <?= $harga ?></li>
                        <li><strong>Stok:</strong> <?= $stok ?></li>
                        <li><strong>Berat:</strong> <?= $berat ?> kg</li>
                    </ul>
                    <div class="btn-group d-flex justify-content-between">
                        <a href="edit.php?id=<?= $id_produk ?>" class="btn btn-info btn-sm">Edit</a>
                        <a href="deleteproduk.php?id_produk=<?= $id_produk ?>" class="btn btn-danger btn-sm delete-btn">Delete</a>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal" data-id="<?= $id_produk ?>">Detail</button>
                        </div>
                </div>
            </div>
        </div>
    <?php
        }
    } else {
        echo "<div class='col-12'>Tidak ada produk ditemukan</div>";
    }
    ?>
</div>


<!-- Modal Detail Produk -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Detail produk akan dimuat di sini menggunakan AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    </main>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>


    <script>
$(document).ready(function() {
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });

    
});
    </script>
    <script>
    $(document).ready(function() {
        // Ketika tombol detail diklik
        $('[data-bs-target="#detailModal"]').on('click', function() {
            var id_produk = $(this).data('id'); // Ambil ID produk dari atribut data-id

            // Melakukan AJAX untuk mendapatkan data produk
            $.ajax({
                url: 'detail.php', // File PHP untuk mengambil detail
                type: 'GET',
                data: { id: id_produk }, // Kirim ID produk
                success: function(response) {
                    // Isi modal dengan data produk
                    $('#detailContent').html(response);
                    $('#detailModal').modal('show'); // Tampilkan modal
                },
                error: function() {
                    alert('Gagal memuat data produk.');
                }
            });
        });
        
    });
</script>
<script>
    $(document).ready(function() {
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
});

</script>


    
</body>
</html>
