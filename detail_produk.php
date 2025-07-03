<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// Periksa apakah parameter id ada
if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit();
}

$id_produk = bersihkan_input($_GET['id']);

// Ambil produk berdasarkan ID
$query = "SELECT * FROM produk WHERE id_produk = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($stmt);

if (!$produk) {
    header("Location: produk.php");
    exit();
}

// Proses form pemesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = bersihkan_input($_POST['nama']);
    $email = bersihkan_input($_POST['email']);
    $no_hp = bersihkan_input($_POST['no_hp']);
    $alamat = bersihkan_input($_POST['alamat']);
    $jumlah = bersihkan_input($_POST['jumlah']);
    $total_harga = $jumlah * $produk['harga'];
    
    // Simpan data pesanan ke database dengan status pending
    $query = "INSERT INTO pesanan (id_produk, nama_pemesan, email, no_hp, alamat, jumlah, total_harga, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "issssii", $id_produk, $nama, $email, $no_hp, $alamat, $jumlah, $total_harga);
    mysqli_stmt_execute($stmt);
    $id_pesanan = mysqli_insert_id($koneksi);
    
    // Redirect ke halaman pembayaran
    header("Location: pembayaran.php?id_pesanan=$id_pesanan");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $produk['nama_produk'] ?> - Petani Maju</title>
    <!-- Include CSS dan JS yang sama seperti index.php -->
    <style>
        .produk-detail-section {
            padding: 60px 0;
        }
        
        .produk-detail-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
        }
        
        .harga-detail {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .stok-detail {
            font-size: 1.1rem;
        }
        
        .form-pemesanan {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 25px;
        }
    </style>
</head>
<body>
    <!-- Navbar (sama seperti index.php) -->

    <!-- Page Header -->
    <section class="page-header">
        <div class="container text-center">
            <h1>Detail Produk</h1>
        </div>
    </section>

    <!-- Detail Produk -->
    <section class="produk-detail-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <?php if($produk['gambar']): ?>
                        <img src="uploads/produk/<?= $produk['gambar'] ?>" class="produk-detail-img" alt="<?= $produk['nama_produk'] ?>">
                    <?php else: ?>
                        <div class="produk-detail-img bg-secondary d-flex align-items-center justify-content-center">
                            <i class="fas fa-image fa-5x text-white"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h2><?= $produk['nama_produk'] ?></h2>
                    <div class="d-flex align-items-center mb-3">
                        <span class="harga-detail">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></span>
                        <span class="stok-detail ms-3">Stok: <?= $produk['stok'] ?></span>
                    </div>
                    <div class="mb-4">
                        <p><?= nl2br($produk['deskripsi']) ?></p>
                    </div>
                    
                    <!-- Form Pemesanan -->
                    <div class="form-pemesanan">
                        <h4 class="mb-4">Form Pemesanan</h4>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No. HP</label>
                                <input type="tel" class="form-control" id="no_hp" name="no_hp" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" max="<?= $produk['stok'] ?>" value="1" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-3 fw-bold">
                                <i class="fas fa-shopping-cart me-2"></i> Lanjutkan Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer (sama seperti index.php) -->
</body>
</html>