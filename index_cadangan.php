<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// Ambil data berita dan produk
$query_berita = "SELECT * FROM berita ORDER BY tanggal DESC LIMIT 3";
$result_berita = mysqli_query($koneksi, $query_berita);
$berita = [];
if ($result_berita) {
    while ($row = mysqli_fetch_assoc($result_berita)) {
        $berita[] = $row;
    }
}
$query_produk = "SELECT * FROM produk WHERE stok > 0 ORDER BY created_at DESC LIMIT 4";
$result_produk = mysqli_query($koneksi, $query_produk);
$produk_unggulan = [];
if ($result_produk) {
    while ($row = mysqli_fetch_assoc($result_produk)) {
        $produk_unggulan[] = $row;
    }
}
$nomor_wa = "6282229820607";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Kelompok Wanita Tani Wonomulyo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet" />
<style>
    :root {
        --primary: #2e7d32;
        --primary-dark: #1b5e20;
        --secondary: #EE4D2D; /* Vibrant Orange for accents */
        --light: #f7f9f7; /* Lighter background */
        --white: #ffffff;
        --dark: #343a40;
        --text: #454545;
        --text-light: #6c757d;
        --border-color: #e9ecef;
        --shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease-in-out;
        --border-radius: 8px;
    }
    html {
        scroll-behavior: smooth;
        overflow-x: hidden;
        max-width: 100%;
    }
    body {
        font-family: 'Open Sans', sans-serif;
        color: var(--text);
        background-color: var(--white);
        line-height: 1.7;
    }
    h1, h2, h3, h4, h5, h6 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: var(--dark);
    }
    .container { overflow-x: hidden; }

    .pre-title {
        display: block;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--primary);
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .section-title {
        position: relative;
        margin-bottom: 50px;
        text-align: center;
        font-weight: 700;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: var(--secondary);
        border-radius: 2px;
    }

    .navbar {
        background-color: transparent;
        padding: 1rem 0;
        transition: var(--transition);
    }
    .navbar.scrolled {
        background-color: rgba(255, 255, 255, 0.97);
        box-shadow: var(--shadow);
        padding: 0.75rem 0;
        backdrop-filter: blur(8px);
    }
    .navbar.scrolled .navbar-brand,
    .navbar.scrolled .nav-link {
        color: var(--primary-dark) !important;
    }
    .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--white);
        transition: var(--transition);
    }
    .navbar-brand i { margin-right: 10px; color: #ffc107; }
    .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500;
        margin: 0 5px;
        padding: 8px 15px !important;
        border-radius: var(--border-radius);
        transition: var(--transition);
    }
    .nav-link:hover, .nav-link.active {
        color: var(--white) !important;
        background-color: rgba(255, 255, 255, 0.1);
    }
    .navbar.scrolled .nav-link:hover,
    .navbar.scrolled .nav-link.active {
        color: var(--primary) !important;
        background-color: rgba(46, 125, 50, 0.1);
    }

    .hero-section {
        background: linear-gradient(rgba(27, 94, 32, 0.8), rgba(46, 125, 50, 0.7)),
                    url('https://images.unsplash.com/photo-1492496913980-501348b61469?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') center center/cover no-repeat fixed;
        min-height: 90vh;
        display: flex;
        align-items: center;
        color: var(--white);
        padding-top: 80px;
    }
    .hero-content { z-index: 1; max-width: 800px; }
    .hero-title { font-size: 3.2rem; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.3); color: var(--white); }
    .hero-subtitle { font-size: 1.25rem; opacity: 0.95; margin-bottom: 30px; }
    .btn-whatsapp {
        background-color: #25D366;
        color: white;
        font-weight: 600;
        padding: 12px 30px;
        border-radius: 50px;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
        border: none;
        font-size: 1.1rem;
    }
    .btn-whatsapp:hover {
        background-color: #128C7E;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
    }

    .section {
        padding: 80px 0;
    }
    .bg-light-green { background-color: var(--light); }
    
    .card-product, .card-news {
        background: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        transition: var(--transition);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .card-product:hover, .card-news:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }
    .card-img-wrapper {
        height: 220px;
        overflow: hidden;
    }
    .card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .card-product:hover .card-img, .card-news:hover .card-img {
        transform: scale(1.05);
    }
    .card-body {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 10px;
        line-height: 1.4;
    }
    .card-text {
        font-size: 0.9rem;
        color: var(--text-light);
        margin-bottom: 15px;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .btn-primary-outline {
        border: 2px solid var(--primary);
        color: var(--primary);
        background-color: transparent;
        font-weight: 600;
        transition: var(--transition);
    }
    .btn-primary-outline:hover {
        background-color: var(--primary);
        color: var(--white);
    }

    .product-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--secondary);
        margin-bottom: 15px;
    }
    .card-product .card-body {
        padding-bottom: 0;
    }
    .card-product .btn-pesan {
        background-color: var(--primary);
        color: var(--white);
        border: 0;
        border-radius: 0 0 var(--border-radius) var(--border-radius);
        padding: 12px;
        margin: 15px -20px 0 -20px;
        font-weight: 600;
        transition: var(--transition);
    }
    .card-product .btn-pesan:hover {
        background-color: var(--primary-dark);
    }

    .history-image {
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-hover);
    }
    .history-content h3 {
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 15px;
    }
    .history-content p {
        font-size: 1rem;
        line-height: 1.8;
        color: var(--text-light);
    }
    
    /* === [BARU] Section Kontak & Peta === */
    #kontak {
        background-color: var(--light);
    }
    .contact-info-wrapper {
        background: var(--white);
        padding: 40px;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .contact-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 30px;
    }
    .contact-item:last-child {
        margin-bottom: 0;
    }
    .contact-icon {
        flex-shrink: 0;
        width: 55px;
        height: 55px;
        background: rgba(46, 125, 50, 0.1);
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        margin-right: 20px;
        transition: var(--transition);
    }
    .contact-item:hover .contact-icon {
        background: var(--primary);
        color: var(--white);
        transform: scale(1.1);
    }
    .contact-text h4 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 5px;
        color: var(--dark);
    }
    .contact-text p {
        margin: 0;
        font-size: 1rem;
        color: var(--text-light);
        line-height: 1.6;
    }
    .contact-text p a {
        color: var(--text-light);
        text-decoration: none;
        transition: var(--transition);
        font-weight: 500;
    }
    .contact-text p a:hover {
        color: var(--primary);
    }

    .map-container {
        overflow: hidden;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        height: 100%;
        min-height: 400px;
    }
    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    
    /* === Footer (Desktop) === */
    .footer {
        background-color: var(--primary-dark);
        color: rgba(255, 255, 255, 0.7);
        padding: 60px 0 0;
    }
    .footer-title {
        color: var(--white);
        margin-bottom: 25px;
        font-size: 1.2rem;
        font-weight: 600;
        position: relative;
        padding-bottom: 10px;
    }
    .footer-title::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0;
        width: 35px; height: 3px;
        background: var(--secondary);
    }
    .footer-links a {
        color: rgba(255, 255, 255, 0.7);
        transition: var(--transition);
        text-decoration: none;
        font-size: 0.95rem;
    }
    .footer-links a:hover {
        color: var(--white);
        padding-left: 5px;
    }
    .footer-bottom {
        background-color: rgba(0, 0, 0, 0.2);
        padding: 20px 0;
        margin-top: 40px;
    }
    .footer-bottom p {
        margin: 0;
        font-size: 0.9rem;
    }

    /* ======= RESPONSIVE CSS ======= */
    @media (max-width: 991.98px) {
        .hero-title { font-size: 2.5rem; }
        .hero-subtitle { font-size: 1.1rem; }
    }

    @media (max-width: 767.98px) {
        /* ==== MOBILE OPTIMIZATION: SMALLER & MORE COMPACT ==== */
        body { font-size: 14px; line-height: 1.6; }
        .section { padding: 40px 0; }

        .navbar-brand { font-size: 1.1rem; }
        .nav-link { font-size: 0.9rem; padding: 6px 12px !important; }

        .hero-section { min-height: 60vh; background-attachment: scroll; }
        .hero-content { text-align: center; }
        .hero-title { font-size: 1.6rem; }
        .hero-subtitle { font-size: 0.9rem; margin-bottom: 20px; }
        .btn-whatsapp { width: 90%; font-size: 0.95rem; padding: 10px 0; }
        
        .pre-title { font-size: 0.8rem; }
        .section-title { font-size: 1.3rem; margin-bottom: 40px;}
        .section-title::after { width: 50px; height: 3px; bottom: -10px;}

        .history-image { margin-bottom: 20px; }
        .history-content h3 { font-size: 1.2rem; }
        .history-content p { font-size: 0.9rem; }

        /* Horizontal Scroll */
        .products-scroll-wrapper, .news-scroll-wrapper { position: relative; }
        .products-scroll-wrapper::after, .news-scroll-wrapper::after {
            content: ''; position: absolute; top: 0; right: 0; width: 40px; height: 100%;
            background: linear-gradient(to left, var(--light), transparent);
            pointer-events: none; z-index: 2;
        }
        #berita .news-scroll-wrapper::after { background: linear-gradient(to left, var(--white), transparent); }
        .row-scroll {
            display: flex; flex-wrap: nowrap; overflow-x: auto;
            -webkit-overflow-scrolling: touch; margin: 0 -15px; padding: 4px 15px 15px 15px;
            scrollbar-width: none; -ms-overflow-style: none;
        }
        .row-scroll::-webkit-scrollbar { display: none; }
        .col-scroll-item {
            padding: 0 6px; margin-bottom: 0 !important;
        }
        .col-product-item { flex: 0 0 48%; max-width: 48%; }
        .col-scroll-item.col-md-4 { flex: 0 0 65%; max-width: 65%; }

        .card-body { padding: 12px; }
        .card-title { font-size: 0.95rem; }
        .card-text { font-size: 0.8rem; -webkit-line-clamp: 2; }
        .btn-primary-outline { font-size: 0.8rem; padding: 5px 10px; }

        .card-product .card-img-wrapper { height: 120px; }
        .card-product .card-body { padding: 8px; }
        .card-product .card-title {
            font-size: 0.8rem; height: 2.4em; /* 2 baris */
            line-height: 1.2; overflow: hidden;
            margin-bottom: 5px;
        }
        .product-price { font-size: 1rem; margin-bottom: 8px; font-weight: 600; }
        .card-product .btn-pesan {
             margin: 8px -8px 0 -8px; padding: 8px;
             font-size: 0.85rem;
        }

        /* [BARU] Mobile styles untuk contact section */
        .contact-info-wrapper { padding: 25px 20px; }
        .contact-icon { width: 45px; height: 45px; font-size: 1.1rem; margin-right: 15px; }
        .contact-text h4 { font-size: 1rem; }
        .contact-text p { font-size: 0.9rem; }
        .map-container { min-height: 300px; }

        /* [DIUBAH] Footer (Mobile Optimization) - Tidak lagi disembunyikan */
        .footer {
            padding: 40px 0 0; /* Memberi padding atas di mobile */
            text-align: center; /* Meratakan semua teks di footer ke tengah */
        }
        .footer-main-content .col-lg-5,
        .footer-main-content .col-lg-3,
        .footer-main-content .col-lg-4 {
            margin-bottom: 30px; /* Jarak antar widget saat bertumpuk */
        }
        .footer-title::after {
            left: 50%; /* Posisikan garis bawah di tengah */
            transform: translateX(-50%);
        }
        .footer-bottom {
            margin-top: 10px; /* Kurangi jarak dari widget terakhir */
        }
        .footer-bottom p { font-size: 0.8rem; }
    }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-leaf"></i> KWT Wonomulyo
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="#">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#sejarah">Tentang</a></li>
                <li class="nav-item"><a class="nav-link" href="#produk">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="#berita">Berita</a></li>
                <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-lock me-1"></i>Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section text-center text-lg-start">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">KWT Wonomulyo</h1>
            <p class="hero-subtitle">Menumbuhkan produk lokal berkualitas, memperkuat ekonomi keluarga, dan melestarikan lingkungan.</p>
            <a href="https://api.whatsapp.com/send?phone=<?= htmlspecialchars($nomor_wa) ?>&text=Halo%20KWT%20Wonomulyo,%20saya%20tertarik%20untuk%20mengetahui%20lebih%20lanjut." target="_blank" class="btn btn-whatsapp d-inline-flex align-items-center justify-content-center">
                <i class="fab fa-whatsapp me-2"></i>Hubungi Kami
            </a>
        </div>
    </div>
