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
$id_pengguna = $_SESSION['session_id_pengguna'];


// Variabel untuk menyimpan data
$err = "";

// Fungsi untuk memperbarui status
function updateStatus($id, $newStatus) {
    global $koneksi; // Akses koneksi database dari luar fungsi
    $newStatus = mysqli_real_escape_string($koneksi, $newStatus);
    $query = "UPDATE riwayat SET status = '$newStatus' WHERE id_riwayat = $id";
    return mysqli_query($koneksi, $query);
}


// Tangani pengiriman formulir pencarian
$searchQuery = "
    SELECT o.*, p.nama_produk, p.gambar, o.id_riwayat 
    FROM `order` o
    JOIN produk p ON o.id_produk = p.id_produk
    WHERE o.id_seller = '$id_pengguna'
";
// Default query untuk order pengguna

if (isset($_POST['search'])) {
    $searchValue = mysqli_real_escape_string($koneksi, $_POST['searchInput']);
    $searchQuery = "
        SELECT o.*, p.nama_produk, p.gambar 
        FROM `order` o
        JOIN produk p ON o.id_produk = p.id_produk
        WHERE o.id_seller = '$id_pengguna' 
        AND (p.nama_produk LIKE '%$searchValue%' OR o.status_order LIKE '%$searchValue%')
    ";
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
                    <a class="nav-link " href="../jualproduk/index.php"><i class="fas fa-box"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="../order/index.php"><i class="fas fa-chart-bar"></i> Orders</a>
                </li>
            </ul>
        </div>  
    </div>

    <!-- Main Content -->
    <main class="content">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Data Order</h1>
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
            <!-- Tombol Pencarian di kiri atas -->
            <div class="col-lg-4">
                <form class="d-flex mb-4" role="search" method="post">
                    <input id="searchInput" class="form-control me-2" type="search" name="searchInput" placeholder="Cari Order" aria-label="Search">
                    <button class="btn btn-search" type="submit" name="search">Cari</button>
                </form>
            </div>
        </div>
        <!-- Tabel Data -->
        <div class="row">
    <?php
    $i = 1; // Penomoran
    if ($ambildata && mysqli_num_rows($ambildata) > 0) {
        while ($data = mysqli_fetch_array($ambildata)) {
            $id_order = $data['id_order'];
            $nama_produk = $data['nama_produk'];
            $jumlah = $data['jumlah'];
            $ongkos = $data['ongkos'];
            $total_harga = $data['total_harga'];
            $status_order = $data['status_order'];
            $tanggal_order = $data['tanggal_order'];
            $gambar = $data['gambar']; // Gambar dari tabel produk
    ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <img src="<?= $gambar ? 'http://localhost/ekspor/gambar/gambar_produk/' . $gambar : 'default_image_path.jpg' ?>" class="card-img-top" alt="gambar produk">
                <div class="card-body">
                    <h5 class="card-title"><?= $nama_produk ?></h5>
                    <ul class="list-unstyled">
                        <li><strong>Jumlah:</strong> <?= $jumlah ?></li>
                        <li><strong>Ongkos:</strong> <?= $ongkos ?></li>
                        <li><strong>Total Harga:</strong> <?= $total_harga ?></li>
                        <li><strong>Status:</strong> <?= $status_order ?></li>
                        <li><strong>Tanggal:</strong> <?= $tanggal_order ?></li>
                    </ul>
                    <div class="row mb-4">
                    <a href="generate_pdf.php?id_riwayat=<?php echo $data['id_riwayat']; ?>" class="btn btn-info btn-sm">
        Generate PDF
    </a>                    </div>

                </div>
            </div>
        </div>
    <?php
        }
    } else {
        echo "<div class='col-12'>Tidak ada order ditemukan</div>";
    }
    ?>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
$(document).ready(function() {
    $('#dataTable').DataTable();

    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: 'Data ini akan dihapus secara permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',f
        }).then((result) => {
            if (result.isConfirmed) {
                document.location.href = href;
            }
        });
    });

    // Event untuk tombol logout
    $('#logout-btn').on('click', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: 'Anda akan keluar dari sesi ini!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, logout!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                document.location.href = href;
            }
        });
    });

    $('#detailModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var id_pengguna = button.data('id');

    // AJAX request

    $.ajax({
        url: 'detail.php',
        type: 'GET',    
        data: { detail_id: id_pengguna },
        success: function(response) {
            console.log(response); // Cek respons di console browser
            $('#detailContent').html(response); // Masukkan hasil ke dalam modal-body
        },
        error: function() {
            $('#detailContent').html('<p>Gagal memuat detail pengguna.</p>');
        }
    });
});


});

    </script>
</body>
</html>
