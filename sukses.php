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
$query = "SELECT p.*, pr.nama_produk 
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

// Jika status masih pending, update menjadi success
if ($pesanan['status'] === 'pending') {
    $updateQuery = "UPDATE pesanan SET status = 'success' WHERE id_pesanan = ?";
    $stmt = mysqli_prepare($koneksi, $updateQuery);
    mysqli_stmt_bind_param($stmt, "i", $id_pesanan);
    mysqli_stmt_execute($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Sukses - Petani Maju</title>
    <!-- Include CSS dan JS yang sama seperti index.php -->
    <style>
        .sukses-section {
            padding: 80px 0;
            text-align: center;
        }
        
        .icon-sukses {
            width: 100px;
            height: 100px;
            background-color: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        
        .icon-sukses i {
            color: white;
            font-size: 50px;
        }
        
        .detail-invoice {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
            text-align: left;
        }
        
        .invoice-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <!-- Navbar (sama seperti index.php) -->

    <!-- Page Header -->
    <section class="page-header">
        <div class="container text-center">
            <h1>Pembayaran Sukses</h1>
        </div>
    </section>

    <!-- Sukses Section -->
    <section class="sukses-section">
        <div class="container">
            <div class="icon-sukses">
                <i class="fas fa-check"></i>
            </div>
            
            <h2 class="mb-3">Terima Kasih!</h2>
            <p class="lead mb-5">Pembayaran Anda telah berhasil diproses.</p>
            
            <div class="detail-invoice mb-5">
                <h4 class="mb-4">Detail Pesanan</h4>
                
                <div class="invoice-item">
                    <span>ID Pesanan</span>
                    <span><?= $pesanan['id_pesanan'] ?></span>
                </div>
                <div class="invoice-item">
                    <span>Produk</span>
                    <span><?= $pesanan['nama_produk'] ?></span>
                </div>
                <div class="invoice-item">
                    <span>Jumlah</span>
                    <span><?= $pesanan['jumlah'] ?></span>
                </div>
                <div class="invoice-item">
                    <span>Total Harga</span>
                    <span class="fw-bold">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></span>
                </div>
                <div class="invoice-item">
                    <span>Nama Pemesan</span>
                    <span><?= $pesanan['nama_pemesan'] ?></span>
                </div>
                <div class="invoice-item">
                    <span>Alamat</span>
                    <span><?= nl2br($pesanan['alamat']) ?></span>
                </div>
            </div>
            
            <a href="produk.php" class="btn btn-success btn-lg">
                <i class="fas fa-shopping-cart me-2"></i> Belanja Lagi
            </a>
        </div>
    </section>

    <!-- Footer (sama seperti index.php) -->
</body>
</html>