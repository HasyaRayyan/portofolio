<?php
include '../../koneksi.php'; // Pastikan koneksi ke database

// Cek apakah `detail_id` ada
if (isset($_GET['detail_id'])) {
    $detail_id = intval($_GET['detail_id']);

    // Query untuk mengambil semua detail pengguna
    $query = "SELECT * FROM pengguna WHERE id_pengguna = $detail_id";
    $result = mysqli_query($koneksi, $query);

    // Jika data ditemukan
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_array($result);
        ?>
        <p><strong>Nama:</strong> <?= htmlspecialchars($data['nama_asli']) ?></p>
        <p><strong>Username:</strong> <?= htmlspecialchars($data['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($data['email']) ?></p>
        <p><strong>No. Telp:</strong> <?= htmlspecialchars($data['no_telp']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($data['role']) ?></p>
        <p><strong>Alamat:</strong> <?= htmlspecialchars($data['alamat']) ?></p>
        <?php
    } else {
        echo "<p>Data tidak ditemukan.</p>";
    }
} else {
    echo "<p>ID pengguna tidak valid.</p>";
}
?>
