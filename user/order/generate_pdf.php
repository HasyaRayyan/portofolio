<?php
// Atur koneksi ke database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "ekspor";
$koneksi = new mysqli($host_db, $user_db, $pass_db, $nama_db);

if ($koneksi->connect_error) {
    die("Koneksi ke database gagal: " . $koneksi->connect_error);
}

require 'C:\xampp\htdocs\ekspor\vendor\autoload.php';
use Dompdf\Dompdf;

if (isset($_GET['id_riwayat'])) {
    $id_riwayat = intval($_GET['id_riwayat']);

    // Ambil data dari tabel `riwayat` berdasarkan ID
    $query = $koneksi->prepare("
        SELECT 
            r.id_riwayat, 
            r.nama_produk, 
            r.jumlah, 
            r.total_harga, 
            r.nama_penerima, 
            r.alamat, 
            r.no_telp, 
            r.status, 
            r.beacukai, 
            r.ongkos, 
            r.resi
        FROM riwayat r 
        WHERE r.id_riwayat = ?
    ");
    $query->bind_param('i', $id_riwayat);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // Buat HTML dengan styling untuk PDF
        $html = "
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }
                h1 {
                    text-align: center;
                    color: #4CAF50;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                table, th, td {
                    border: 1px solid #ddd;
                }
                th, td {
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                }
                .footer {
                    margin-top: 20px;
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                }
            </style>
            <h1>Order Resi #" . htmlspecialchars($data['resi']) . "</h1>
            <table>
                <tr>
                    <th>Nama Produk</th>
                    <td>" . htmlspecialchars($data['nama_produk']) . "</td>
                </tr>
                <tr>
                    <th>Jumlah</th>
                    <td>" . htmlspecialchars($data['jumlah']) . "</td>
                </tr>
                <tr>
                    <th>Total Harga</th>
                    <td>$ " . number_format($data['total_harga'], 2) . "</td>
                </tr>
                <tr>
                    <th>Nama Penerima</th>
                    <td>" . htmlspecialchars($data['nama_penerima']) . "</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>" . htmlspecialchars($data['alamat']) . "</td>
                </tr>
                <tr>
                    <th>No. Telepon</th>
                    <td>" . htmlspecialchars($data['no_telp']) . "</td>
                </tr>
                <tr>
                    <th>Bea Cukai</th>
                    <td>$ " . number_format($data['beacukai'], 2) . "</td>
                </tr>
                <tr>
                    <th>Ongkos Kirim</th>
                    <td>$ " . number_format($data['ongkos'], 2) . "</td>
                </tr>
            </table>
            <div class='footer'>Dicetak menggunakan sistem ekspor kami</div>
        ";

        // Inisialisasi DOMPDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Bersihkan buffer output
        ob_end_clean();

        // Kirim PDF ke browser
        $dompdf->stream("Riwayat_Order_" . $data['id_riwayat'] . ".pdf", ["Attachment" => false]);
    } else {
        die("Riwayat dengan ID tersebut tidak ditemukan.");
    }
} else {
    die("ID riwayat tidak diberikan.");
}
?>
