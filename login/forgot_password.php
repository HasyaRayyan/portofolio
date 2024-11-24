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

// Cek apakah tombol lupa password ditekan
if (isset($_POST['forgot_password'])) {
    $email = $_POST['email'];

    // Validasi email kosong
    if ($email == '') {
        $err .= "<ul>Email harus diisi.</ul>";
    } else {
        // Cek apakah email terdaftar di database
        $sql1 = "SELECT * FROM pengguna WHERE email = '$email'";
        $q1   = mysqli_query($koneksi, $sql1);
        $r1   = mysqli_fetch_array($q1);

        if (!$r1) {
            $err .= "<ul>Email <b>$email</b> tidak terdaftar.</ul>";
        } else {
            // Buat token untuk reset password
            $token = md5(uniqid(rand(), true));
            $sql2 = "UPDATE pengguna SET reset_token = '$token' WHERE email = '$email'";
            $q2 = mysqli_query($koneksi, $sql2);

            if ($q2) {
                // Kirim email dengan tautan reset password
                $to = $email;
                $subject = "Reset Password Anda";
                $message = "Klik tautan berikut untuk mereset password Anda: \n";
                $message .= "http://localhost/ekspor/reset_password.php?token=$token";
                $headers = "From: noreply@ekspor.com";

                if (mail($to, $subject, $message, $headers)) {
                    $success = "<ul>Tautan reset password telah dikirim ke email Anda.</ul>";
                } else {
                    $err .= "<ul>Gagal mengirim email reset password.</ul>";
                }
            } else {
                $err .= "<ul>Gagal membuat token reset password, coba lagi.</ul>";
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
    <title>Lupa Password</title>
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
            <div class="login">Lupa Password</div>
            <div>
                <form id="forgotpasswordform" action="" method="post" role="form">
                    <div class="email" style="margin-bottom: 25px">
                        <input id="email" type="email" name="email" placeholder="Masukkan email terdaftar" required>
                    </div>
                    <div class="log">
                        <input type="submit" name="forgot_password" class="log" value="Kirim Tautan Reset"/>
                    </div>
                </form>
            </div>
        </div>
    </center>
</body>
</html>
