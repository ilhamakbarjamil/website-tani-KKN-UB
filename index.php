<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// Ambil semua berita
$berita = ambil_semua_berita($koneksi);

// BARU: Data produk (dummy). Nantinya bisa diganti dari database.
// Letakkan bagian PHP ini di bagian atas file index.php atau sebelum section produk

// Ambil 4 produk unggulan (terbaru dan stok tersedia) untuk ditampilkan di beranda
$query_produk = "SELECT * FROM produk WHERE stok > 0 ORDER BY created_at DESC LIMIT 4";
$result_produk = mysqli_query($koneksi, $query_produk);
$produk_unggulan = [];
if ($result_produk) {
    while ($row = mysqli_fetch_assoc($result_produk)) {
        $produk_unggulan[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petani Maju - Solusi Pertanian Modern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
            --primary-light: #4caf50;
            --secondary: #ff9800;
            --light: #f8f9fa;
            --dark: #343a40;
            --text: #333;
            --text-light: #6c757d;
            --border: #e0e0e0;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--text);
            background-color: var(--light);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--dark);
        }

        /* Navbar - Sedikit penyesuaian untuk transisi yang lebih baik */
        .navbar {
            background-color: transparent;
            padding: 1rem 0;
            transition: background-color 0.4s ease, box-shadow 0.4s ease, padding 0.4s ease;
        }

        .navbar.scrolled {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: var(--shadow);
            padding: 0.75rem 0;
            backdrop-filter: blur(10px);
        }
        
        .navbar.scrolled .navbar-brand,
        .navbar.scrolled .nav-link {
            color: var(--primary-dark) !important;
        }

        .navbar.scrolled .nav-link:hover, 
        .navbar.scrolled .nav-link.active {
            color: var(--primary) !important;
            background-color: rgba(46, 125, 50, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }

        .navbar-brand i {
            margin-right: 10px;
            font-size: 1.8rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            margin: 0 8px;
            padding: 8px 15px !important;
            border-radius: 50px;
            transition: var(--transition);
        }

        .nav-link:hover, .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* === HERO SECTION - IMPROVED === */
        .hero-section {
            background: linear-gradient(
                135deg,
                rgba(27, 94, 32, 0.85) 0%,
                rgba(46, 125, 50, 0.75) 100%
            ),
            url('https://images.unsplash.com/photo-1492496913980-501348b61469?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Parallax effect */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
            padding-top: 80px; /* Space for navbar */
        }
        
        /* Wave effect at bottom */
        .hero-section::after {
            content: "";
            position: absolute;
            bottom: -1px; /* To prevent small gaps */
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23f8f9fa" fill-opacity="1" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,261.3C672,256,768,224,864,197.3C960,171,1056,149,1152,160C1248,171,1344,213,1392,234.7L1440,256L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
            z-index: 2;
        }

        .hero-content {
            position: relative;
            z-index: 3;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .hero-title {
            font-size: 3.5rem; /* Adjusted for balance */
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 3px 6px rgba(0,0,0,0.3);
            line-height: 1.2;
            animation: fadeInUp 1s ease-out;
            color: white; /* Ensure it's white */
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 35px;
            opacity: 0.95;
            font-weight: 400;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.4s both;
        }
        
        .btn-hero-primary {
            background: var(--secondary);
            color: var(--dark);
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            border: none;
            font-size: 1.1rem;
        }

        .btn-hero-primary:hover {
            background: #ffb74d; /* Lighter shade of secondary */
            color: var(--dark);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .btn-whatsapp {
            background-color: #25D366;
            color: white;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
            box-shadow: var(--shadow);
            border: none;
            font-size: 1.1rem;
        }

        .btn-whatsapp:hover {
            background-color: #128C7E;
            color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 60px;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.6s both;
        }

        .hero-stat {
            text-align: center;
            color: white;
        }

        .hero-stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Scroll indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            animation: bounce 2s infinite;
        }

        .scroll-indicator a {
            color: white;
            font-size: 1.5rem;
            text-decoration: none;
            opacity: 0.8;
            transition: var(--transition);
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-15px);
            }
            60% {
                transform: translateY(-7px);
            }
        }
        /* === END HERO SECTION === */

        /* Features Section */
        .features-section {
            padding: 80px 0;
            position: relative;
            z-index: 5; /* Ensure it is above the hero wave */
            background-color: var(--light);
        }

        .section-title {
            position: relative;
            padding-bottom: 20px;
            margin-bottom: 50px;
            text-align: center;
            color: var(--primary-dark);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border-radius: 2px;
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            height: 100%;
            border: none;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 25px;
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(15deg);
            background: linear-gradient(135deg, var(--secondary), #ffb74d);
        }

        .feature-title {
            font-size: 1.4rem;
            margin-bottom: 15px;
        }

        /* === Excellence Section (Why Choose Us) - REVISED DESIGN === */
        .excellence-section {
            padding: 100px 0;
            background-color: #ffffff;
            overflow: hidden; 
        }

        .excellence-image-wrapper {
            position: relative;
            padding: 20px; 
        }

        .image-background-shape {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 85%;
            height: 85%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 1rem;
            z-index: 1;
            transform: rotate(-5deg);
            transition: var(--transition);
        }

        .excellence-image-wrapper:hover .image-background-shape {
            transform: rotate(0deg) scale(1.05);
        }

        .excellence-image-wrapper img {
            position: relative;
            z-index: 2;
            object-fit: cover;
            width: 100%;
            height: 100%;
            max-height: 500px;
        }
        .pre-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--secondary);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            display: block;
            margin-bottom: 10px;
        }

        .excellence-content .section-title {
            text-align: left;
            margin-bottom: 20px;
        }

        .excellence-content .section-title::after {
            left: 0;
            transform: none;
        }

        .excellence-content .lead {
            font-size: 1.1rem;
        }
        .excellence-item {
            background-color: var(--light);
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 12px;
            transition: var(--transition);
        }

        .excellence-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(46, 125, 50, 0.15);
            border-color: var(--primary-light);
        }

        .excellence-item .icon-wrapper {
            width: 60px;
            height: 60px;
            background-color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            font-size: 1.6rem;
            transition: var(--transition);
        }

        .excellence-item:hover .icon-wrapper {
            background: var(--secondary);
            transform: rotate(10deg);
        }

        .excellence-item .item-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 5px;
        }

        .excellence-item .item-text {
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 0;
        }
        
        /* BARU: Styling untuk Section Produk */
        .products-section {
            padding: 80px 0;
            background-color: var(--light);
        }

        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            height: 100%;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .product-img-wrapper {
            position: relative;
            height: 220px;
        }
        
        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(27, 94, 32, 0.9);
            color: white;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
        }

        .product-card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1; /* Membuat body mengisi sisa ruang */
        }

        .product-title {
            font-size: 1.2rem;
            color: var(--primary-dark);
            margin-bottom: 10px;
            flex-grow: 1; /* Mendorong harga dan tombol ke bawah */
        }

        .product-desc {
            font-size: 0.95rem;
            color: var(--text-light);
            margin-bottom: 15px;
            min-height: 60px; /* Jaga tinggi deskripsi agar seragam */
        }
        
        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 15px;
        }

        .product-card .btn {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
            font-weight: 500;
            transition: var(--transition);
        }

        .product-card .btn:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: scale(1.05);
        }


        /* News Section */
        .news-section {
            background-color: #f0f7f0;
            padding: 80px 0;
        }

        .news-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            height: 100%;
            border: none;
            position: relative;
        }

        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .news-img {
            height: 220px;
            width: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .news-card:hover .news-img {
            transform: scale(1.05);
        }

        .news-date {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary);
            color: white;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .news-body {
            padding: 25px;
        }

        .news-title {
            font-size: 1.3rem;
            margin-bottom: 12px;
            color: var(--primary-dark);
        }

        .news-text {
            color: var(--text-light);
            margin-bottom: 20px;
        }

        .read-more {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
        }

        .read-more:hover {
            color: var(--primary-dark);
        }

        .read-more i {
            margin-left: 5px;
            transition: var(--transition);
        }

        .read-more:hover i {
            transform: translateX(3px);
        }

        /* Contact Section */
        .contact-section {
            padding: 80px 0;
            background-color: var(--light);
        }
        
        /* Footer */
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

        .footer-about {
            margin-bottom: 25px;
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

        .footer-links a i {
            margin-right: 8px;
            font-size: 0.9rem;
        }

        .footer-links a:hover {
            color: white;
            transform: translateX(5px);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .social-links a:hover {
            background: var(--secondary);
            transform: translateY(-5px);
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            margin-top: 50px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-title {
                font-size: 2.8rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 767px) {
            .hero-section {
                min-height: auto;
                padding-top: 100px;
                padding-bottom: 120px;
            }
            
            .hero-title {
                font-size: 2.3rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }

            .hero-stats {
                gap: 20px;
                margin-top: 40px;
            }

            .hero-stat-number {
                font-size: 2rem;
            }
            
            .feature-card, .news-card, .contact-card, .product-card {
                margin-bottom: 30px;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Kelas untuk memicu animasi saat di-scroll */
        .animated {
            opacity: 0;
        }
        .animated.animate-in {
            animation: fadeInUp 0.8s ease forwards;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-tractor"></i>Petani Maju
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#layanan">Layanan</a>
                    </li>
                    <!-- BARU: Link Navbar Produk -->
                    <li class="nav-item">
                        <a class="nav-link" href="#produk">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#berita">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">
                            <i class="fas fa-lock me-1"></i>Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Membangun Masa Depan Pertanian Indonesia</h1>
            <p class="hero-subtitle">Solusi terintegrasi untuk meningkatkan hasil panen, efisiensi, dan kesejahteraan petani di seluruh nusantara.</p>
            <div class="hero-buttons">
                 <a href="#layanan" class="btn-hero-primary">
                    <i class="fas fa-seedling me-2"></i>Lihat Layanan Kami
                </a>
                <a href="https://api.whatsapp.com/send?phone=6281234567890&text=Halo%20Petani%20Maju,%20saya%20tertarik%20untuk%20berkonsultasi." target="_blank" class="btn-whatsapp">
                    <i class="fab fa-whatsapp me-2"></i>Konsultasi Gratis
                </a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number">1,200+</span>
                    <span class="hero-stat-label">Petani Mitra</span>
                </div>
                 <div class="hero-stat">
                    <span class="hero-stat-number">50+</span>
                    <span class="hero-stat-label">Daerah Jangkauan</span>
                </div>
                 <div class="hero-stat">
                    <span class="hero-stat-number">30%</span>
                    <span class="hero-stat-label">Peningkatan Panen</span>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <a href="#layanan"><i class="fas fa-chevron-down"></i></a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="layanan" class="features-section">
        <div class="container">
            <h2 class="section-title animated">Layanan Unggulan Kami</h2>
            <div class="row">
                <div class="col-md-4 mb-4 animated" style="animation-delay: 0.1s;">
                    <div class="feature-card text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <h4 class="feature-title">Penyuluhan Pertanian</h4>
                        <p>Konsultasi dengan ahli pertanian untuk meningkatkan hasil panen dan kualitas produk pertanian Anda.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 animated" style="animation-delay: 0.2s;">
                    <div class="feature-card text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h4 class="feature-title">Distribusi Hasil Panen</h4>
                        <p>Jaringan distribusi luas untuk memasarkan hasil pertanian langsung ke konsumen dan pasar modern.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 animated" style="animation-delay: 0.3s;">
                    <div class="feature-card text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h4 class="feature-title">Edukasi Modern</h4>
                        <p>Pelatihan teknik pertanian modern untuk meningkatkan produktivitas dan efisiensi usaha tani.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Excellence Section -->
    <section id="keunggulan" class="excellence-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 animated" style="animation-delay: 0.1s;">
                    <div class="excellence-image-wrapper">
                        <div class="image-background-shape"></div>
                        <img src="https://images.unsplash.com/photo-1625246333195-78d9c38ad449?ixlib=rb-4.0.3&auto=format&fit=crop&w=987&q=80" class="img-fluid rounded-3 shadow-lg" alt="Petani Sukses dengan Teknologi Modern">
                    </div>
                </div>
                <div class="col-lg-6 animated" style="animation-delay: 0.2s;">
                    <div class="excellence-content">
                        <span class="pre-title">KOMITMEN KAMI</span>
                        <h2 class="section-title text-start">Mitra Terpercaya untuk Pertanian Berkelanjutan</h2>
                        <p class="lead text-muted mb-4">
                            Kami menggabungkan kearifan lokal dengan inovasi terdepan untuk menciptakan ekosistem pertanian yang produktif, efisien, dan menguntungkan.
                        </p>
                        <div class="vstack gap-3">
                            <div class="excellence-item">
                                <div class="d-flex align-items-start">
                                    <div class="icon-wrapper flex-shrink-0">
                                        <i class="fas fa-brain"></i>
                                    </div>
                                    <div>
                                        <h5 class="item-title">Inovasi Berbasis Data</h5>
                                        <p class="item-text mb-0">Analisis data akurat untuk setiap keputusan, mulai dari pemilihan bibit hingga prediksi panen.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="excellence-item">
                                <div class="d-flex align-items-start">
                                    <div class="icon-wrapper flex-shrink-0">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div>
                                        <h5 class="item-title">Hasil Terbukti</h5>
                                        <p class="item-text mb-0">Peningkatan hasil panen rata-rata 30% dan efisiensi biaya yang signifikan bagi mitra kami.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="excellence-item">
                               <div class="d-flex align-items-start">
                                    <div class="icon-wrapper flex-shrink-0">
                                        <i class="fas fa-handshake-angle"></i>
                                    </div>
                                    <div>
                                        <h5 class="item-title">Pendampingan Ahli</h5>
                                        <p class="item-text mb-0">Tim ahli kami memberikan pendampingan berkelanjutan untuk memastikan kesuksesan Anda.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="#kontak" class="btn btn-primary mt-4 py-2 px-4 fw-bold">
                            Diskusi dengan Ahli Kami <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BARU: Products Section -->
        <!-- MODIFIKASI: Bagian Section Produk yang sudah terhubung ke database -->
<section id="produk" class="products-section">
    <div class="container">
        <h2 class="section-title animated">Produk Unggulan Kami</h2>
        
        <?php if (empty($produk_unggulan)): ?>
            <div class="alert alert-light text-center">Saat ini belum ada produk unggulan yang ditampilkan.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach($produk_unggulan as $index => $item): ?>
                    <div class="col-lg-3 col-md-6 mb-4 d-flex align-items-stretch animated" style="animation-delay: <?= $index * 0.1 ?>s;">
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <!-- Menampilkan gambar dari folder uploads/produk/ -->
                                <?php if (!empty($item['gambar']) && file_exists('uploads/produk/' . $item['gambar'])): ?>
                                    <img src="uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" class="product-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                                <?php else: ?>
                                    <!-- Placeholder jika gambar tidak ada -->
                                    <div class="d-flex justify-content-center align-items-center bg-light h-100">
                                        <i class="fas fa-leaf fa-4x text-muted"></i>
                                    </div>
                                <?php endif; ?>

                                <!-- Badge ketersediaan stok -->
                                <div class="product-badge bg-success">Tersedia</div>
                            </div>
                            <div class="product-card-body">
                                <!-- Menggunakan kolom 'nama_produk' -->
                                <h5 class="product-title"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                                
                                <!-- Menggunakan kolom 'deskripsi', dipotong agar tidak terlalu panjang -->
                                <p class="product-desc"><?= htmlspecialchars(substr($item['deskripsi'], 0, 70)) ?>...</p>
                                
                                <!-- Menggunakan kolom 'harga' dan memformatnya -->
                                <div class="product-price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></div>
                                
                                <!-- Tombol aksi ke WhatsApp -->
                                <a href="https://api.whatsapp.com/send?phone=6281234567890&text=Halo%20Petani%20Maju,%20saya%20tertarik%20dengan%20produk%20*<?= urlencode($item['nama_produk']) ?>*" target="_blank" class="btn btn-primary mt-auto w-100">
                                    <i class="fab fa-whatsapp me-2"></i>Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Tombol untuk melihat semua produk -->
            <div class="text-center mt-4">
                <a href="produk.php" class="btn btn-outline-primary fw-bold py-2 px-4">
                    Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        <?php endif; ?>

    </div>
</section>





    <!-- News Section -->
    <section id="berita" class="news-section">
        <div class="container">
            <h2 class="section-title animated">Berita & Kegiatan Terbaru</h2>
            <div class="row">
                <?php if(empty($berita)): ?>
                    <div class="col-12 text-center py-5 animated">
                        <h4>Belum ada berita</h4>
                        <p>Admin dapat menambahkan berita baru</p>
                    </div>
                <?php else: ?>
                    <?php foreach($berita as $index => $item): ?>
                        <div class="col-md-4 mb-4 animated" style="animation-delay: <?= $index * 0.1 ?>s;">
                            <div class="news-card">
                                <?php if($item['gambar']): ?>
                                    <img src="uploads/<?= $item['gambar'] ?>" class="news-img" alt="<?= $item['judul'] ?>">
                                <?php else: ?>
                                    <div class="news-img bg-secondary d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-3x text-white"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="news-date">
                                    <i class="fas fa-calendar me-1"></i><?= date('d M Y', strtotime($item['tanggal'])) ?>
                                </div>
                                <div class="news-body">
                                    <h5 class="news-title"><?= htmlspecialchars($item['judul']) ?></h5>
                                    <p class="news-text"><?= htmlspecialchars(substr($item['isi'], 0, 120)) ?>...</p>
                                    <a href="detail_berita.php?id=<?php echo urlencode($item['id_berita']); ?>" class="read-more">
                                        Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="kontak" class="contact-section">
        <div class="container">
            <h2 class="section-title animated">Hubungi Kami</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-5 d-flex flex-column animated" style="animation-delay: 0.1s;">
                    <div class="contact-info-card bg-white p-4 p-md-5 shadow-sm rounded-3 h-100">
                        <h3 class="mb-4 text-primary">Informasi & Lokasi</h3>
                        <p class="text-muted mb-4">Kami siap membantu Anda. Hubungi kami melalui detail di bawah ini selama jam kerja.</p>

                        <div class="contact-item d-flex mb-4">
                            <i class="fas fa-map-marker-alt fa-2x text-primary me-4 mt-1"></i>
                            <div>
                                <strong>Alamat:</strong><br>
                                Jl. Pertanian No. 123, Kec. Tani Makmur, Kabupaten Agro Sejahtera, Indonesia 12345
                            </div>
                        </div>
                        <div class="contact-item d-flex mb-4">
                            <i class="fas fa-envelope fa-2x text-primary me-4 mt-1"></i>
                            <div>
                                <strong>Email:</strong><br>
                                <a href="mailto:info@petanimaju.id">info@petanimaju.id</a>
                            </div>
                        </div>
                         <div class="contact-item d-flex mb-4">
                            <i class="fab fa-whatsapp fa-2x text-primary me-4 mt-1"></i>
                            <div>
                                <strong>WhatsApp:</strong><br>
                                <a href="https://api.whatsapp.com/send?phone=6281234567890&text=Halo%20Petani%20Maju" target="_blank">+62 812-3456-7890</a>
                            </div>
                        </div>

                        <hr>
                        
                        <h4 class="h5 mt-4 mb-3 text-dark">Jam Operasional</h4>
                        <ul class="list-unstyled">
                            <li class="d-flex justify-content-between mb-2">
                                <span>Senin - Jumat</span>
                                <span class="fw-bold">08:00 - 16:00</span>
                            </li>
                            <li class="d-flex justify-content-between mb-2">
                                <span>Sabtu</span>
                                <span class="fw-bold">08:00 - 14:00</span>
                            </li>
                            <li class="d-flex justify-content-between text-muted">
                                <span>Minggu & Hari Libur</span>
                                <span class="fw-bold">Tutup</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bagian Peta -->
            <div class="row mt-5 animated" style="animation-delay: 0.3s;">
                <div class="col-12">
                    <div class="map-container shadow-sm rounded-3" style="overflow: hidden;">
                         <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15829.96571060964!2d110.3639880295629!3d-7.842785121404172!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a57a195b8535f%3A0xb35a51357f085183!2sArea%20Sawah%2C%20Bangunharjo%2C%20Kec.%20Sewon%2C%20Kabupaten%20Bantul%2C%20Daerah%20Istimewa%20Yogyakarta!5e0!3m2!1sid!2sid!4v1691565432109!5m2!1sid!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row footer-content">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <div class="footer-logo">
                        <i class="fas fa-tractor"></i>Petani Maju
                    </div>
                    <p class="footer-about">Meningkatkan kesejahteraan petani melalui teknologi dan inovasi pertanian modern untuk Indonesia yang lebih maju.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <h5 class="footer-title">Link Cepat</h5>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Beranda</a></li>
                        <li><a href="#layanan"><i class="fas fa-chevron-right me-2"></i>Layanan</a></li>
                        <li><a href="#produk"><i class="fas fa-chevron-right me-2"></i>Produk</a></li>
                        <li><a href="#berita"><i class="fas fa-chevron-right me-2"></i>Berita & Kegiatan</a></li>
                        <li><a href="#kontak"><i class="fas fa-chevron-right me-2"></i>Kontak Kami</a></li>
                        <li><a href="admin.php"><i class="fas fa-chevron-right me-2"></i>Admin Area</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="footer-title">Layanan Kami</h5>
                    <ul class="footer-links">
                        <li><a href="#layanan"><i class="fas fa-chevron-right me-2"></i>Penyuluhan Pertanian</a></li>
                        <li><a href="#layanan"><i class="fas fa-chevron-right me-2"></i>Distribusi Hasil Panen</a></li>
                        <li><a href="#layanan"><i class="fas fa-chevron-right me-2"></i>Pelatihan Modern</a></li>
                        <li><a href="#kontak"><i class="fas fa-chevron-right me-2"></i>Konsultasi Ahli</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>© <?= date('Y') ?> Shinta. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar');
            const animatedElements = document.querySelectorAll('.animated');

            // Navbar style change on scroll
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Intersection Observer for animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            animatedElements.forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
