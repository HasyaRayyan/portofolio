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
$searchQuery = "SELECT * FROM riwayat"; // Default query

if (isset($_POST['search'])) {
    $searchValue = mysqli_real_escape_string($koneksi, $_POST['searchInput']);
    $searchQuery = "SELECT * FROM riwayat WHERE nama_produk LIKE '%$searchValue%' OR alamat LIKE '%$searchValue%' OR no_telp LIKE '%$searchValue%' OR resi LIKE '%$searchValue%'";
}

$ambildata = mysqli_query($koneksi, $searchQuery);

// Cek apakah query berhasil
if (!$ambildata) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Tangani ID pengguna untuk detail
$detail_id = isset($_GET['detail_id']) ? intval($_GET['detail_id']) : 0;
if ($detail_id) {
    $sql = "SELECT * FROM riwayat WHERE id = $detail_id";
    $result = mysqli_query($koneksi, $sql);
    
    if ($result) {
        $data = mysqli_fetch_array($result);
        // Ambil data dari tabel riwayat
        $nama_produk = $data['nama_produk'];
        $jumlah = $data['jumlah'];
        $alamat = $data['alamat'];
        $no_telp = $data['no_telp'];
        $resi = $data['resi'];
        $created_at = $data['created_at'];
        $status = $data['status'];
    } else {
        $err = "Data tidak ditemukan.";
    }
}

// Cek apakah ada permintaan untuk mengubah status
if (isset($_POST['updateStatus'])) {
    $id = intval($_POST['id']);
    $newStatus = intval($_POST['newStatus']); // Pastikan ini diubah menjadi integer

    if (updateStatus($id, $newStatus)) {
        $err = "Status berhasil diubah.";
    } else {
        $err = "Gagal mengubah status: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Riwayat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
                    <a class="nav-link" href="../produk/index.php"><i class="fas fa-box"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="../order/index.php"><i class="fas fa-chart-bar"></i> Orders</a>
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
            <h1 class="h2">Data Order</h1>
        </div>
        
        <!-- Tampilkan pesan kesalahan atau sukses -->
        <?php if ($err): ?>
            <div class="alert alert-danger"><?= $err ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-4">
                <form class="d-flex mb-4" role="search" method="post">
                    <input class="form-control me-2" type="search" name="searchInput" placeholder="Cari Order" aria-label="Search">
                    <button class="btn btn-search" type="submit" name="search">Cari</button>
                </form>
            </div>
            <div class="col-lg-8 d-flex justify-content-end" style="position: absolute; right: 20px;">
                <a href="tambah.php" class="btn btn-sm btn-primary btn-register">Tambah Riwayat</a>
            </div>
        </div>

        <!-- Card Data -->
        <div class="row">
            <?php
            $i = 1; // Penomoran
            if ($ambildata && mysqli_num_rows($ambildata) > 0) {
                while ($data = mysqli_fetch_array($ambildata)) {
                    $id = $data['id_riwayat'];
                    $nama_produk = $data['nama_produk'];
                    $jumlah = $data['jumlah'];
                    $alamat = $data['alamat'];
                    $no_telp = isset($data['no_telp']) ? $data['no_telp'] : 'Tidak ada'; // Debug
                    $resi = $data['resi'];
                    $created_at = $data['created_at'];
                    $status = $data['status'];
            ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($nama_produk); ?></h5>
                        <p class="card-text">
                            <strong>Jumlah:</strong> <?= htmlspecialchars($jumlah); ?><br>
                            <strong>Alamat:</strong> <?= htmlspecialchars($alamat); ?><br>
                            <strong>No Telpon:</strong> <?= htmlspecialchars($no_telp); ?><br>
                            <strong>Resi:</strong> <?= htmlspecialchars($resi); ?><br>
                            <strong>Tanggal Dibuat:</strong> <?= htmlspecialchars($created_at); ?><br>
                            <strong>Status:</strong> 
                            <?php
                                // Tampilkan status sebagai teks
                                switch ($status) {
                                    case 0:
                                        echo "Pending";
                                        break;
                                    case 1:
                                        echo "Diproses";
                                        break;
                                    case 2:
                                        echo "Selesai";
                                        break;
                                    case 3:
                                        echo "Dibatalkan";
                                        break;
                                    default:
                                        echo "Status tidak diketahui";
                                }
                            ?>
                        </p>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $id; ?>">
                            <select name="newStatus" class="form-select">
                                <option value="0" <?= $status == 0 ? 'selected' : ''; ?>>Pending</option>
                                <option value="1" <?= $status == 1 ? 'selected' : ''; ?>>Diproses</option>
                                <option value="2" <?= $status == 2 ? 'selected' : ''; ?>>Selesai</option>
                                <option value="3" <?= $status == 3 ? 'selected' : ''; ?>>Dibatalkan</option>
                            </select>
                            <button type="submit" name="updateStatus" class="btn btn-sm btn-warning mt-2">Ubah Status</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                echo "<div class='col-12'>Tidak ada riwayat yang ditemukan.</div>";
            }
            ?>
        </div>
    </main>
     <!-- Modal Detail -->
     <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel">Detail Riwayat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Nama Produk:</strong> <?= htmlspecialchars($nama_produk); ?></p>
                        <p><strong>Jumlah:</strong> <?= htmlspecialchars($jumlah); ?></p>
                        <p><strong>Alamat:</strong> <?= htmlspecialchars($alamat); ?></p>
                        <p><strong>No Telpon:</strong> <?= htmlspecialchars($no_telp); ?></p>
                        <p><strong>Resi:</strong> <?= htmlspecialchars($resi); ?></p>
                        <p><strong>Tanggal Dibuat:</strong> <?= htmlspecialchars($created_at); ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($status); ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