</section>

<section id="sejarah" class="section">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <span class="pre-title">AWAL MULA KAMI</span>
                <h2 class="section-title">Dari Pelatihan Hingga Kemandirian</h2>
            </div>
        </div>
        <div class="row align-items-center g-5">
            <div class="col-lg-5 text-center">
                <img src="uploads/logo.png" class="img-fluid history-image" alt="Logo KWT Wonomulyo" />
            </div>
            <div class="col-lg-7">
                <div class="history-content">
                    <h3>Terbentuk pada 4 Januari 2023</h3>
                    <p>
                        Kami memilih untuk fokus mengolah singkong yang melimpah di desa kami sebagai cara mengangkat potensi lokal dan memberdayakan anggota.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="produk" class="section bg-light-green">
    <div class="container">
        <div class="text-center mb-5">
            <span class="pre-title">PILIHAN TERBAIK</span>
            <h2 class="section-title">Produk Unggulan Kami</h2>
        </div>
        <?php if (empty($produk_unggulan)): ?>
            <div class="alert alert-light text-center">Belum ada produk unggulan.</div>
        <?php else: ?>
            <div class="products-scroll-wrapper">
                <div class="row row-scroll">
                    <?php foreach($produk_unggulan as $item): ?>
                        <div class="col-lg-3 col-md-6 mb-4 col-scroll-item col-product-item">
                            <div class="card-product">
                                <a href="https://api.whatsapp.com/send?phone=<?= htmlspecialchars($nomor_wa) ?>&text=Halo%2C%20saya%20tertarik%20memesan%20*<?= urlencode($item['nama_produk']) ?>*." target="_blank" class="text-decoration-none">
                                    <div class="card-img-wrapper">
                                        <?php if (!empty($item['gambar']) && file_exists('uploads/produk/' . $item['gambar'])): ?>
                                            <img src="uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" class="card-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                                        <?php else: ?>
                                            <img src="https://via.placeholder.com/400x300.png/E8F5E9/1B5E20?text=Produk" class="card-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title text-dark"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                                    <div class="mt-auto">
                                        <div class="product-price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></div>
                                    </div>
                                </div>
                                <a href="https://api.whatsapp.com/send?phone=<?= htmlspecialchars($nomor_wa) ?>&text=Halo%2C%20saya%20tertarik%20memesan%20*<?= urlencode($item['nama_produk']) ?>*." target="_blank" class="btn btn-pesan d-flex align-items-center justify-content-center">
                                    <i class="fab fa-whatsapp me-2"></i>Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="produk.php" class="btn btn-primary-outline py-2 px-4">
                    Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="berita" class="section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="pre-title">KABAR TERKINI</span>
            <h2 class="section-title">Kegiatan & Berita Terbaru</h2>
        </div>
        <?php if(empty($berita)): ?>
            <div class="text-center py-5">
                <h4 class="text-muted">Belum ada berita untuk ditampilkan.</h4>
            </div>
        <?php else: ?>
            <div class="news-scroll-wrapper">
                <div class="row row-scroll">
                    <?php foreach($berita as $item): ?>
                        <div class="col-md-4 mb-4 col-scroll-item">
                            <div class="card-news">
                                <a href="detail_berita.php?id=<?= urlencode($item['id_berita']) ?>" class="text-decoration-none">
                                    <div class="card-img-wrapper">
                                        <?php if(!empty($item['gambar']) && file_exists('uploads/' . $item['gambar'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($item['gambar']) ?>" class="card-img" alt="<?= htmlspecialchars($item['judul']) ?>">
                                        <?php else: ?>
                                            <img src="https://via.placeholder.com/400x300.png/f0f7f0/1B5E20?text=Berita" class="card-img" alt="<?= htmlspecialchars($item['judul']) ?>">
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title text-dark"><?= htmlspecialchars($item['judul']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars(substr(strip_tags($item['isi']), 0, 100)) ?>...</p>
                                    <a href="detail_berita.php?id=<?= urlencode($item['id_berita']) ?>" class="btn btn-primary-outline mt-auto">
                                        Baca Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- =======================================================
     BAGIAN KONTAK & PETA (TAMPILAN BARU YANG LEBIH BAIK)
     ======================================================== -->
<section id="kontak" class="section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="pre-title">TEMUKAN KAMI</span>
            <h2 class="section-title">Lokasi & Kontak</h2>
        </div>
        <div class="row g-lg-5 align-items-stretch">
            <!-- Kolom Informasi Kontak -->
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="contact-info-wrapper">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Alamat</h4>
                            <p>Dusun Wonorejo, Desa Wonomulyo,<br>Kec. Poncokusumo, Kab. Malang, Jawa Timur</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="contact-text">
                            <h4>WhatsApp</h4>
                            <p><a href="https://api.whatsapp.com/send?phone=<?= htmlspecialchars($nomor_wa) ?>" target="_blank">+<?= htmlspecialchars(chunk_split($nomor_wa, 4, ' ')) ?></a></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Email</h4>
                            <p><a href="mailto:info.kwtwonomulyo@gmail.com">info.kwtwonomulyo@gmail.com</a></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Peta Google -->
            <div class="col-lg-6">
                <div class="map-container">
                    <!-- CATATAN: Ganti 'src' dengan kode embed Google Maps Anda. Cari lokasi di Google Maps, klik Bagikan > Sematkan peta > SALIN HTML. -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15799.309489542176!2d112.7840138243469!3d-8.118239077694925!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd621f2a41a2935%3A0x5a3597b5e4822003!2sWonomulyo%2C%20Kec.%20Poncokusumo%2C%20Kabupaten%20Malang%2C%20Jawa%20Timur!5e0!3m2!1sid!2sid!4v1691565432109!5m2!1sid!2sid" 
                            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- =======================================================
     FOOTER (DISEDERHANAKAN & RESPONSIF)
     ======================================================== -->
<footer class="footer">
    <!-- Konten Utama Footer (Kini Tampil di Mobile) -->
    <div class="container footer-main-content">
        <div class="row">
            <div class="col-lg-5 col-md-12 mb-4 mb-lg-0">
                <h5 class="footer-title">KWT Wonomulyo</h5>
                <p class="pe-lg-4">
                    Sebuah kelompok wanita tani yang berdedikasi untuk mengolah hasil bumi lokal menjadi produk unggulan, demi kemandirian ekonomi dan pemberdayaan masyarakat desa.
                </p>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="footer-title">Tautan Cepat</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="#"><i class="fas fa-angle-right me-2"></i>Beranda</a></li>
                    <li><a href="#sejarah"><i class="fas fa-angle-right me-2"></i>Tentang</a></li>
                    <li><a href="#produk"><i class="fas fa-angle-right me-2"></i>Produk</a></li>
                    <li><a href="#berita"><i class="fas fa-angle-right me-2"></i>Berita</a></li>
                    <li><a href="#kontak"><i class="fas fa-angle-right me-2"></i>Kontak</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h5 class="footer-title">Informasi</h5>
                <p>Kunjungi kami untuk melihat langsung proses produksi dan membeli produk segar hasil olahan kami.</p>
            </div>
        </div>
    </div>
    
    <!-- Footer Bottom (Selalu ditampilkan) -->
    <div class="footer-bottom">
        <div class="container text-center">
            <p>© <?= date('Y') ?> Kelompok Wanita Tani Wonomulyo. All Rights Reserved.</p>
        </div>
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        const navLinks = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('section');
        const footer = document.querySelector('.footer');

        window.addEventListener('scroll', () => {
            let current = '';
            
            // Cek jika user sudah scroll sampai footer
            if ((window.innerHeight + window.scrollY) >= footer.offsetTop) {
                 current = 'kontak';
            } else {
                 sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    if (pageYOffset >= sectionTop - 150) {
                        current = section.getAttribute('id');
                    }
                });
            }

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                     link.classList.add('active');
                } else if (!current && link.getAttribute('href') === '#') {
                    // Jika paling atas, aktifkan link Beranda
                    link.classList.add('active');
                }
            });
        });
    });
</script>
</body>
</html>