<?php 
session_start();
$_SESSION['session_username'] = "";
$_SESSION['session_password'] = "";
session_destroy();

echo "<script class = alert alert-secondary >alert('logout berhasil');window.location.href = 'login.php';</script>";

?>