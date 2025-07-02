<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// Ambil semua berita
$berita = ambil_semua_berita($koneksi);
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

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--text);
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--dark);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            transition: var(--transition);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-whatsapp {
            background-color: #25D366;
            color: white;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
            box-shadow: var(--shadow);
            border: none;
        }

        .btn-whatsapp:hover {
            background-color: #128C7E;
            color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-whatsapp i {
            margin-right: 8px;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            transition: var(--transition);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
            display: flex;
            align-items: center;
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

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(46, 125, 50, 0.9), rgba(46, 125, 50, 0.9)), 
                        url('https://images.unsplash.com/photo-1492496913980-501348b61469?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            padding: 120px 0 100px;
            color: white;
            text-align: center;
            margin-bottom: 60px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23f8f9fa" fill-opacity="1" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,261.3C672,256,768,224,864,197.3C960,171,1056,149,1152,160C1248,171,1344,213,1392,234.7L1440,256L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
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
            transform: scale(1.1);
            background: linear-gradient(135deg, var(--secondary), #ffb74d);
        }

        .feature-title {
            font-size: 1.4rem;
            margin-bottom: 15px;
        }

        /* News Section */
        .news-section {
            background-color: #f0f7f0;
            padding: 80px 0;
            position: relative;
        }

        .news-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23f8f9fa" fill-opacity="1" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,261.3C672,256,768,224,864,197.3C960,171,1056,149,1152,160C1248,171,1344,213,1392,234.7L1440,256L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>');
            background-size: cover;
            background-position: top;
            z-index: 0;
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
            transform: scale(1.03);
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
        }

        .contact-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            height: 100%;
            border: none;
            transition: var(--transition);
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .contact-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 25px;
            text-align: center;
        }

        .contact-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .contact-title {
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        .contact-body {
            padding: 25px;
        }

        .contact-item {
            display: flex;
            margin-bottom: 20px;
            align-items: flex-start;
        }

        .contact-item i {
            color: var(--primary);
            font-size: 1.2rem;
            min-width: 30px;
            padding-top: 3px;
        }

        .contact-text {
            flex: 1;
        }

        .hours-list {
            list-style: none;
            padding-left: 0;
        }

        .hours-list li {
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
        }

        .hours-list li:not(:last-child) {
            border-bottom: 1px dashed var(--border);
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 70px 0 20px;
            position: relative;
        }

        .footer::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23f8f9fa" fill-opacity="1" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,261.3C672,256,768,224,864,197.3C960,171,1056,149,1152,160C1248,171,1344,213,1392,234.7L1440,256L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: top;
            z-index: 0;
        }

        .footer-content {
            position: relative;
            z-index: 1;
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
                font-size: 1.3rem;
            }
        }

        @media (max-width: 767px) {
            .hero-section {
                padding: 80px 0 70px;
            }
            
            .hero-title {
                font-size: 2.3rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .feature-card, .news-card, .contact-card {
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

        .animate {
            animation: fadeInUp 0.8s ease forwards;
        }

        .delay-1 {
            animation-delay: 0.1s;
        }

        .delay-2 {
            animation-delay: 0.2s;
        }

        .delay-3 {
            animation-delay: 0.3s;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-tractor me-2"></i>Petani Maju
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
        <div class="container">
            <div class="hero-content animate">
                <h1 class="hero-title">Petani Indonesia Maju Bersama</h1>
                <p class="hero-subtitle">Solusi terbaik untuk meningkatkan hasil pertanian dan kesejahteraan petani</p>
                <a href="" target="_blank" class="btn btn-whatsapp">
                    <i class="fab fa-whatsapp me-2"></i>Konsultasi via WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title animate">Layanan Kami</h2>
            <div class="row">
                <div class="col-md-4 mb-4 animate delay-1">
                    <div class="feature-card text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <h4 class="feature-title">Penyuluhan Pertanian</h4>
                        <p>Konsultasi dengan ahli pertanian untuk meningkatkan hasil panen dan kualitas produk pertanian Anda.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 animate delay-2">
                    <div class="feature-card text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h4 class="feature-title">Distribusi Hasil Panen</h4>
                        <p>Jaringan distribusi luas untuk memasarkan hasil pertanian langsung ke konsumen dan pasar modern.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 animate delay-3">
                    <div class="feature-card text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h4 class="feature-title">Edukasi Modern</h4>
                        <p>Pelatihan teknik pertanian modern untuk meningkatkan produktivitas dan efisiensi usaha tani.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section id="berita" class="news-section">
        <div class="container">
            <h2 class="section-title animate">Berita & Kegiatan Terbaru</h2>
            <div class="row">
                <?php if(empty($berita)): ?>
                    <div class="col-12 text-center py-5">
                        <div class="animate">
                            <h4>Belum ada berita</h4>
                            <p>Admin dapat menambahkan berita baru</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach($berita as $item): ?>
                        <div class="col-md-4 mb-4 animate">
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
            <h2 class="section-title animate">Hubungi Kami</h2>
            <div class="row">
                <div class="col-md-6 mb-4 animate delay-1">
                    <div class="contact-card">
                        <div class="contact-header">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h4 class="contact-title">Lokasi Kami</h4>
                        </div>
                        <div class="contact-body">
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="contact-text">
                                    <h5>Alamat</h5>
                                    <p>Jl. Pertanian No. 123, Kec. Tani Makmur<br>
                                    Kabupaten Agro Sejahtera, Provinsi Lumbung Pangan<br>
                                    Indonesia 12345</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <div class="contact-text">
                                    <h5>Telepon</h5>
                                    <p>(021) 1234-5678</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <div class="contact-text">
                                    <h5>Email</h5>
                                    <p>info@petanimaju.id</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 animate delay-2">
                    <div class="contact-card">
                        <div class="contact-header">
                            <div class="contact-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <h4 class="contact-title">Konsultasi WhatsApp</h4>
                        </div>
                        <div class="contact-body">
                            <div class="contact-item">
                                <i class="fab fa-whatsapp"></i>
                                <div class="contact-text">
                                    <h5>WhatsApp</h5>
                                    <p>Kirim pesan langsung via WhatsApp untuk konsultasi cepat dengan ahli pertanian kami.</p>
                                    <a href="" target="_blank" class="btn btn-whatsapp mt-2">
                                        <i class="fab fa-whatsapp me-2"></i>Chat Sekarang
                                    </a>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-clock"></i>
                                <div class="contact-text">
                                    <h5>Jam Operasional</h5>
                                    <ul class="hours-list">
                                        <li><span>Senin - Jumat:</span> <span>08:00 - 16:00 WIB</span></li>
                                        <li><span>Sabtu:</span> <span>08:00 - 14:00 WIB</span></li>
                                        <li><span>Minggu & Hari Libur:</span> <span>Tutup</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
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
                        <li><a href="#berita"><i class="fas fa-chevron-right me-2"></i>Berita & Kegiatan</a></li>
                        <li><a href="#kontak"><i class="fas fa-chevron-right me-2"></i>Kontak Kami</a></li>
                        <li><a href="admin.php"><i class="fas fa-chevron-right me-2"></i>Admin Area</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="footer-title">Layanan Kami</h5>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Penyuluhan Pertanian</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Distribusi Hasil Panen</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Pelatihan Modern</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Konsultasi Ahli</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?= date('Y') ?> Website Petani Maju. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animasi saat scroll
        document.addEventListener('DOMContentLoaded', function() {
            // Animasi saat elemen masuk ke viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.animate').forEach(el => {
                observer.observe(el);
            });

            // Animasi scroll untuk navigasi internal
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        window.scrollTo({
                            top: target.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Efek hover untuk kartu
            const cards = document.querySelectorAll('.feature-card, .news-card, .contact-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-10px)';
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0)';
                });
            });

            // Alert untuk WhatsApp
            document.querySelectorAll('.btn-whatsapp').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    if (!e.ctrlKey && !e.metaKey) {
                        e.preventDefault();
                        alert('Anda akan diarahkan ke WhatsApp untuk konsultasi dengan ahli pertanian kami.');
                        window.open(this.href, '_blank');
                    }
                });
            });
        });
    </script>
</body>
</html>