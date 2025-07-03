<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// MODIFIKASI: Menghapus data dummy dan mengambil data langsung dari database
$produk = ambil_semua_produk($koneksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Unggulan - Petani Maju</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
            --secondary: #ff9800;
            --light: #f8f9fa;
            --dark: #343a40;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        body { font-family: 'Open Sans', sans-serif; background-color: var(--light); }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--dark); }
        .navbar { background-color: white; box-shadow: var(--shadow); padding: 0.75rem 0; }
        .navbar .navbar-brand, .navbar .nav-link { color: var(--primary-dark) !important; }
        .navbar .nav-link:hover, .navbar .nav-link.active { color: var(--primary) !important; background-color: rgba(46, 125, 50, 0.1); }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; display: flex; align-items: center; }
        .navbar-brand i { margin-right: 10px; font-size: 1.8rem; color: var(--primary); }
        .nav-link { font-weight: 500; margin: 0 8px; padding: 8px 15px !important; border-radius: 50px; transition: var(--transition); }
        .page-header { background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1464226184884-fa280b87c399?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80') center center/cover no-repeat; padding: 8rem 0 4rem 0; color: white; text-align: center; }
        .page-header h1 { color: white; font-size: 3rem; font-weight: 700; }
        .page-header p { font-size: 1.1rem; opacity: 0.9; }
        .products-section { padding: 80px 0; }
        .product-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: var(--shadow); transition: var(--transition); height: 100%; display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1); }
        .product-img-wrapper { position: relative; height: 220px; }
        .product-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
        .product-card:hover .product-img { transform: scale(1.05); }
        .product-badge { position: absolute; top: 15px; left: 15px; color: white; padding: 5px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 500; }
        .product-card-body { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
        .product-title { font-size: 1.2rem; color: var(--primary-dark); margin-bottom: 10px; }
        .product-desc { font-size: 0.95rem; color: #6c757d; margin-bottom: 15px; flex-grow: 1; }
        .product-price { font-size: 1.3rem; font-weight: 700; color: var(--secondary); margin-bottom: 15px; }
        .product-card .btn { background-color: var(--primary); border-color: var(--primary); color: white; font-weight: 500; transition: var(--transition); }
        .product-card .btn:hover { background-color: var(--primary-dark); border-color: var(--primary-dark); transform: scale(1.05); }
        /* Footer styles */
        .footer { background: var(--primary-dark); color: white; padding: 70px 0 20px; }
        .footer-logo { font-size: 1.8rem; font-weight: 700; display: flex; align-items: center; margin-bottom: 20px; }
        .footer-logo i { margin-right: 10px; }
        .footer-title { font-size: 1.3rem; margin-bottom: 20px; position: relative; padding-bottom: 10px; }
        .footer-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 50px; height: 3px; background-color: var(--secondary); }
        .footer-links { list-style: none; padding-left: 0; } .footer-links li { margin-bottom: 10px; }
        .footer-links a { color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: var(--transition); display: flex; align-items: center; }
        .footer-links a:hover { color: white; transform: translateX(5px); }
        .copyright { text-align: center; padding-top: 30px; margin-top: 50px; border-top: 1px solid rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; }
    </style>
</head>
<body>

    <!-- Navbar (Tidak berubah) -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-tractor"></i>Petani Maju</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#layanan">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link active" href="produk.php">Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#berita">Berita</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#kontak">Kontak</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-lock me-1"></i>Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header (Tidak berubah) -->
    <header class="page-header">
        <div class="container">
            <h1>Produk Unggulan Kami</h1>
            <p>Menyediakan sarana produksi pertanian berkualitas untuk hasil panen maksimal.</p>
        </div>
    </header>

    <!-- Main Product Grid -->
    <section class="products-section">
        <div class="container">
            <?php if (empty($produk)): ?>
                <div class="alert alert-info text-center">
                    <h4>Belum Ada Produk</h4>
                    <p>Saat ini belum ada produk yang tersedia. Silakan cek kembali nanti.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($produk as $item): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 d-flex align-items-stretch">
                            <div class="product-card">
                                <div class="product-img-wrapper">
                                    
                                    <!-- MODIFIKASI: Menampilkan gambar dari folder 'uploads/produk/' -->
                                    <?php if (!empty($item['gambar']) && file_exists('uploads/produk/' . $item['gambar'])): ?>
                                        <img src="uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" class="product-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                                    <?php else: ?>
                                        <!-- Placeholder jika gambar tidak ada -->
                                        <div class="d-flex justify-content-center align-items-center bg-light h-100">
                                            <i class="fas fa-leaf fa-4x text-muted"></i>
                                        </div>
                                    <?php endif; ?>

                                    <!-- MODIFIKASI: Badge dinamis berdasarkan stok -->
                                    <div class="product-badge <?= ($item['stok'] > 0) ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ($item['stok'] > 0) ? 'Stok: ' . $item['stok'] : 'Stok Habis' ?>
                                    </div>
                                </div>
                                <div class="product-card-body">
                                    <!-- MODIFIKASI: Menampilkan nama produk dari database -->
                                    <h5 class="product-title"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                                    
                                    <!-- MODIFIKASI: Menampilkan deskripsi dari database -->
                                    <p class="product-desc"><?= htmlspecialchars($item['deskripsi']) ?></p>
                                    
                                    <!-- MODIFIKASI: Format harga dari integer ke Rupiah -->
                                    <div class="product-price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></div>
                                    
                                    <!-- MODIFIKASI: Logika tombol berdasarkan stok -->
                                    <?php if($item['stok'] > 0): ?>
    <a href="checkout.php?id_produk=<?= $item['id_produk'] ?>" class="btn btn-primary mt-auto w-100">
        <i class="fas fa-shopping-cart me-2"></i>Beli Sekarang
    </a>
<?php else: ?>
    <button class="btn btn-secondary mt-auto w-100" disabled>
        <i class="fas fa-times-circle me-2"></i>Stok Habis
    </button>
<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer (Tidak berubah) -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <div class="footer-logo"><i class="fas fa-tractor"></i>Petani Maju</div>
                    <p>Meningkatkan kesejahteraan petani melalui teknologi dan inovasi pertanian modern untuk Indonesia yang lebih maju.</p>
                </div>
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <h5 class="footer-title">Link Cepat</h5>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fas fa-chevron-right me-2"></i>Beranda</a></li>
                        <li><a href="index.php#layanan"><i class="fas fa-chevron-right me-2"></i>Layanan</a></li>
                        <li><a href="produk.php"><i class="fas fa-chevron-right me-2"></i>Produk</a></li>
                        <li><a href="index.php#berita"><i class="fas fa-chevron-right me-2"></i>Berita</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="footer-title">Kontak</h5>
                     <ul class="footer-links">
                        <li><a href="index.php#kontak"><i class="fas fa-map-marker-alt me-2"></i>Lokasi Kami</a></li>
                        <li><a href="mailto:info@petanimaju.id"><i class="fas fa-envelope me-2"></i>info@petanimaju.id</a></li>
                        <li><a href="https://api.whatsapp.com/send?phone=6281234567890"><i class="fab fa-whatsapp me-2"></i>WhatsApp</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>© <?= date('Y') ?> Petani Maju. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>