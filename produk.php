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
            --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
            --border-radius: 15px;
        }

        * {
            box-sizing: border-box;
        }

        body { 
            font-family: 'Open Sans', sans-serif; 
            background-color: var(--light);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 { 
            font-family: 'Poppins', sans-serif; 
            font-weight: 600; 
            color: var(--dark); 
        }

        /* === NAVBAR RESPONSIF === */
        .navbar { 
            background-color: white; 
            box-shadow: var(--shadow); 
            padding: 0.75rem 0; 
        }

        .navbar .navbar-brand, .navbar .nav-link { 
            color: var(--primary-dark) !important; 
        }

        .navbar .nav-link:hover, .navbar .nav-link.active { 
            color: var(--primary) !important; 
            background-color: rgba(46, 125, 50, 0.1);
            border-radius: 25px;
        }

        .navbar-brand { 
            font-weight: 700; 
            font-size: 1.5rem; 
            display: flex; 
            align-items: center; 
        }

        .navbar-brand i { 
            margin-right: 10px; 
            font-size: 1.8rem; 
            color: var(--primary); 
        }

        .nav-link { 
            font-weight: 500; 
            margin: 0 4px; 
            padding: 8px 15px !important; 
            border-radius: 25px; 
            transition: var(--transition); 
        }

        /* === HEADER RESPONSIF === */
        .page-header { 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('https://images.unsplash.com/photo-1464226184884-fa280b87c399?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80') center center/cover no-repeat; 
            padding: 6rem 0 4rem 0; 
            color: white; 
            text-align: center; 
        }

        .page-header h1 { 
            color: white; 
            font-size: 2.5rem; 
            font-weight: 700; 
            margin-bottom: 1rem;
        }

        .page-header p { 
            font-size: 1.1rem; 
            opacity: 0.9; 
        }

        /* === PRODUCTS SECTION === */
        .products-section { 
            padding: 60px 0; 
        }

        /* === PRODUCT CARD BARU === */
        .product-card { 
            background: white; 
            border-radius: var(--border-radius); 
            overflow: hidden; 
            box-shadow: var(--shadow); 
            transition: var(--transition); 
            height: 100%; 
            display: flex; 
            flex-direction: column;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .product-card:hover { 
            transform: translateY(-8px); 
            box-shadow: var(--shadow-hover); 
        }

        .product-img-wrapper {
            position: relative;
            height: 200px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            overflow: hidden;
        }

        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .product-card:hover .product-img { 
            transform: scale(1.1); 
        }

        .product-badge { 
            position: absolute; 
            top: 12px; 
            right: 12px; 
            color: white; 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase;
            backdrop-filter: blur(10px);
        }

        .product-card-body { 
            padding: 20px; 
            display: flex; 
            flex-direction: column; 
            flex-grow: 1; 
        }

        .product-title { 
            font-size: 1.1rem; 
            color: var(--primary-dark); 
            margin-bottom: 8px; 
            font-weight: 600;
            line-height: 1.4;
        }

        .product-desc {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 15px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price { 
            font-size: 1.25rem; 
            font-weight: 700; 
            color: var(--secondary); 
            margin-bottom: 15px; 
        }

        .product-card .btn { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            color: white; 
            font-weight: 500; 
            transition: var(--transition);
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 0.9rem;
        }

        .product-card .btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.4);
        }

        .product-card .btn:disabled {
            background: #6c757d;
            transform: none;
            box-shadow: none;
        }

        /* === PLACEHOLDER UNTUK PRODUK TANPA GAMBAR === */
        .product-placeholder {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        }

        .product-placeholder i {
            font-size: 3rem;
            color: #adb5bd;
        }

        /* === EMPTY STATE === */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: var(--primary-dark);
            margin-bottom: 15px;
        }

        .empty-state p {
            color: #6c757d;
            font-size: 1.1rem;
        }

        /* === FOOTER === */
        .footer { 
            background: var(--primary-dark); 
            color: white; 
            padding: 70px 0 20px; 
        }

        .footer-logo { 
            font-size: 1.8rem; 
            font-weight: 700; 
            display: flex; 
            align-items: center; 
            margin-bottom: 20px; 
        }

        .footer-logo i { 
            margin-right: 10px; 
        }

        .footer-title { 
            font-size: 1.3rem; 
            margin-bottom: 20px; 
            position: relative; 
            padding-bottom: 10px; 
        }

        .footer-title::after { 
            content: ''; 
            position: absolute; 
            bottom: 0; 
            left: 0; 
            width: 50px; 
            height: 3px; 
            background-color: var(--secondary); 
        }

        .footer-links { 
            list-style: none; 
            padding-left: 0; 
        } 

        .footer-links li { 
            margin-bottom: 10px; 
        }

        .footer-links a { 
            color: rgba(255, 255, 255, 0.8); 
            text-decoration: none; 
            transition: var(--transition); 
            display: flex; 
            align-items: center; 
        }

        .footer-links a:hover { 
            color: white; 
            transform: translateX(5px); 
        }

        .copyright { 
            text-align: center; 
            padding-top: 30px; 
            margin-top: 50px; 
            border-top: 1px solid rgba(255, 255, 255, 0.1); 
            color: rgba(255, 255, 255, 0.7); 
            font-size: 0.9rem; 
        }

        /* === RESPONSIVE BREAKPOINTS === */
        @media (max-width: 768px) {
            .page-header {
                padding: 4rem 0 3rem 0;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .page-header p {
                font-size: 1rem;
            }

            .products-section {
                padding: 40px 0;
            }

            .product-card-body {
                padding: 15px;
            }

            .product-title {
                font-size: 1rem;
            }

            .product-desc {
                font-size: 0.85rem;
            }

            .product-price {
                font-size: 1.1rem;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .navbar-brand i {
                font-size: 1.5rem;
            }

            .nav-link {
                margin: 2px 0;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .page-header h1 {
                font-size: 1.8rem;
            }

            .product-img-wrapper {
                height: 180px;
            }

            .product-card-body {
                padding: 12px;
            }

            .product-title {
                font-size: 0.95rem;
            }

            .product-desc {
                font-size: 0.8rem;
                -webkit-line-clamp: 2;
            }

            .product-price {
                font-size: 1rem;
            }

            .product-card .btn {
                font-size: 0.8rem;
                padding: 8px 16px;
            }
        }

        /* === LOADING ANIMATION === */
        .loading-placeholder {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-tractor"></i>Petani Maju
            </a>
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

    <!-- Page Header -->
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
                <div class="empty-state">
                    <i class="fas fa-seedling"></i>
                    <h4>Belum Ada Produk</h4>
                    <p>Saat ini belum ada produk yang tersedia. Silakan cek kembali nanti atau hubungi kami untuk informasi lebih lanjut.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach($produk as $item): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                            <div class="product-card">
                                <div class="product-img-wrapper">
                                    <?php if (!empty($item['gambar']) && file_exists('uploads/produk/' . $item['gambar'])): ?>
                                        <img src="uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" 
                                             class="product-img" 
                                             alt="<?= htmlspecialchars($item['nama_produk']) ?>"
                                             loading="lazy">
                                    <?php else: ?>
                                        <div class="product-placeholder">
                                            <i class="fas fa-leaf"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="product-badge <?= ($item['stok'] > 0) ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ($item['stok'] > 0) ? 'Stok: ' . $item['stok'] : 'Habis' ?>
                                    </div>
                                </div>
                                
                                <div class="product-card-body">
                                    <h5 class="product-title"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                                    <p class="product-desc"><?= htmlspecialchars($item['deskripsi']) ?></p>
                                    <div class="product-price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></div>
                                    
                                    <?php if($item['stok'] > 0): ?>
                                        <a href="https://wa.me/6281234567890?text=Halo%20saya%20ingin%20memesan%20produk%20<?= urlencode($item['nama_produk']) ?>%20dengan%20ID%20<?= $item['id_produk'] ?>" 
                                           target="_blank" 
                                           class="btn btn-primary mt-auto w-100">
                                            <i class="fab fa-whatsapp me-2"></i>Pesan Sekarang
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

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <div class="footer-logo">
                        <i class="fas fa-tractor"></i>Petani Maju
                    </div>
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