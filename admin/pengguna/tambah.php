<?php
session_start();

// Atur koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Variabel untuk menyimpan pesan error dan data
$err = "";
$username = "";
$password = "";
$email = "";
$no_telp = "";
$role = "";
$alamat = "";
$nama_asli = "";

// Tangani pengiriman formulir
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];
    $role = $_POST['role'];
    $alamat = $_POST['alamat'];
    $nama_asli = $_POST['nama_asli'];
    $id_negara = isset($_POST['id_negara']) ? $_POST['id_negara'] : null;

    // Validasi input kosong
    if ($username == '' || $password == '' || $email == '' || $no_telp == '' || $role == '' || $alamat == '' || $nama_asli == '' || $id_negara == '') {
        $err = "Silakan masukkan semua data.";
    } else {
        // Cek apakah username sudah ada
        $sql1 = "SELECT * FROM pengguna WHERE username = '$username'";
        $q1 = mysqli_query($koneksi, $sql1);

        if (mysqli_num_rows($q1) > 0) {
            $err = "Username <b>$username</b> sudah terdaftar.";
        } else {
            // Insert data pengguna baru
            $password_hash = $password; // Enkripsi password dengan md5 jika perlu

            $sql2 = "INSERT INTO pengguna (username, password, email, no_telp, role, alamat, nama_asli, id_negara) 
                     VALUES ('$username', '$password_hash', '$email', '$no_telp', '$role', '$alamat', '$nama_asli', '$id_negara')";
            
            $q2 = mysqli_query($koneksi, $sql2);

            if ($q2) {
                echo "<script>alert('Registrasi berhasil!');window.location.href = 'index.php';</script>";
            } else {
                $err = "Gagal melakukan registrasi, silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Tambah Pengguna</h2>
        <?php if ($err) { ?>
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
                    <option value="seller" <?php echo ($role == 'seller') ? 'selected' : ''; ?>>Seller</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_negara" class="form-label">Negara</label>
                <select class="form-select" id="id_negara" name="id_negara" required>
                    <?php
                    $query_negara = mysqli_query($koneksi, "SELECT id_negara, nama_negara FROM negara");
                    while ($negara = mysqli_fetch_assoc($query_negara)) {
                        echo "<option value='{$negara['id_negara']}'>{$negara['nama_negara']}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" name="register" class="btn btn-primary">Tambah</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
