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

// Tangani pengambilan data dari tabel ongkir
$searchQuery = "SELECT o.id_pengiriman, 
                       asal.nama_negara AS negara_asal, 
                       tujuan.nama_negara AS negara_tujuan, 
                       o.ongkos, 
                       o.beacukai 
                FROM ongkir o
                JOIN negara asal ON o.id_negara_asal = asal.id_negara
                JOIN negara tujuan ON o.id_negara_tujuan = tujuan.id_negara";

// Tangani pencarian
if (isset($_POST['search'])) {
    $searchValue = mysqli_real_escape_string($koneksi, $_POST['searchInput']);
    $searchQuery .= " WHERE asal.nama_negara LIKE '%$searchValue%' 
                      OR tujuan.nama_negara LIKE '%$searchValue%'";
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
    <title>Responsive Sidebar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="style.css">
    <style>

    </style>
</head>
<body>

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
                    <a class="nav-link " href="../produk/index.php"><i class="fas fa-box"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../order/index.php"><i class="fas fa-chart-bar"></i> Orders</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link active" href="../ongkir/index.php"><i class="fa-regular fa-file-lines"></i> Ongkir</a>
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
                    <li><a class="dropdown-item " href="../../login/logout.php"><i  class="fas fa-sign-out"></i> Log Out</a></li>
                </ul>
            </div>
        </div>
        <div>
            <!-- Tombol Pencarian di kiri atas -->
            <div class="col-lg-4">
                <form action="" method="POST" class="d-flex">
                    <input class="form-control me-2" type="search" name="searchInput" placeholder="Cari pengguna..." aria-label="Search">
                    <button class="btn" type="submit" name="search" style="background-color: #FFD700; color: white;">Cari</button>
                </form>
            </div>
            <!-- Tombol Registrasi Penggun atas -->
            <div style="position: absolute; right: 20px;top: 105px;">
                <a href="tambahnegara.php" class="btn btn-sm btn-primary btn-register">Tambah Negara</a>
                <a href="tambah.php" class="btn btn-sm btn-primary btn-register">Tambah Ongkir</a>

            </div>
        </div>
        <!-- Tabel Data -->
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>NO</th>
                    <th>Negara Asal</th>
                    <th>Negara Tujuan</th>
                    <th>Ongkos</th>
                    <th>Bea Cukai</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1; // Penomoran
                if ($ambildata && mysqli_num_rows($ambildata) > 0) {
                    while ($data = mysqli_fetch_assoc($ambildata)) {
                        echo "<tr>
                                <td>{$i}</td>
                                <td>{$data['negara_asal']}</td>
                                <td>{$data['negara_tujuan']}</td>
                                <td>{$data['ongkos']}</td>
                                <td>{$data['beacukai']}</td>
                                                    <td>
                        <a href='edit.php?id={$data['id_pengiriman']}' class='btn btn-warning btn-sm'>Edit</a>
                        <a href='delete.php?id={$data['id_pengiriman']}' class='btn btn-danger btn-sm delete-btn'>Delete</a>
                    </td>
                              </tr>";
                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Tidak ada data ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
                    
                </div>
            </div>
        </div>
    </main>

    
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    <script>
$(document).ready(function() {
    $('#dataTable').DataTable();

    $(document).on('click', '.delete-btn', function(e) {
    e.preventDefault();
    var href = $(this).attr('href');
    Swal.fire({
        title: 'Apakah kamu yakin?',
        text: 'Data ini akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
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
