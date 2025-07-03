<?php
// Pastikan tidak ada spasi atau karakter sebelum tag <?php ini

// Aktifkan error reporting untuk debugging selama pengembangan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set header ke JSON di awal untuk memastikan output yang bersih
header('Content-Type: application/json');

// --- PANGGIL SEMUA FILE YANG DIBUTUHKAN ---
// Validasi path dan panggil file jika ada
$paths = [
    'koneksi' => __DIR__ . '/includes/koneksi.php',
    'functions' => __DIR__ . '/includes/functions.php',
    'config_midtrans' => __DIR__ . '/includes/config_midtrans.php',
    'autoload' => __DIR__ . '/vendor/autoload.php'
];

foreach ($paths as $key => $path) {
    if (!file_exists($path)) {
        echo json_encode(['status' => 'error', 'message' => "File konfigurasi '$key' tidak ditemukan di '$path'. Pastikan path sudah benar."]);
        exit();
    }
    require_once $path; // Panggil file di sini
}

// --- VALIDASI REQUEST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid.']);
    exit();
}

if (!isset($_POST['id_produk'], $_POST['jumlah'], $_POST['nama_pemesan'], $_POST['email'], $_POST['no_hp'], $_POST['alamat'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data dari form tidak lengkap.']);
    exit();
}


// --- KONFIGURASI MIDTRANS ---
try {
    \Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
    \Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Konfigurasi Midtrans gagal: ' . $e->getMessage()]);
    exit();
}

// --- SANITASI & AMBIL DATA DARI FORM ---
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

// --- AMBIL DATA PRODUK DARI DB & VALIDASI ---
$query_produk = "SELECT nama_produk, harga, stok FROM produk WHERE id_produk = ?";
$stmt = mysqli_prepare($koneksi, $query_produk);
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$produk = mysqli_stmt_get_result($stmt)->fetch_assoc();
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

// --- SIMPAN PESANAN KE DATABASE ---
$query_pesanan = "INSERT INTO pesanan (id_produk, nama_pemesan, email, no_hp, alamat, jumlah, total_harga, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
$stmt_pesanan = mysqli_prepare($koneksi, $query_pesanan);
mysqli_stmt_bind_param($stmt_pesanan, "issssii", $id_produk, $nama_pemesan, $email, $no_hp, $alamat, $jumlah, $total_harga);

if (!mysqli_stmt_execute($stmt_pesanan)) {
     echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesanan ke DB. Error: ' . mysqli_stmt_error($stmt_pesanan)]);
     exit();
}
$id_pesanan = mysqli_insert_id($koneksi);

// --- SIAPKAN DATA TRANSAKSI UNTUK MIDTRANS ---
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

// --- DAPATKAN SNAP TOKEN & KIRIM KE CLIENT ---
try {
    $snapToken = \Midtrans\Snap::getSnapToken($transaction_data);
    $query_update = "UPDATE pesanan SET snap_token = ? WHERE id_pesanan = ?";
    $stmt_update = mysqli_prepare($koneksi, $query_update);
    mysqli_stmt_bind_param($stmt_update, "si", $snapToken, $id_pesanan);
    mysqli_stmt_execute($stmt_update);
    
    echo json_encode(['status' => 'success', 'snap_token' => $snapToken]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Midtrans Error: ' . $e->getMessage()]);
}

?>