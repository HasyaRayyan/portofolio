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

// Tangani pengiriman formulir pencarian
// Mengupdate query untuk ambil data supplier dengan negara dan nomor telepon
$searchQuery = "SELECT s.id_supplier, s.nama_supplier, s.alamat_supplier, s.email, s.no_telp, n.nama_negara, s.gambar 
                FROM supplier s 
                LEFT JOIN negara n ON s.id_negara = n.id_negara"; // Gantilah sesuai dengan join yang relevan

if (isset($_POST['search'])) {
    $searchValue = mysqli_real_escape_string($koneksi, $_POST['searchInput']);
    $searchQuery .= " WHERE s.nama_supplier LIKE '%$searchValue%' OR s.alamat_supplier LIKE '%$searchValue%' OR s.email LIKE '%$searchValue%'";
}

$ambildata = mysqli_query($koneksi, $searchQuery);

// Tangani ID produk untuk detail
$detail_id = isset($_GET['detail_id']) ? intval($_GET['detail_id']) : 0;
if ($detail_id) {
    $sql = "SELECT * FROM produk WHERE id_produk = $detail_id";
    $result = mysqli_query($koneksi, $sql);
    
    if ($result) {
        $data = mysqli_fetch_array($result);
        $nama_produk = $data['nama_produk'];
        $harga = $data['harga'];
        $stok = $data['stok'];
        $berat = $data['berat'];
        $deskripsi = $data['deskripsi'];
        $gambar = $data['gambar'];
    } else {
        $err = "Data tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="style.css">
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
                    <a class="nav-link active" href="../Supplier/index.php"><i class="fa-solid fa-id-card"></i> Suppliers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="../produk/index.php"><i class="fas fa-box"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../order/index.php"><i class="fas fa-chart-bar"></i> Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../ekspor/index.php"><i class="fa-regular fa-file"></i>|  Ekspor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../import/index.php"><i class="fa-regular fa-file-lines"></i>| Import</a>
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
            <h1 class="h2">Data Suppliers</h1>
        </div>
        <div class="">
            <!-- Tombol Pencarian di kiri atas -->
            <div class="col-lg-4">
                <form action="" method="POST" class="d-flex">
                    <input class="form-control me-2" type="search" name="searchInput" placeholder="Cari pengguna..." aria-label="Search">
                    <button class="btn" type="submit" name="search" style="background-color: #FFD700; color: white;">Cari</button>
                </form>
            </div>
            <!-- Tombol Registrasi Pengguna -->
            <div style="position: absolute; right: 20px;top: 105px;">
                <a href="tambah.php" class="btn btn-sm btn-primary btn-register">Registrasi Pengguna</a>
            </div>
        </div>

        <!-- Tabel Data Supplier -->
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th> <!-- Kolom Gambar -->
                                <th>Nama Supplier</th>
                                <th>Alamat Supplier</th>
                                <th>Email</th>
                                <th>No. Telp</th>
                                <th>Negara</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($ambildata && mysqli_num_rows($ambildata) > 0) {
                                $i = 1; // Penomoran tabel
                                while ($data = mysqli_fetch_array($ambildata)) {
                                    $id_supplier = $data['id_supplier'];
                                    $nama_supplier = $data['nama_supplier'];
                                    $alamat_supplier = $data['alamat_supplier'];
                                    $email_supplier = $data['email'];
                                    $no_telp_supplier = $data['no_telp'];
                                    $nama_negara = $data['nama_negara'];
                                    $gambar = $data['gambar'];
                            ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td>
                                    <?php if (!empty($gambar)) { ?>
                                        <img src="uploads/<?= htmlspecialchars($gambar) ?>" alt="Gambar Supplier" width="100">
                                    <?php } else { ?>
                                        <p>No Image</p>
                                    <?php } ?>
                                </td>
                                <td><?= htmlspecialchars($nama_supplier) ?></td>
                                <td><?= htmlspecialchars($alamat_supplier) ?></td>
                                <td><?= htmlspecialchars($email_supplier) ?></td>
                                <td><?= htmlspecialchars($no_telp_supplier) ?></td>
                                <td><?= htmlspecialchars($nama_negara) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="edit.php?id_supplier=<?= $id_supplier ?>" class="btn-info btn-sm">Edit</a>
                                        <a href="delete.php?id_supplier=<?= $id_supplier ?>" class="btn-danger btn-sm delete-btn">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='8'>Tidak ada data ditemukan</td></tr>";
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
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        // SweetAlert untuk tombol hapus
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.href = href;
                }
            })
        });
    </script>
</body>
</html>
