<?php 
session_start();

//atur koneksi ke database
$host_db    = "localhost";
$user_db    = "root";
$pass_db    = "";
$nama_db    = "ekspor";
$koneksi    = mysqli_connect($host_db,$user_db,$pass_db,$nama_db);

//atur variabel
$err        = "";
$username_or_email   = "";



// Jika sudah login, arahkan ke halaman yang sesuai
if(isset($_SESSION['session_username'])){
    if ($_SESSION['session_role'] == 'admin') {
        header("Location: ../admin/pengguna/index.php");
    } elseif ($_SESSION['session_role'] == 'user') {
        header("location: http://localhost/ekspor/user/home/");
    } elseif ($_SESSION['session_role'] == 'seller') {
        header("location:http://localhost/ekspor/seller/home/index.php");
    }
    exit();
}

// Mengecek form login
if(isset($_POST['login'])) {
    $username_or_email   = $_POST['username_or_email'];
    $password   = $_POST['password'];

    // Validasi input kosong
    if($username_or_email == '' || $password == '') {
        $err .= "<ul>Silakan masukkan username/email dan juga password.</ul>";
    } else {
        // Query untuk mendapatkan data pengguna dari tabel 'pengguna'
        $sql1 = "SELECT * FROM pengguna WHERE username = ? OR email = ?";
        $stmt1 = mysqli_prepare($koneksi, $sql1);
        mysqli_stmt_bind_param($stmt1, "ss", $username_or_email, $username_or_email);
        mysqli_stmt_execute($stmt1);
        $result1 = mysqli_stmt_get_result($stmt1);
        $r1 = mysqli_fetch_array($result1);

        if ($r1) {
            // Cek password
            if ($r1['password'] != $password) {
                $err .= "<ul>Password yang dimasukkan tidak sesuai.</ul>";
            } else {
                // Jika login berhasil, buat session
                $_SESSION['session_username'] = $r1['username'];
                $_SESSION['session_role'] = $r1['role'];
                $_SESSION['session_nam_asli'] = $r1['nama_asli'];
                $_SESSION['session_id_pengguna'] = $r1['id_pengguna'];

                // Arahkan pengguna sesuai peran mereka
                if ($r1['role'] == 'admin') {
                    echo "<script>alert('Login sebagai Admin berhasil');window.location.href = '../admin/pengguna/index.php';</script>";
                } elseif ($r1['role'] == 'user') {
                    echo "<script>alert('Login sebagai User berhasil');window.location.href = '../user/home/index.php';</script>";
                } elseif ($r1['role'] == 'seller') {
                    echo "<script>alert('Login sebagai Seller berhasil');window.location.href = '../seller/home/index.php';</script>";
                }
                exit();
            }
        } else {
            // Jika tidak ditemukan pengguna
            $err .= "<ul>Username atau Email <b>$username_or_email</b> tidak tersedia.</ul>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
    height: 500px; /* Atur ketinggian sesuai kebutuhan */
    width: 450px;  /* Atur lebar sesuai kebutuhan */
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
    height: 50px;
    width: 400px;
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






</style>
<body>
    <center>
        <?php if($err) { ?>
            <div id="login-alert" class="alert alert-danger">
                <ul><?php echo $err; ?></ul>
            </div>
        <?php } ?>
        <div class="frame">
            <div class="login">Login</div>
            <div>
                <form id="loginform" action="" method="post" role="form">       
                    <div class="email" style="margin-bottom: 25px">
                        <input id="login-username" type="text" name="username_or_email" value="<?php echo $username_or_email ?>" placeholder="username atau email">                                      
                    </div> 
                    <div class="password" style="margin-bottom: 25px">
                        <input id="login-password" type="password" name="password" placeholder="password">
                    </div>
                    <div class="log">
                        <input type="submit" name="login" class="log" value="Login"/>
                    </div>
                </form>
                <a href="regist.php">Register</a>
            </div>
        </div>
    </center>
</body>
</html>
