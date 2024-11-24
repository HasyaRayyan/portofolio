<?php
// Mulai session
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

// Ambil id pengguna dari parameter URL
if (!isset($_GET['id'])) {
    echo "ID pengguna tidak ditemukan.";
    exit();
}

$id_pengguna = $_GET['id'];

// Ambil data pengguna
$sql = "SELECT * FROM pengguna WHERE id_pengguna = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_pengguna);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah data pengguna ditemukan
if ($result->num_rows == 0) {
    echo "Pengguna tidak ditemukan.";
    exit();
}

$user = $result->fetch_assoc();

// Ambil URL asal (halaman sebelumnya) jika tidak ada URL sebelumnya gunakan halaman default
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'edit.php';

// Proses pembaruan data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];
    $alamat = $_POST['alamat'];
    $nama_asli = $_POST['nama_asli'];
    $id_negara = $_POST['id_negara'];
    $role = $_POST['role'];
    $gambar_baru = $user['gambar']; // Default gambar lama

    // Proses upload gambar jika ada file yang diunggah
    if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name'] != "") {
        $target_dir = "gambar/gambar_profil/";
        
        // Pastikan folder ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Buat folder jika belum ada
        }

        $file_name = preg_replace('/\s+/', '_', $_FILES['gambar']['name']); // Ganti spasi dengan _
        $target_file = $target_dir . basename($file_name);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi format gambar
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $valid_extensions)) {
            echo "Format gambar tidak valid. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
            exit;
        }

        // Pindahkan file ke folder target
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar_baru = $file_name;
        } else {
            echo "Gagal mengunggah gambar.";
            exit;
        }
    }


    // Update data ke database
    $update_sql = "UPDATE pengguna SET username = ?, email = ?, no_telp = ?, alamat = ?, nama_asli = ?, id_negara = ?, role = ?, gambar = ? WHERE id_pengguna = ?";
    $update_stmt = $koneksi->prepare($update_sql);
    $update_stmt->bind_param("ssssssssi", $username, $email, $no_telp, $alamat, $nama_asli, $id_negara, $role, $gambar_baru, $id_pengguna);

    if ($update_stmt->execute()) {
        echo "Data berhasil diperbarui.";
        header("Location: http://localhost/ekspor/seller/home/");     
    } else {
        echo "Gagal memperbarui data: " . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Edit Pengguna</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="no_telp" class="form-label">No. Telp</label>
            <input type="text" class="form-control" id="no_telp" name="no_telp" value="<?php echo $user['no_telp']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat" required><?php echo $user['alamat']; ?></textarea>
        </div>
        <div class="mb-3">
            <label for="nama_asli" class="form-label">Nama Asli</label>
            <input type="text" class="form-control" id="nama_asli" name="nama_asli" value="<?php echo $user['nama_asli']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="id_negara" class="form-label">Negara</label>
            <select class="form-select" id="id_negara" name="id_negara" required>
                <?php
                $negara_sql = "SELECT * FROM negara";
                $negara_result = $koneksi->query($negara_sql);
                while ($negara = $negara_result->fetch_assoc()) {
                    $selected = $negara['id_negara'] == $user['id_negara'] ? "selected" : "";
                    echo "<option value='{$negara['id_negara']}' $selected>{$negara['nama_negara']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="seller" <?php echo $user['role'] == 'seller' ? 'selected' : ''; ?>>Seller</option>
                <option value="buyer" <?php echo $user['role'] == 'buyer' ? 'selected' : ''; ?>>Buyer</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar Profil</label>
            <input type="file" class="form-control" id="gambar" name="gambar">
            <?php if ($user['gambar']) { ?>
                <img src="http://localhost/ekspor/gambar/gambar_profil/<?php echo $user['gambar']; ?>" alt="Gambar Profil" width="100" class="mt-2">
            <?php } ?>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?php echo $redirect_url; ?>" class="btn btn-secondary">Kembali</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Tutup koneksi
$stmt->close();
$koneksi->close();
?>
