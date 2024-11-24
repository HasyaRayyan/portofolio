<?php
session_start();

// Atur koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Ambil ID pengguna dari parameter URL
$id_pengguna = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Variabel untuk menyimpan data pengguna
$username = "";
$password = "";
$email = "";
$no_telp = "";
$role = "";
$alamat = "";
$nama_asli = "";

// Jika ID pengguna valid, ambil data dari database
if ($id_pengguna) {
    $sql = "SELECT * FROM pengguna WHERE id_pengguna = $id_pengguna";
    $result = mysqli_query($koneksi, $sql);
    
    if ($result) {
        $data = mysqli_fetch_array($result);
        // Periksa apakah data ditemukan
        if ($data) {
            $username = $data['username'];
            $password = $data['password'];
            $email = $data['email'];
            $no_telp = $data['no_telp'];
            $role = $data['role'];
            $alamat = $data['alamat'];
            $nama_asli = $data['nama_asli'];
        } else {
            echo "Data tidak ditemukan.";
            exit();
        }
    } else {
        echo "Query error: " . mysqli_error($koneksi);
        exit();
    }
}

// Tangani pengiriman formulir
if (isset($_POST['update'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];
    $role = $_POST['role'];
    $alamat = $_POST['alamat'];
    $nama_asli = $_POST['nama_asli'];

    // Validasi input kosong
    if ($username == '' || $password == '' || $email == '' || $no_telp == '' || $role == '' || $alamat == '' || $nama_asli == '') {
        $err = "Silakan masukkan semua data.";
    } else {
        // Simpan password dalam bentuk plaintext
        $sql_update = "UPDATE pengguna SET username = '$username', password = '$password', email = '$email', no_telp = '$no_telp', role = '$role', alamat = '$alamat', nama_asli = '$nama_asli' WHERE id_pengguna = $id_pengguna";
        $result_update = mysqli_query($koneksi, $sql_update);

        if ($result_update) {
            echo "<script>alert('Data berhasil diperbarui!');window.location.href = 'index.php';</script>";
        } else {
            $err = "Gagal memperbarui data, silakan coba lagi. Error: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Pengguna</h2>
        <?php if (isset($err) && $err != '') { ?>
            <div class="alert alert-danger">
                <?php echo $err; ?>
            </div>
        <?php } ?>
        <form action="" method="post">
            <div class="mb-3">
                <label for="nama_asli" class="form-label">Nama Asli</label>
                <input type="text" class="form-control" id="nama_asli" name="nama_asli" value="<?php echo htmlspecialchars($nama_asli); ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-3">
                <label for="no_telp" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control" id="no_telp" name="no_telp" value="<?php echo htmlspecialchars($no_telp); ?>" required>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo htmlspecialchars($alamat); ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="user" <?php echo ($role == 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo ($role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" name="update" class="btn btn-primary">Update</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
