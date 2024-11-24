<?php
session_start();

// Atur koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Variabel untuk error
$err = "";

// Mengambil data negara dari tabel negara
$sql_negara = "SELECT id_negara, nama_negara FROM negara";
$query_negara = mysqli_query($koneksi, $sql_negara);

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];
    $alamat = $_POST['alamat'];
    $nama_asli = $_POST['nama_asli'];
    $id_negara = $_POST['id_negara'];
    $role = $_POST['role']; // Ambil role dari form

    // Validasi input kosong
    if ($username == '' || $password == '' || $email == '' || $no_telp == '' || $alamat == '' || $nama_asli == '' || $id_negara == '' || $role == '') {
        $err .= "<ul>Silakan masukkan semua data.</ul>";
    } else {
        // Validasi panjang password
        if (strlen($password) < 6) {
            $err .= "<ul>Password harus minimal 6 karakter.</ul>";
        } else {
            // Cek apakah username sudah ada
            $sql1 = "SELECT * FROM pengguna WHERE username = '$username'";
            $q1 = mysqli_query($koneksi, $sql1);
            $r1 = mysqli_fetch_array($q1);

            if ($r1) {
                $err .= "<ul>Username <b>$username</b> sudah terdaftar.</ul>";
            } else {
                // Cek apakah email sudah ada
                $sql_email = "SELECT * FROM pengguna WHERE email = '$email'";
                $q_email = mysqli_query($koneksi, $sql_email);
                $r_email = mysqli_fetch_array($q_email);

                if ($r_email) {
                    $err .= "<ul>Email <b>$email</b> sudah terdaftar.</ul>";
                } else {
                    // Insert data ke database
                    $password_hashed = $password; // Gunakan hashing untuk password di sistem nyata
                    $sql2 = "INSERT INTO pengguna (username, password, email, no_telp, role, alamat, nama_asli, id_negara) 
                             VALUES ('$username', '$password_hashed', '$email', '$no_telp', '$role', '$alamat', '$nama_asli', '$id_negara')";
                    $q2 = mysqli_query($koneksi, $sql2);

                    if ($q2) {
                        echo "<script>alert('Registrasi berhasil!');window.location.href = 'login.php';</script>";
                    } else {
                        $err .= "<ul>Gagal melakukan registrasi, silakan coba lagi.</ul>";
                    }
                }
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
    <title>Registrasi</title>
    <link rel="stylesheet" href="login.css" />
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
<style>
    @import url("https://fonts.googleapis.com/css2?family=Mukta:wght@200;300;400;500;600;700;800&display=swap");

* {
    margin: 0;
    padding: 0;
    font-family: system-ui;
}

body {
    height: 100vh;
    width: 100vw;
    display: flex;
    justify-content: center;
    align-items: center;
    background: url(kapal.png);
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
}

body:before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.164);
}

.frame {
    height: 630px;
    width: 460px;
    backdrop-filter: blur(5px);
    border: 1px solid white;
    display: flex;
    flex-direction: column;
    align-items: center;
    border-radius: 10px;
    box-shadow: 0px 0px 5px white;
}

.login {
    font-size: 50px;
    font-weight: bold;
    color: rgb(0, 0, 0);
    margin-top: 0.5rem;
    margin-bottom: 2.5rem;
}

.email,
.password {
    width: 90%;
    height: auto;
    display: flex;
    align-items: center;
    margin: 0.5rem;
    position: relative;
}

.email input,
.password input {
    padding: 8px 8px 8px 15px;
    background: transparent;
    border: none;
    width: 100%;
    border: 1px solid #cecece;
    border-radius: 50px;
    font-size: 17px;
    outline: none;
    font-weight: bold;
}

.email input::placeholder,
.password input::placeholder {
    color: white;
    font-weight: bold;
}

.email img,
.password img {
    position: absolute;
    right: 0.7rem;
}

.remember_forgot {
    height: auto;
    width: 90%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.5rem;
}

.remember_forgot .remember {
    height: auto;
    width: auto;
    display: flex;
    align-items: center;
    font-size: 17px;
    color: white;
    font-weight: bold;
}

.remember_forgot .remember input {
    height: 15px;
    width: 15px;
    margin-right: 0.2rem;
}

.remember_forgot .forgot a {
    font-size: 17px;
    text-decoration: none;
    color: white;
    font-weight: bold;
}

.log {
    width: 90%;
    padding: 10px;
    margin-top: 1rem;
    font-size: 25px;
    font-weight: bold;
    outline: none;
    border: none;
    border-radius: 50px;
    cursor: pointer;
}

.reg {
    margin-top: 1.5rem;
    color: white;
}

.reg a {
    font-weight: bold;
    color: white;
}

.form-group {
    width: 90%;
    margin: 0.5rem 0;
    margin-top: 30px;
}

.input-group {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.input-group .form-control {
    flex: 1;
    padding: 8px 15px;
    border: 1px solid #cecece;
    border-radius: 50px;
    font-size: 16px;
    outline: none;
    font-weight: bold;
}

.input-group .form-control::placeholder {
    color: white;
    font-weight: bold;
}

.log {
    width: 90%;
    padding: 10px;
    margin-top: 1rem;
    font-size: 25px;
    font-weight: bold;
    outline: none;
    border: none;
    border-radius: 50px;
    cursor: pointer;
}

#login-alert {
    width: 90%;
    padding: 15px;
    background-color: #f2dede;
    color: #a94442;
    border: 1px solid #ebccd1;
    border-radius: 5px;
    text-align: center;
}
</style>
<body>
<center>
        <?php if ($err) { ?>
            <div id="login-alert" class="alert alert-danger">
                <ul><?php echo $err; ?></ul>
            </div>
        <?php } ?>
        <div class="frame">
            <div class="login">Registrasi</div>
            <div>
                <form id="registerform" action="" method="post" role="form">
                    <!-- Nama Asli -->
                    <div class="form-group email">
                        <input type="text" name="nama_asli" class="form-control" placeholder="Nama Asli" value="<?php echo isset($nama_asli) ? $nama_asli : ''; ?>" required>
                    </div>

                    <!-- Username dan Password bersebelahan -->
                    <div class="form-group input-group email">
                        <input type="text" name="username" class="form-control" placeholder="Username" value="<?php echo isset($username) ? $username : ''; ?>" required>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>

                    <!-- Email dan Nomor Telepon bersebelahan -->
                    <div class="form-group input-group email">
                        <input type="email" name="email" class="form-control" placeholder="Email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                        <input type="text" name="no_telp" class="form-control" placeholder="Nomor Telepon" value="<?php echo isset($no_telp) ? $no_telp : ''; ?>" required>
                    </div>

                    <!-- Alamat -->
                    <div class="form-group email">
                        <input type="text" name="alamat" class="form-control" placeholder="Alamat" value="<?php echo isset($alamat) ? $alamat : ''; ?>" required>
                    </div>

                    <!-- Pilihan Negara -->
                    <div class="form-group email">
                        <select name="id_negara" class="form-control" required>
                            <option value="">Pilih Negara</option>
                            <?php while ($row = mysqli_fetch_assoc($query_negara)) { ?>
                                <option value="<?php echo $row['id_negara']; ?>"><?php echo $row['nama_negara']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <!-- Pilihan Role -->
<div class="form-group email">
    <select name="role" class="form-control" required>
        <option value="">Pilih Role</option>
        <option value="user">User</option>
        <option value="seller">Seller</option>
    </select>
</div>


                    <!-- Tombol Register -->
                    <div class="log">
                        <input type="submit" name="register" class="log" value="Register"/>
                    </div>
                    <a href="login.php">Login</a>
                </form>
            </div>
        </div>
    </center>
</body>
</html>
