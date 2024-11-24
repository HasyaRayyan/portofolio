
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
                    <a class="nav-link " href="../Supplier/index.php"><i class="fa-solid fa-id-card"></i></i> Suppliers</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link active" href="../produk/index.php"><i class="fas fa-box"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="../order/index.php"><i class="fas fa-chart-bar"></i> Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../ekspor/index.php"><i class="fa-regular fa-file"></i>|  Ekspor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="../import/index.php"><i class="fa-regular fa-file-lines"></i>| Import</a>
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
            <h1 class="h2">Data Pengguna</h1>
        </div>
        <div class="row">
            <!-- Tombol Pencarian di kiri atas -->
            <!-- Tombol Pencarian di kiri atas -->
            <div class="col-lg-4">
                <form class="d-flex mb-4" role="search" method="post">
                    <input id="searchInput" class="form-control me-2" type="search" name="searchInput" placeholder="Cari pengguna" aria-label="Search">
                    <button class="btn btn-search" type="submit" name="search">Cari</button>
                </form>
            </div>
            <!-- Tombol Registrasi Penggun atas -->
            <!-- Tambah style di bagian tombol Registrasi Pengguna -->
            <div class="col-lg-8 d-flex justify-content-end" style="position: absolute; right: 20px;">
                <a href="tambah.php" class="btn btn-sm btn-primary btn-register">Registrasi Pengguna</a>
            </div>
        </div>
        <!-- Tabel Data -->
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1; // Penomoran
                    if ($ambildata && mysqli_num_rows($ambildata) > 0) {
                        while ($data = mysqli_fetch_array($ambildata)) {
                            $id_pengguna = $data['id_pengguna'];
                            $username = $data['username'];
                            $password = $data['password'];
                            $role = $data['role'];
                            $email = $data['email'];
                            $no_telp = $data['no_telp'];
                            $nama_asli = $data['nama_asli'];
                    ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= $nama_asli ?></td>
                        <td><?= $username ?></td>
                        <td><?= $password ?></td>
                        <td><?= $email ?></td>
                        <td><?= $role ?></td>
                        <td>
    <div class="btn-group">
        <a href="editform.php?id=<?= $id_pengguna ?>" class="btn-info btn-sm">Edit</a>
        <a href="delete.php?id=<?= $id_pengguna ?>" class="btn-danger btn-sm delete-btn">Delete</a>
        <button type="button" class="btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal" data-id="<?= $id_pengguna ?>">Detail</button>
    </div>
</td>

                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='7'>Tidak ada data ditemukan</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
                    
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Detail Pengguna -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($err): ?>
                        <div class="alert alert-danger"><?= $err ?></div>
                    <?php else: ?>
                        <p><strong>Nama:</strong> <?= htmlspecialchars($nama_asli) ?></p>
                        <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                        <p><strong>No. Telp:</strong> <?= htmlspecialchars($no_telp) ?></p>
                        <p><strong>Role:</strong> <?= htmlspecialchars($role) ?></p>
                        <p><strong>Alamat:</strong> <?= htmlspecialchars($alamat) ?></p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
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
