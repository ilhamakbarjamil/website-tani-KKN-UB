<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// Periksa apakah parameter id_pesanan ada
if (!isset($_GET['id_pesanan'])) {
    header("Location: produk.php");
    exit();
}

$id_pesanan = bersihkan_input($_GET['id_pesanan']);

// Ambil data pesanan
$query = "SELECT p.*, pr.nama_produk, pr.harga 
          FROM pesanan p
          JOIN produk pr ON p.id_produk = pr.id_produk
          WHERE p.id_pesanan = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_pesanan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pesanan = mysqli_fetch_assoc($stmt);

if (!$pesanan) {
    header("Location: produk.php");
    exit();
}

// Jika status sudah success, redirect ke halaman sukses
if ($pesanan['status'] === 'success') {
    header("Location: sukses.php?id_pesanan=$id_pesanan");
    exit();
}

// Sertakan Midtrans PHP SDK
require_once 'midtrans-php-master/Midtrans.php';

// Set konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-Your_Server_Key'; // Ganti dengan server key Midtrans Anda
\Midtrans\Config::$isProduction = false; // Set true untuk environment production
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Buat parameter transaksi
$params = [
    'transaction_details' => [
        'order_id' => $id_pesanan,
        'gross_amount' => $pesanan['total_harga'],
    ],
    'customer_details' => [
        'first_name' => $pesanan['nama_pemesan'],
        'email' => $pesanan['email'],
        'phone' => $pesanan['no_hp'],
    ],
    'item_details' => [
        [
            'id' => $pesanan['id_produk'],
            'price' => $pesanan['harga'],
            'quantity' => $pesanan['jumlah'],
            'name' => $pesanan['nama_produk']
        ]
    ]
];

// Dapatkan Snap Token
$snapToken = '';
try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    
    // Simpan snap token ke database
    $updateQuery = "UPDATE pesanan SET snap_token = ? WHERE id_pesanan = ?";
    $stmt = mysqli_prepare($koneksi, $updateQuery);
    mysqli_stmt_bind_param($stmt, "si", $snapToken, $id_pesanan);
    mysqli_stmt_execute($stmt);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Petani Maju</title>
    <!-- Include CSS dan JS yang sama seperti index.php -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-Your_Client_Key"></script>
    <style>
        .pembayaran-section {
            padding: 60px 0;
        }
        
        .detail-pesanan {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 30px;
        }
        
        .btn-bayar {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            font-weight: bold;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            transition: all 0.3s;
            display: block;
            width: 100%;
            margin-top: 30px;
        }
        
        .btn-bayar:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(46, 125, 50, 0.4);
        }
    </style>
</head>
<body>
    <!-- Navbar (sama seperti index.php) -->

    <!-- Page Header -->
    <section class="page-header">
        <div class="container text-center">
            <h1>Pembayaran</h1>
        </div>
    </section>

    <!-- Pembayaran Section -->
    <section class="pembayaran-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="detail-pesanan">
                        <h3 class="mb-4">Detail Pesanan</h3>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Produk</h5>
                                <p><?= $pesanan['nama_produk'] ?></p>
                                
                                <h5>Harga Satuan</h5>
                                <p>Rp <?= number_format($pesanan['harga'], 0, ',', '.') ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Jumlah</h5>
                                <p><?= $pesanan['jumlah'] ?></p>
                                
                                <h5>Total Harga</h5>
                                <p class="fw-bold">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5>Informasi Pemesan</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>Nama:</strong> <?= $pesanan['nama_pemesan'] ?></p>
                                <p><strong>Email:</strong> <?= $pesanan['email'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>No. HP:</strong> <?= $pesanan['no_hp'] ?></p>
                                <p><strong>Alamat:</strong> <?= $pesanan['alamat'] ?></p>
                            </div>
                        </div>
                        
                        <button id="pay-button" class="btn-bayar">
                            <i class="fas fa-credit-card me-2"></i> Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('pay-button').onclick = function(){
            snap.pay('<?= $snapToken ?>', {
                onSuccess: function(result){
                    window.location.href = 'sukses.php?id_pesanan=<?= $id_pesanan ?>';
                },
                onPending: function(result){
                    alert("Pembayaran tertunda! Silakan selesaikan pembayaran.");
                    console.log(result);
                },
                onError: function(result){
                    alert("Pembayaran gagal! Silakan coba lagi.");
                    console.log(result);
                }
            });
        };
    </script>

    <!-- Footer (sama seperti index.php) -->
</body>
</html>