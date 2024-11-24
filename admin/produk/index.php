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

// Tangani pengiriman formulir pencarian
$searchQuery = "SELECT produk.*, kategori.nama_kategori 
                FROM produk 
                LEFT JOIN kategori ON produk.id_kategori = kategori.id_kategori";

if (isset($_POST['search'])) {
    $searchValue = mysqli_real_escape_string($koneksi, $_POST['searchInput']);
    $searchQuery = "SELECT produk.*, kategori.nama_kategori 
                    FROM produk 
                    LEFT JOIN kategori ON produk.id_kategori = kategori.id_kategori
                    WHERE nama_produk LIKE '%$searchValue%' OR deskripsi LIKE '%$searchValue%'";
}

$ambildata = mysqli_query($koneksi, $searchQuery);

// Cek apakah query berhasil
if (!$ambildata) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
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
    background-color: #343a40;
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
    color: #fff;
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
    width: 60px;
    height: 60px;
    border-radius: 50%;
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


    </style>



<!-- Sidebar dan bagian lainnya tetap sama -->
    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <div class="sidebar-header">
                <h3 class="bold">Admin Panel</h3>
            </div>
            <div class="user-info">
                <img src="http://localhost/ekspor/gambar/Logo%20web%20warna%20hitam%20wa.png" alt="User">
                <p class="">Welcome <?php echo htmlspecialchars($_SESSION['session_username']); ?></p>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link " href="../beranda/index.php"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="../pengguna/index.php"><i class="fas fa-users"></i> Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="../produk/index.php"><i class="fas fa-box"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="../order/index.php"><i class="fas fa-chart-bar"></i> Orders</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="../ongkir/index.php"><i class="fa-regular fa-file-lines"></i> Ongkir</a>
                </li>
            </ul>
        </div>
        
        <!-- Logout Button -->
        <div class="logout-section">
            <a href="../../login/logout.php" id="logout-btn" class="btn btn-danger btn-sm w-100">Log Out</a>
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
                    <li><a class="dropdown-item  " id="logout-btn" href="../../login/logout.php"><i  class="fas fa-sign-out"></i> Log Out</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Tombol Pencarian di kiri atas -->
        <div class="col-lg-4">
            <form action="" method="POST" class="d-flex">
                <input class="form-control me-2" type="search" name="searchInput" placeholder="Cari produk..." aria-label="Search">
                <button class="btn" type="submit" name="search" style="background-color: #FFD700; color: white;">Cari</button>
            </form>
        </div>
        
        <!-- Tombol Tambah Produk & Kategori -->
        <div style="position: absolute; right: 20px; top: 105px;">

            <a href="tambah.php" class="btn btn-sm btn-primary btn-register">Tambah Produk</a>
        </div>

        
<!-- Tabel Data -->
<div class="row mt-4">
        <?php 
        $no = 1;
        while ($row = mysqli_fetch_assoc($ambildata)): 
            $gambar = "http://localhost/ekspor/gambar/gambar_produk/" . htmlspecialchars($row['gambar']);
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="<?php echo $gambar; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['nama_produk']); ?></h5>
                    <p class="card-text"><strong>Harga:</strong> Rp <?php echo htmlspecialchars($row['harga']); ?></p>
                    <p class="card-text"><strong>Stok:</strong> <?php echo htmlspecialchars($row['stok']); ?></p>
                    <p class="card-text"><strong>Berat:</strong> <?php echo htmlspecialchars($row['berat']); ?> Kg</p>
                    <p class="card-text"><strong>Kategori:</strong> <?php echo htmlspecialchars($row['nama_kategori']); ?></p>
                </div>
                <div class="card-footer">
                    <div class="btn-group">
                        <a href="edit.php?id=<?= $row['id_produk'] ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-edit"></i> Edit</a>
                        <a href="delete.php?id=<?= $row['id_produk'] ?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

<!-- Modal untuk Detail Produk -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="detailContent">
        <!-- Detail produk akan dimuat di sini -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>






    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    <script>
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
        $(document).ready(function() {
    // Konfirmasi sebelum menghapus produk
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data produk akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.location.href = href;
            }
        });
    });
});

    </script>
    <script>
        $(document).ready(function() {
    // Event listener ketika tombol Detail diklik
    $('button[data-bs-target="#detailModal"]').on('click', function() {
        var id_produk = $(this).data('id');

        // Lakukan AJAX request ke file PHP yang akan mengambil detail produk
        $.ajax({
            url: 'get_product_detail.php',  // Buat file PHP untuk menangani request ini
            type: 'GET',
            data: { id: id_produk },
            success: function(response) {
                // Tampilkan data yang didapatkan di modal
                $('#detailContent').html(response);
            },
            error: function() {
                $('#detailContent').html('<p class="text-danger">Gagal memuat detail produk.</p>');
            }
        });
    });
});

    </script>
</body>
</html>





