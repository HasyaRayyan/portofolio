<?php
session_start();

// Atur koneksi ke database
$host_db    = "localhost";
$user_db    = "root";
$pass_db    = "";
$nama_db    = "ekspor";
$koneksi    = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Variabel untuk error dan sukses
$err = "";
$success = "";

// Cek token dari URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Cek apakah token valid
    $sql1 = "SELECT * FROM pengguna WHERE reset_token = '$token'";
    $q1   = mysqli_query($koneksi, $sql1);
    $r1   = mysqli_fetch_array($q1);

    if (!$r1) {
        $err .= "<ul>Token tidak valid.</ul>";
    } else {
        // Cek apakah form password baru dikirim
        if (isset($_POST['reset_password'])) {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Validasi input kosong
            if ($new_password == '' || $confirm_password == '') {
                $err .= "<ul>Semua field harus diisi.</ul>";
            } elseif ($new_password != $confirm_password) {
                $err .= "<ul>Password baru dan konfirmasi password tidak sesuai.</ul>";
            } else {
                // Ubah password di database
                $new_password_hash = md5($new_password);
                $sql2 = "UPDATE pengguna SET password = '$new_password_hash', reset_token = NULL WHERE reset_token = '$token'";
                $q2 = mysqli_query($koneksi, $sql2);

                if ($q2) {
                    $success = "<ul>Password berhasil direset.</ul>";
                } else {
                    $err .= "<ul>Gagal mereset password, coba lagi.</ul>";
                }
            }
        }
    }
} else {
    $err .= "<ul>Token tidak ditemukan.</ul>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="login.css" />
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
<body>
    <center>
        <?php if ($err) { ?>
            <div id="login-alert" class="alert alert-danger">
                <ul><?php echo $err; ?></ul>
            </div>
        <?php } elseif ($success) { ?>
            <div id="login-alert" class="alert alert-success">
                <ul><?php echo $success; ?></ul>
            </div>
        <?php } ?>
        <div class="frame">
            <div class="login">Reset Password</div>
            <div>
                <form id="resetpasswordform" action="" method="post" role="form">
                    <div class="password" style="margin-bottom: 25px">
                        <input id="new-password" type="password" name="new_password" placeholder="Password Baru">
                    </div>
                    <div class="password" style="margin-bottom: 25px">
                        <input id="confirm-password" type="password" name="confirm_password" placeholder="Konfirmasi Password Baru">
                    </div>
                    <div class="log">
                        <input type="submit" name="reset_password" class="log" value="Reset Password"/>
                    </div>
                </form>
            </div>
        </div>
    </center>
</body>
</html>
