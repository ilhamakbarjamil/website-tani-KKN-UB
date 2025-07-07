<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// MODIFIKASI: Menghapus data dummy dan mengambil data langsung dari database
$produk = ambil_semua_produk($koneksi);

// Nomor WhatsApp untuk tombol pemesanan (ganti dengan nomor Anda)
$nomor_wa_pemesanan = '6281234567890'; // Contoh
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
    
    <!-- GANTI SEMUA CSS LAMA ANDA DENGAN INI -->
    <style>
        :root {
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
            --secondary: #ff9800; /* Warna oranye untuk harga, khas e-commerce */
            --light: #f1f3f5; /* Background sedikit abu-abu untuk kontras */
            --dark: #343a40;
            --border-color: #dee2e6;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            --transition: all 0.3s ease;
            --border-radius: 12px;
        }

        body { 
            font-family: 'Open Sans', sans-serif; 
            background-color: var(--light);
        }

        h1, h2, h3, h4, h5, h6 { 
            font-family: 'Poppins', sans-serif; 
            font-weight: 600; 
            color: var(--dark); 
        }

        a {
            text-decoration: none;
            color: var(--primary);
        }
        
        /* === Navbar (Tidak ada perubahan signifikan) === */
        .navbar { background-color: white; box-shadow: var(--shadow); padding: 0.75rem 0; }
        .navbar .navbar-brand, .navbar .nav-link { color: var(--primary-dark) !important; }
        .navbar .nav-link:hover, .navbar .nav-link.active { color: var(--primary) !important; }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; display: flex; align-items: center; }
        .navbar-brand i { margin-right: 10px; font-size: 1.8rem; color: var(--primary); }
        .nav-link { font-weight: 500; margin: 0 4px; padding: 8px 15px !important; transition: var(--transition); }

        /* === Header (Tidak ada perubahan signifikan) === */
        .page-header { 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('https://images.unsplash.com/photo-1464226184884-fa280b87c399?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80') center center/cover no-repeat; 
            padding: 5rem 0; color: white; text-align: center; 
        }
        .page-header h1 { color: white; font-size: 2.5rem; font-weight: 700; }
        .page-header p { font-size: 1.1rem; opacity: 0.9; }

        /* === PRODUCTS SECTION === */
        .products-section { padding: 50px 0; }

        /* === PRODUCT CARD BARU (Inspirasi Shopee) === */
        .product-card-link {
            display: block;
            color: inherit;
            text-decoration: none;
            transition: var(--transition);
        }

        .product-card-link:hover .product-card {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .product-card { 
            background: white; 
            border-radius: var(--border-radius); 
            overflow: hidden; 
            box-shadow: var(--shadow); 
            height: 100%; 
            display: flex; 
            flex-direction: column;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .product-img-wrapper {
            position: relative;
            /* Membuat gambar menjadi persegi */
            aspect-ratio: 1 / 1; 
            background-color: #f8f9fa;
        }

        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Pastikan gambar tidak gepeng */
        }
        
        .product-placeholder {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }

        .product-placeholder i {
            font-size: 3rem;
            color: #adb5bd;
        }

        .product-card-body { 
            padding: 12px; 
            display: flex; 
            flex-direction: column; 
            flex-grow: 1; 
        }

        .product-title { 
            font-size: 0.9rem; /* Ukuran font lebih kecil untuk mobile grid */
            color: var(--dark); 
            font-weight: 500;
            line-height: 1.4;
            margin-bottom: 8px;
            /* Batasi judul hanya 2 baris */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 2.8em; /* 2 baris x 1.4 line-height */
        }

        .product-info-bottom {
            margin-top: auto; /* Mendorong blok ini ke bawah */
        }

        .product-price { 
            font-size: 1.1rem; 
            font-weight: 700; 
            color: var(--secondary); /* Warna oranye yang mencolok */
            margin-bottom: 5px; 
        }

        .product-stock {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .product-stock .badge {
            font-size: 0.7rem;
            padding: 4px 8px;
        }

        /* === Empty State (Tidak ada perubahan) === */
        .empty-state { text-align: center; padding: 80px 20px; background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); }
        .empty-state i { font-size: 4rem; color: var(--primary); margin-bottom: 20px; }
        .empty-state h4 { color: var(--primary-dark); margin-bottom: 15px; }
        .empty-state p { color: #6c757d; font-size: 1.1rem; }

        /* === FOOTER === */
        .footer { background: var(--primary-dark); color: rgba(255, 255, 255, 0.8); padding: 60px 0 20px; }
        .footer-logo { font-size: 1.8rem; font-weight: 700; display: flex; align-items: center; margin-bottom: 20px; color: white; }
        .footer-logo i { margin-right: 10px; }
        .footer-title { font-size: 1.2rem; margin-bottom: 20px; position: relative; padding-bottom: 10px; color: white; }
        .footer-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 50px; height: 3px; background-color: var(--secondary); }
        .footer-links { list-style: none; padding-left: 0; } 
        .footer-links li { margin-bottom: 10px; }
        .footer-links a { color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: var(--transition); }
        .footer-links a:hover { color: white; padding-left: 5px; }
        .copyright { text-align: center; padding-top: 30px; margin-top: 40px; border-top: 1px solid rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.6); font-size: 0.9rem; }

        /* === RESPONSIVE FIXES === */
        @media (max-width: 767.98px) {
            .footer-widget {
                text-align: center;
                margin-bottom: 40px;
            }
            .footer-logo {
                justify-content: center;
            }
            .footer-title::after {
                left: 50%;
                transform: translateX(-50%);
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
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
                    <p>Saat ini belum ada produk yang tersedia. Silakan cek kembali nanti.</p>
                </div>
            <?php else: ?>
                <!-- PERUBAHAN GRID: col-6 untuk mobile (2 kolom) -->
                <div class="row g-3 g-md-4">
                    <?php foreach($produk as $item): ?>
                        <!-- PERUBAHAN GRID: col-6 col-md-4 col-lg-3 -->
                        <div class="col-6 col-md-4 col-lg-3">
                            <!-- PERUBAHAN STRUKTUR: Seluruh kartu dibungkus link <a> -->
                            <a href="https://wa.me/<?= $nomor_wa_pemesanan ?>?text=Halo%2C%20saya%20tertarik%20dengan%20produk%20'<?= urlencode($item['nama_produk']) ?>'" 
                               target="_blank" 
                               class="product-card-link">
                                <div class="product-card">
                                    <div class="product-img-wrapper">
                                        <?php if (!empty($item['gambar']) && file_exists('uploads/produk/' . $item['gambar'])): ?>
                                            <img src="uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" class="product-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>" loading="lazy">
                                        <?php else: ?>
                                            <div class="product-placeholder"><i class="fas fa-leaf"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="product-card-body">
                                        <h5 class="product-title"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                                        
                                        <!-- Bagian harga & stok dipindah ke bawah agar rapi -->
                                        <div class="product-info-bottom">
                                            <div class="product-price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></div>
                                            <div class="product-stock">
                                                <?php if($item['stok'] > 0): ?>
                                                    <span class="badge bg-success-subtle text-success-emphasis">Pesan via WhatsApp</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-subtle text-danger-emphasis">Stok Habis</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <!-- PERUBAHAN FOOTER: Menambahkan class .footer-widget untuk targetting di mobile -->
            <div class="row">
                <div class="col-lg-4 footer-widget">
                    <div class="footer-logo">
                        <i class="fas fa-tractor"></i>Petani Maju
                    </div>
                    <p>Meningkatkan kesejahteraan petani melalui teknologi dan inovasi pertanian modern untuk Indonesia yang lebih maju.</p>
                </div>
                <div class="col-lg-4 footer-widget">
                    <h5 class="footer-title">Link Cepat</h5>
                    <ul class="footer-links">
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="index.php#layanan">Layanan</a></li>
                        <li><a href="produk.php">Produk</a></li>
                        <li><a href="index.php#berita">Berita</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 footer-widget">
                    <h5 class="footer-title">Kontak</h5>
                     <ul class="footer-links">
                        <li><a href="index.php#kontak"><i class="fas fa-map-marker-alt me-2"></i>Lokasi Kami</a></li>
                        <li><a href="mailto:info@petanimaju.id"><i class="fas fa-envelope me-2"></i>info@petanimaju.id</a></li>
                        <li><a href="https://api.whatsapp.com/send?phone=<?= $nomor_wa_pemesanan ?>"><i class="fab fa-whatsapp me-2"></i>WhatsApp</a></li>
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