<?php
// Pastikan tidak ada spasi atau karakter sebelum tag <?php ini

// Aktifkan error reporting untuk debugging selama pengembangan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set header ke JSON di awal untuk memastikan output yang bersih
header('Content-Type: application/json');

// Validasi path file sebelum memanggilnya
$paths = [
    'koneksi' => 'includes/koneksi.php',
    'functions' => 'includes/functions.php',
    'config_midtrans' => 'includes/config_midtrans.php',
    'autoload' => 'vendor/autoload.php'
];

foreach ($paths as $key => $path) {
    if (!file_exists($path)) {
        echo json_encode(['status' => 'error', 'message' => "File '$path' tidak ditemukan. Pastikan path sudah benar."]);
        exit();
    }
}

require_once $paths['koneksi'];
require_once $paths['functions'];
require_once $paths['config_midtrans'];
require_once $paths['autoload'];

// Validasi metode request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid.']);
    exit();
}

// Validasi input dasar
if (!isset($_POST['id_produk'], $_POST['jumlah'], $_POST['nama_pemesan'], $_POST['email'], $_POST['no_hp'], $_POST['alamat'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data dari form tidak lengkap.']);
    exit();
}

// Konfigurasi Midtrans
try {
    \Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
    \Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Konfigurasi Midtrans gagal: ' . $e->getMessage()]);
    exit();
}

// Sanitasi dan ambil data dari form
$id_produk = (int)$_POST['id_produk'];
$jumlah = (int)$_POST['jumlah'];
$nama_pemesan = bersihkan_input($_POST['nama_pemesan']);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : '';
$no_hp = bersihkan_input($_POST['no_hp']);
$alamat = bersihkan_input($_POST['alamat']);

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
    exit();
}

// Ambil data produk dari DB untuk validasi harga & stok
$query_produk = "SELECT nama_produk, harga, stok FROM produk WHERE id_produk = ?";
$stmt = mysqli_prepare($koneksi, $query_produk);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query produk.']);
    exit();
}
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$result_produk = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result_produk);
mysqli_stmt_close($stmt);

if (!$produk) {
    echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan di database.']);
    exit();
}
if ($jumlah > $produk['stok']) {
    echo json_encode(['status' => 'error', 'message' => 'Stok produk tidak mencukupi. Sisa stok: ' . $produk['stok']]);
    exit();
}

$total_harga = $produk['harga'] * $jumlah;

// 1. Simpan pesanan ke database dengan status 'pending'
$query_pesanan = "INSERT INTO pesanan (id_produk, nama_pemesan, email, no_hp, alamat, jumlah, total_harga, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
$stmt_pesanan = mysqli_prepare($koneksi, $query_pesanan);
if (!$stmt_pesanan) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query pesanan.']);
    exit();
}
mysqli_stmt_bind_param($stmt_pesanan, "issssii", $id_produk, $nama_pemesan, $email, $no_hp, $alamat, $jumlah, $total_harga);

if (!mysqli_stmt_execute($stmt_pesanan)) {
     echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesanan ke DB. Error: ' . mysqli_stmt_error($stmt_pesanan)]);
     exit();
}
$id_pesanan = mysqli_insert_id($koneksi);

// 2. Siapkan data transaksi untuk Midtrans
$transaction_details = [
    'order_id' => 'PESANAN-' . $id_pesanan . '-' . time(),
    'gross_amount' => $total_harga,
];

$item_details = [[
    'id'       => (string)$id_produk,
    'price'    => (int)$produk['harga'],
    'quantity' => (int)$jumlah,
    'name'     => $produk['nama_produk']
]];

$customer_details = [
    'first_name' => $nama_pemesan,
    'email'      => $email,
    'phone'      => $no_hp,
    'shipping_address' => ['address' => $alamat]
];

$transaction_data = [
    'transaction_details' => $transaction_details,
    'item_details'        => $item_details,
    'customer_details'    => $customer_details
];

// 3. Dapatkan Snap Token dari Midtrans
try {
    $snapToken = \Midtrans\Snap::getSnapToken($transaction_data);

    // 4. Update pesanan dengan snap_token
    $query_update = "UPDATE pesanan SET snap_token = ? WHERE id_pesanan = ?";
    $stmt_update = mysqli_prepare($koneksi, $query_update);
    mysqli_stmt_bind_param($stmt_update, "si", $snapToken, $id_pesanan);
    mysqli_stmt_execute($stmt_update);
    
    // 5. Kirim token ke client
    echo json_encode(['status' => 'success', 'snap_token' => $snapToken]);

} catch (Exception $e) {
    // Tangkap error dari Midtrans
    echo json_encode(['status' => 'error', 'message' => 'Midtrans Error: ' . $e->getMessage()]);
}
?>