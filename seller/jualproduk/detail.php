<?php
// detailproduk.php

// Pastikan koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil id produk dari query string
if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];

    // Query untuk mengambil data produk berdasarkan id_produk
    $query = "SELECT * FROM produk WHERE id_produk = '$id_produk'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        ?>
                <img src="http://localhost/ekspor/gambar/gambar_produk/<?php echo $data['gambar']; ?>" alt="Gambar Produk" class="img-fluid">
        <h5>Nama Produk: <?php echo htmlspecialchars($data['nama_produk']); ?></h5>
        <p><strong>Harga:</strong> <?php echo number_format($data['harga'], 2); ?></p>
        <p><strong>Stok:</strong> <?php echo $data['stok']; ?></p>
        <p><strong>Berat:</strong> <?php echo $data['berat']; ?> kg</p>
        <p><strong>Deskripsi:</strong> <?php echo htmlspecialchars($data['deskripsi']); ?></p>
        <p><strong>Dimensi:</strong> <?php echo $data['panjang'] . ' x ' . $data['tinggi'] . ' x ' . $data['lebar']; ?> cm</p>
        <p><strong>Negara:</strong> <?php
            // Ambil nama negara
            $id_negara = $data['id_negara'];
            $negara_query = "SELECT nama_negara FROM negara WHERE id_negara = '$id_negara'";
            $negara_result = mysqli_query($koneksi, $negara_query);
            if ($negara_result && mysqli_num_rows($negara_result) > 0) {
                $negara = mysqli_fetch_assoc($negara_result);
                echo $negara['nama_negara'];
            }
        ?></p>
        <p><strong>Bahan:</strong> <?php
            // Ambil nama bahan
            $id_bahan = $data['id_bahan'];
            $bahan_query = "SELECT bahan FROM bahan WHERE id_bahan = '$id_bahan'";
            $bahan_result = mysqli_query($koneksi, $bahan_query);
            if ($bahan_result && mysqli_num_rows($bahan_result) > 0) {
                $bahan = mysqli_fetch_assoc($bahan_result);
                echo $bahan['bahan'];
            }
        ?></p>
        <?php
    } else {
        echo "Produk tidak ditemukan.";
    }
} else {
    echo "ID produk tidak tersedia.";
}
?>
