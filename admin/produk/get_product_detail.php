<?php
// Koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil id_produk dari request
$id_produk = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Query untuk mendapatkan detail produk serta negara, kategori, dan supplier
$query = "SELECT produk.*, kategori.nama_kategori, supplier.nama_supplier, negara.nama_negara
          FROM produk
          LEFT JOIN kategori ON produk.id_kategori = kategori.id_kategori
          LEFT JOIN supplier ON produk.id_supplier = supplier.id_supplier
          LEFT JOIN negara ON produk.id_negara = negara.id_negara
          WHERE produk.id_produk = $id_produk";

$result = mysqli_query($koneksi, $query);

// Jika produk ditemukan
if ($row = mysqli_fetch_assoc($result)) {
    echo "<h5>" . htmlspecialchars($row['nama_produk']) . "</h5>";
    echo "<p><strong>Harga:</strong> Rp " . htmlspecialchars($row['harga']) . "</p>";
    echo "<p><strong>Stok:</strong> " . htmlspecialchars($row['stok']) . "</p>";
    echo "<p><strong>Berat:</strong> " . htmlspecialchars($row['berat']) . " Kg</p>";
    echo "<p><strong>Kategori:</strong> " . htmlspecialchars($row['nama_kategori']) . "</p>";
    echo "<p><strong>Supplier:</strong> " . htmlspecialchars($row['nama_supplier']) . "</p>";
    echo "<p><strong>Negara Asal:</strong> " . htmlspecialchars($row['nama_negara']) . "</p>";
    echo "<p><strong>Deskripsi:</strong> " . htmlspecialchars($row['deskripsi']) . "</p>";
    echo "<img src='http://localhost/ekspor/admin/produk/uploads/" . htmlspecialchars($row['gambar']) . "' alt='" . htmlspecialchars($row['nama_produk']) . "' style='width:100%; height:auto;'>";
} else {
    echo "<p class='text-danger'>Produk tidak ditemukan.</p>";
}

// Tutup koneksi
mysqli_close($koneksi);
?>
