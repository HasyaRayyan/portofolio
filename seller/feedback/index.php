<?php
session_start();
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
// Pastikan pengguna sudah login
if (!isset($_SESSION['session_id_pengguna'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_pengguna = $_SESSION['session_id_pengguna'];
    $feedback_text = $_POST['feedback_text'];

    // Koneksi ke database

    // Masukkan feedback ke dalam tabel
    $sql = "INSERT INTO feedback (id_pengguna, feedback_text) VALUES (?, ?)";
    $stmt = $koneksi    ->prepare($sql);
    $stmt->bind_param("is", $id_pengguna, $feedback_text);

    if ($stmt->execute()) {
        echo "Feedback berhasil dikirim!";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
    $koneksi->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Feedback</title>
</head>
<body>
    <h2>Form Feedback</h2>
    <form method="POST" action="">
        <label for="feedback_text">Masukkan Feedback Anda:</label><br>
        <textarea name="feedback_text" id="feedback_text" rows="5" cols="50" required></textarea><br><br>
        <button type="submit">Kirim Feedback</button>
    </form>
</body>
</html>
