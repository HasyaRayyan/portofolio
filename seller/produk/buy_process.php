<?php 
// Atur koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Cek koneksi
if (!$koneksi) {
    die(json_encode(['success' => false, 'message' => 'Koneksi gagal: ' . mysqli_connect_error()]));
}
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['session_username'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk melakukan pembelian.']);
    exit();
}

// Dapatkan ID produk dari URL
$id_produk = $_GET['id_produk'];

// Query untuk mendapatkan detail produk dan supplier
$sql = "SELECT p.nama_produk, p.harga, p.stok, p.berat, p.deskripsi, p.gambar, s.nama_supplier, s.gambar AS gambar_supplier, k.nama_kategori, p.id_negara
        FROM produk p
        JOIN supplier s ON p.id_supplier = s.id_supplier
        JOIN kategori k ON p.id_kategori = k.id_kategori
        WHERE p.id_produk = $id_produk";

$result = $koneksi->query($sql);

// Periksa apakah produk ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan!']);
    exit;
}

// Ambil data negara untuk tujuan
$negara_sql = "SELECT id_negara, nama_negara FROM negara";
$negara_result = $koneksi->query($negara_sql);

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_penerima = $_POST['nama_penerima'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $jumlah = $_POST['jumlah'];
    $tujuan = $_POST['tujuan']; // Ambil tujuan dari input form

    // Pastikan semua data telah diisi
    if (empty($nama_penerima) || empty($alamat) || empty($no_telp) || empty($jumlah) || empty($tujuan)) {
        echo json_encode(['success' => false, 'message' => 'Data pembelian tidak lengkap.']);
        exit;
    }

    // Ambil negara asal dari produk
    $asal = $row['id_negara']; // Mengambil id_negara dari produk
    $total_harga = $row['harga'] * $jumlah; // Hitung total harga
    $resi = uniqid('RESI'); // Buat nomor resi unik

    // Query untuk memasukkan data ke tabel riwayat
    $insert_sql = "INSERT INTO riwayat (nama_produk, jumlah, alamat, tujuan, asal, resi, created_at, id_produk, no_telp, nama_penerima, total_harga, status) 
                   VALUES ('{$row['nama_produk']}', $jumlah, '$alamat', $tujuan, $asal, '$resi', NOW(), $id_produk, '$no_telp', '$nama_penerima', $total_harga, 0)";

    if (mysqli_query($koneksi, $insert_sql)) {
        echo json_encode(['success' => true, 'message' => 'Pembelian berhasil!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($koneksi)]);
    }
}
?>
