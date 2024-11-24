<?php
session_start();

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


// Hitung total pengguna dengan role 'user'
$sql_user = "SELECT COUNT(*) as total_user FROM pengguna WHERE role = 'user'";
$result_user = mysqli_query($koneksi, $sql_user);
$total_users = $result_user ? mysqli_fetch_assoc($result_user)['total_user'] : 0;

// Hitung total admin
$sql_admin = "SELECT COUNT(*) as total_admin FROM pengguna WHERE role = 'admin'";
$result_admin = mysqli_query($koneksi, $sql_admin);
$total_admins = $result_admin ? mysqli_fetch_assoc($result_admin)['total_admin'] : 0;

// Hitung total customer
$sql_riwayat = "SELECT COUNT(*) as total_riwayat FROM riwayat";
$result_riwayat = mysqli_query($koneksi, $sql_riwayat);
$total_riwayat = $result_riwayat ? mysqli_fetch_assoc($result_riwayat)['total_riwayat'] : 0;

// Hitung total produk
$sql_product = "SELECT COUNT(*) as total_product FROM produk";
$result_product = mysqli_query($koneksi, $sql_product);
$total_products = $result_product ? mysqli_fetch_assoc($result_product)['total_product'] : 0;

// Ambil data penjualan bulanan dari tabel riwayat
$sql_sales = "SELECT MONTH(created_at) as bulan, SUM(jumlah) as total_penjualan 
              FROM riwayat 
              GROUP BY bulan 
              ORDER BY bulan";
$result_sales = mysqli_query($koneksi, $sql_sales);

$sales_data = [];
while ($row = mysqli_fetch_assoc($result_sales)) {
    $sales_data[] = $row;
}

// Tutup koneksi
mysqli_close($koneksi);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
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
</head>
<body>
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
                    <a class="nav-link active" href="../dasboard/index.php"><i class="fas fa-home"></i> Dasboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="../jualproduk/index.php"><i class="fas fa-box"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="../order/index.php"><i class="fas fa-chart-bar"></i> Orders</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
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
            <!-- Cards for User Stats -->
            <div class="row">
                <div class="col-xl-3 col-md-6 ketik">
                    <div class="card bg-primary text-white mb-4 info-box" id="infoBoxUsers">
                        <div class="card-body"><h5 class="card-title">Jumlah User</h5><p class="card-text"><?= $total_users; ?></p></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-danger text-white mb-4 info-box" id="infoBoxAdmins">
                        <div class="card-body"><h5 class="card-title">Jumlah Admin</h5><p class="card-text"><?= $total_admins; ?></p></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-warning text-white mb-4 info-box" id="infoBoxCustomers">
                        <div class="card-body"><h5 class="card-title">Jumlah Penjualaan</h5><p class="card-text"><?= $total_riwayat; ?></p></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success text-white mb-4 info-box" id="infoBoxProducts">
                        <div class="card-body"><h5 class="card-title">Jumlah Produk</h5><p class="card-text"><?= $total_products; ?></p></div>
                    </div>
                </div>
            </div>

            <!-- Chart for Sales Data -->
            <div class="row mt-4">
                <div class="col-12">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </main>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
    // Data penjualan dari PHP
    var salesData = <?php echo json_encode($sales_data); ?>;
    var months = salesData.map(item => item.bulan);
    var sales = salesData.map(item => item.total_penjualan);

    // Chart.js untuk membuat diagram penjualan
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("salesChart").getContext("2d");

        new Chart(ctx, {
            type: 'line', 
            data: {
                labels: months,
                datasets: [{
                    label: 'Penjualan Bulanan',
                    data: sales,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Jumlah Penjualan'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
    <!-- Script JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


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
    //ajak user
        $(document).ready(function() {
            $('#infoBoxUsers').on('click', function() {
                $.ajax({
                    url: 'fetch_users.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var tableBody = $('#usersTable tbody');
                        tableBody.empty();
                        data.forEach(function(user) {
                            var row = '<tr>' +
                                '<td>' + user.nama_asli + '</td>' +
                                '<td>' + user.email + '</td>' +
                                '<td>' + user.no_telp + '</td>' +
                                '<td>' + user.alamat + '</td>' +
                                '</tr>';
                            tableBody.append(row);
                        });
                        $('#usersModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                    }
                });
            });

            $('#infoBoxAdmins').on('click', function() {
                $.ajax({
                    url: 'fetch_admins.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var tableBody = $('#usersTable tbody');
                        tableBody.empty();
                        data.forEach(function(admin) {
                            var row = '<tr>' +
                                '<td>' + admin.nama_asli + '</td>' +
                                '<td>' + admin.email + '</td>' +
                                '<td>' + admin.no_telp + '</td>' +
                                '<td>' + admin.alamat + '</td>' +
                                '</tr>';
                            tableBody.append(row);
                        });
                        $('#usersModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                    }
                });
            });
        });
    </script>
</body>
</html>

