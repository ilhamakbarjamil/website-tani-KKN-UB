<?php
// Memuat file koneksi database dan fungsi-fungsi umum
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// --- PENGAMBILAN DATA ---

// 1. Ambil 3 berita terbaru untuk ditampilkan di beranda
$query_berita = "SELECT * FROM berita ORDER BY tanggal DESC LIMIT 3";
$result_berita = mysqli_query($koneksi, $query_berita);
$berita = [];
if ($result_berita) {
    while ($row = mysqli_fetch_assoc($result_berita)) {
        $berita[] = $row;
    }
}

// 2. Ambil 4 produk unggulan (terbaru dan stok > 0) untuk beranda
$query_produk = "SELECT * FROM produk WHERE stok > 0 ORDER BY created_at DESC LIMIT 4";
$result_produk = mysqli_query($koneksi, $query_produk);
$produk_unggulan = [];
if ($result_produk) {
    while ($row = mysqli_fetch_assoc($result_produk)) {
        $produk_unggulan[] = $row;
    }
}

// 3. Nomor WhatsApp untuk dihubungi (Ganti dengan nomor yang benar)
$nomor_wa = "6282229820607";

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelompok Wanita Tani Wonomulyo</title>
    <!-- Eksternal CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Internal CSS (Dengan Perbaikan Mobile Final) -->
    <style>
        :root {
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
            --primary-light: #4caf50;
            --primary-soft: #e8f5e9;
            --secondary: #ff9800;
            --secondary-soft: #fff3e0;
            --light: #f8f9fa;
            --dark: #343a40;
            --text: #333;
            --text-light: #6c757d;
            --border: #e0e0e0;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        /* === Global & Reset === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* PERBAIKAN: Mencegah scroll horizontal global */
        html, body {
            scroll-behavior: smooth;
            overflow-x: hidden;
            max-width: 100%;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--text);
            background-color: var(--light);
            line-height: 1.6;
        }
        
        /* PERBAIKAN: Mencegah scroll horizontal pada container */
        .container {
            overflow-x: hidden;
        }


        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--dark);
        }

        .pre-title {
            display: block;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--secondary);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .section-title {
            position: relative;
            margin-bottom: 50px;
            text-align: center;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        /* === Navbar === */
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

        .nav-link:hover,
        .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* === Hero Section === */
        .hero-section {
            background: linear-gradient(135deg, rgba(27, 94, 32, 0.85) 0%, rgba(46, 125, 50, 0.75) 100%),
                        url('https://images.unsplash.com/photo-1492496913980-501348b61469?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
            padding-top: 80px;
        }

        .hero-section::after {
            content: "";
            position: absolute;
            bottom: -1px;
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
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
            line-height: 1.2;
            animation: fadeInUp 1s ease-out;
            color: white;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 35px;
            opacity: 0.95;
            font-weight: 400;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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
            background: #ffb74d;
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

        /* === Scroll Indicator === */
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
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-15px); }
            60% { transform: translateY(-7px); }
        }
        
        /* === History Section === */
        .history-section {
            padding: 80px 0;
            background-color: var(--light);
        }
        
        .history-image {
            max-width: 400px;
            margin: 0 auto 30px auto;
        }

        .history-content .history-subtitle {
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .history-content .history-text {
            font-size: 1.05rem;
            line-height: 1.7;
            color: var(--text-light);
            margin-bottom: 20px;
        }

        .history-content .history-quote {
            border-left: 4px solid var(--primary);
            padding-left: 20px;
            margin-top: 25px;
            font-style: italic;
            color: #444;
        }

        .history-content .history-quote i {
            color: var(--primary-light);
            margin-right: 8px;
        }
        
        /* === Activities Section === */
        .activities-section {
            padding: 80px 0;
            background-color: #ffffff;
        }
        
        .activity-card {
            background-color: #fff;
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 40px 30px;
            text-align: center;
            height: 100%;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .activity-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .activity-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px auto;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2.5rem;
            transition: var(--transition);
        }

        .activity-card:hover .activity-icon {
            background: var(--secondary);
            transform: rotate(15deg) scale(1.1);
        }

        .activity-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 15px;
        }

        .activity-card p {
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 0;
        }
        
        .activities-section .row {
            display: flex;
            flex-wrap: wrap;
        }

        .activities-section .col-lg-4,
        .activities-section .col-md-6 {
            display: flex;
            flex-direction: column;
        }
        
        /* === Excellence Section === */
        .excellence-section-revamped {
            padding: 80px 0;
            background-color: #ffffff;
            overflow: hidden;
        }
        
        .excellence-image-wrapper {
            position: relative;
            padding: 15px;
            overflow: hidden;
        }

        .image-background-shape {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 90%;
            height: 90%;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            border-radius: 1.5rem;
            z-index: 1;
            transform: rotate(-6deg);
            transition: var(--transition);
        }

        .excellence-image-wrapper:hover .image-background-shape {
            transform: rotate(2deg) scale(1.03);
        }

        .excellence-image-wrapper img {
            position: relative;
            z-index: 2;
            object-fit: cover;
            width: 100%;
        }
        
        .excellence-content .section-title {
            text-align: left;
            margin-bottom: 25px;
        }
        
        .excellence-content .section-title::after {
            left: 0;
            transform: none;
        }
        
        .excellence-content .lead {
            font-size: 1.1rem;
            padding-bottom: 20px;
        }
        
        .feature-card {
            background-color: var(--light);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 30px;
            text-align: center;
            height: 100%;
            transition: var(--transition);
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(46, 125, 50, 0.15);
            border-color: var(--primary);
        }
        
        .feature-icon {
            width: 65px;
            height: 65px;
            margin-bottom: 20px;
            background-color: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon {
            background-color: var(--secondary);
            transform: scale(1.1) rotate(-15deg);
        }

        .feature-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 8px;
        }

        .feature-text {
            color: var(--text-light);
            line-height: 1.5;
            margin-bottom: 0;
        }
        
        .action-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            justify-content: center;
            color: #fff;
            position: relative;
        }

        .action-card:hover {
            box-shadow: 0 15px 30px rgba(46, 125, 50, 0.3);
        }

        .action-arrow {
            font-size: 1.5rem;
            margin-top: 10px;
            transition: var(--transition);
        }

        .action-card:hover .action-arrow {
            transform: translateX(8px);
        }

        /* === Products Section === */
        .products-section {
            padding: 80px 0;
            background-color: var(--light);
        }

        .products-section .products-scroll-wrapper .row {
            display: grid;
            grid-template-columns: repeat(4, 1fr); 
            gap: 1.5rem;
        }

        .col-product-item {
            display: flex;
        }

        .product-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .product-img-wrapper {
            position: relative;
            height: 220px;
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

        .product-img-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #e9ecef;
            color: #adb5bd;
        }

        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary);
            color: white;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .product-card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-brand {
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #9fa8b0;
            margin-bottom: 5px;
            display: block;
        }

        .product-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 10px;
            line-height: 1.4;
            margin-top: 0;
        }

        .product-desc {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 20px;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 54px;
        }

        .product-footer {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #f1f1f1;
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 15px;
        }
        
        /* === News Section === */
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

        /* === Contact Section === */
        .contact-section-revamped {
            padding: 80px 0;
            background-color: var(--light);
        }

        .contact-panel-wrapper {
            border-radius: 1.5rem;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
        }

        .contact-info-panel {
            padding: 50px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .contact-info-panel .panel-title {
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 30px;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .contact-item .icon-box {
            flex-shrink: 0;
            width: 45px;
            height: 45px;
            background-color: var(--primary-soft);
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 20px;
        }

        .contact-item .text-box strong {
            font-weight: 600;
            color: var(--primary-dark);
            display: block;
            margin-bottom: 2px;
        }

        .contact-item .text-box p {
            margin-bottom: 0;
            color: var(--text-light);
            line-height: 1.6;
        }

        .contact-item .text-box a {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .contact-item .text-box a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .panel-subtitle {
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 15px;
        }
        
        .op-hours li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            color: var(--text-light);
        }

        .badge.bg-primary-soft {
            background-color: var(--primary-soft) !important;
            color: var(--primary-dark) !important;
            font-weight: 600;
        }

        .badge.bg-secondary-soft {
            background-color: var(--secondary-soft) !important;
            color: #a0522d !important;
            font-weight: 600;
        }
        
        .map-wrapper {
            height: 100%;
            min-height: 450px;
        }

        .map-wrapper iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* === Footer Section === */
.footer-revamped {
    background-color: var(--primary-dark);
    color: rgba(255, 255, 255, 0.7);
    padding: 80px 0 0 0;
}

.footer-widget {
    margin-bottom: 40px;
}

.footer-logo {
    font-size: 1.8rem;
    font-weight: 700;
    color: #fff;
    display: inline-flex;
    align-items: center;
    margin-bottom: 20px;
}

.footer-logo i {
    color: var(--secondary);
    margin-right: 12px;
}

.footer-about {
    line-height: 1.8;
    margin-bottom: 25px;
}

.social-links {
    display: flex;
    gap: 12px;
}

.social-links a {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    text-decoration: none;
    transition: var(--transition);
}

.social-links a:hover {
    background-color: var(--secondary);
    border-color: var(--secondary);
    transform: translateY(-5px);
}

.footer-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 25px;
    letter-spacing: 0.5px;
}

.footer-links,
.footer-contact-info {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: var(--transition);
    position: relative;
    padding-left: 20px;
}

.footer-links a::before {
    content: '\f054';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    font-size: 0.7rem;
    position: absolute;
    left: 0;
    top: 5px;
    opacity: 0;
    color: var(--secondary);
    transition: var(--transition);
}

.footer-links a:hover {
    color: #fff;
    transform: translateX(5px);
}

.footer-links a:hover::before {
    opacity: 1;
}

.footer-contact-info li {
    display: flex;
    align-items: flex-start;
    margin-bottom: 18px;
    color: rgba(255, 255, 255, 0.7);
}

.footer-contact-info i {
    font-size: 1rem;
    color: var(--secondary);
    margin-right: 15px;
    margin-top: 5px;
    width: 20px;
    text-align: center;
}

.footer-contact-info a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: var(--transition);
}

.footer-contact-info a:hover {
    color: #fff;
    text-decoration: underline;
}

.footer-bottom {
    padding: 30px 0;
    margin-top: 40px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
}

.copyright-text {
    margin: 0;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.5);
}


/* === PERBAIKAN UNTUK TAMPILAN MOBILE === */
@media (max-width: 767.98px) {
    .footer-revamped {
        /* Mengurangi padding atas di mobile */
        padding-top: 50px;
    }

    .footer-widget {
        /* Mengatur semua teks di widget menjadi di tengah */
        text-align: center;
    }
    
    .footer-logo {
        /* Memastikan logo berada di tengah */
        justify-content: center;
    }

    .social-links {
        /* Memastikan ikon sosial media berada di tengah */
        justify-content: center;
    }
    
    .footer-contact-info li {
        /* Memastikan ikon dan teks kontak berada di tengah */
        justify-content: center;
        text-align: left; /* Teks kontak tetap rata kiri agar mudah dibaca */
    }

    .footer-links li {
        text-align: center;
    }

    .footer-links a {
        /* Menghapus padding kiri agar teks bisa pas di tengah */
        padding-left: 0;
        display: inline-block; /* Agar hover transform bekerja dengan baik */
    }

    .footer-links a::before {
        /* Menghilangkan ikon panah di mobile untuk tampilan lebih bersih */
        display: none;
    }
    
    .footer-links a:hover {
        /* Menghilangkan efek geser ke kanan di mobile */
        transform: none;
    }
}
        
        /* === Animations === */
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
        
        .animated {
            opacity: 0;
        }

        .animated.animate-in {
            animation: fadeInUp 0.8s ease forwards;
        }
        
        /* === Responsive Design (Tablet & Larger) === */
        @media (max-width: 991.98px) {
            .hero-title { font-size: 2.8rem; }
            .hero-subtitle { font-size: 1.2rem; }
            
            .history-content { text-align: center; }
            
            .excellence-section-revamped { padding: 60px 0; }
            .excellence-content { margin-top: 40px; text-align: center; }
            .excellence-content .section-title,
            .excellence-content .lead { text-align: center; }
            .excellence-content .section-title::after { left: 50%; transform: translateX(-50%); }
            
            .products-section .products-scroll-wrapper .row {
                grid-template-columns: repeat(2, 1fr);
            }

            .contact-section-revamped { padding: 60px 0; }
            .contact-info-panel { padding: 40px 25px; }
            .contact-panel-wrapper { background-color: transparent; box-shadow: none; border-radius: 0; }
            .contact-info-panel, .map-wrapper {
                border-radius: 1.5rem;
                background-color: #ffffff;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            }
            .map-wrapper { margin-top: 30px; }

            .footer-brand, .footer-widget { text-align: center; }
            .social-links, .footer-links, .footer-contact-info { justify-content: center; }
            .footer-contact-info li { text-align: left; }
        }

        /* === Responsive Design (Mobile) === */
        @media (max-width: 767.98px) {
            /* Mengurangi padding vertikal agar tidak terlalu banyak scroll */
            .history-section,
            .activities-section,
            .excellence-section-revamped,
            .products-section,
            .news-section,
            .contact-section-revamped {
                padding: 60px 0;
            }
            .footer-revamped {
                padding-top: 60px;
            }

            .hero-section { min-height: auto; padding: 100px 0 120px 0; }
            .hero-title { font-size: 2.2rem; }
            .hero-subtitle { font-size: 1rem; }
            .hero-stats { gap: 20px; margin-top: 40px; }
            .hero-stat-number { font-size: 2rem; }
            
            .btn-hero-primary,
            .btn-whatsapp {
                padding: 12px 28px;
                font-size: 1rem;
            }
            
            .section-title { font-size: 1.8rem; }
            
            /* PERBAIKAN: History Section untuk Mobile */
            .history-section .row {
                flex-direction: column;
                margin: 0;
            }
            
            .history-section .col-lg-5,
            .history-section .col-lg-7 {
                padding: 0;
                max-width: 100%;
                flex: 0 0 100%;
            }
            
            .history-image {
                max-width: 100%;
                margin-bottom: 20px;
            }

            /* PERBAIKAN: Products Section menjadi Carousel di Mobile */
            .products-scroll-wrapper::after {
                display: none;
            }
            
            .products-section .products-scroll-wrapper .row {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                scroll-snap-type: x mandatory;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 15px;
                margin: 0 -15px; /* Kompensasi padding container agar full-width */
                gap: 0; /* Hapus gap dari grid */
            }

            .col-product-item {
                flex: 0 0 85%; /* Lebar setiap item carousel */
                max-width: 85%;
                scroll-snap-align: start;
                padding: 0 8px; /* Jarak antar item */
            }

            /* Menambahkan padding kiri/kanan agar tidak menempel di tepi */
            .col-product-item:first-child {
                padding-left: 15px;
            }
            .col-product-item:last-child {
                padding-right: 15px;
            }

            /* PERBAIKAN: News Section menjadi Carousel di Mobile */
            .news-section .row {
                flex-wrap: nowrap;
                overflow-x: auto;
                scroll-snap-type: x mandatory;
                -webkit-overflow-scrolling: touch;
                margin: 0 -15px; /* Kompensasi padding container */
                padding-bottom: 15px;
            }
            
            .news-section .col-md-4 {
                flex: 0 0 85%; /* Lebar setiap item carousel */
                max-width: 85%;
                scroll-snap-align: start;
                padding: 0 8px; /* Jarak antar item */
                margin-bottom: 0; /* Hapus margin-bottom dari .mb-4 */
            }

            /* Menambahkan padding kiri/kanan agar tidak menempel di tepi */
            .news-section .col-md-4:first-child {
                padding-left: 15px;
            }
            .news-section .col-md-4:last-child {
                padding-right: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-leaf"></i>KWT Wonomulyo
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
                        <a class="nav-link" href="#kegiatan">Kegiatan</a>
                    </li>
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
            <h1 class="hero-title">KWT Wonomulyo</h1>
            <p class="hero-subtitle">Kelompok Wanita Tani Wonomulyo berkomitmen menumbuhkan produk lokal berkualitas, memperkuat ekonomi keluarga, dan melestarikan lingkungan.</p>
            <div class="hero-buttons">
                <a href="#kegiatan" class="btn-hero-primary">
                    <i class="fas fa-seedling me-2"></i>Lihat Kegiatan Kami
                </a>
                <a href="https://api.whatsapp.com/send?phone=<?= $nomor_wa ?>&text=Halo%20KWT%20Wonomulyo,%20saya%20tertarik%20untuk%20mengetahui%20lebih%20lanjut." target="_blank" class="btn-whatsapp">
                    <i class="fab fa-whatsapp me-2"></i>Hubungi Kami
                </a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number">25+</span>
                    <span class="hero-stat-label">Anggota Aktif</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">5+</span>
                    <span class="hero-stat-label">Jenis Produk Lokal</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">100%</span>
                    <span class="hero-stat-label">Organik & Alami</span>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <a href="#kegiatan"><i class="fas fa-chevron-down"></i></a>
        </div>
    </section>

    <!-- History Section -->
    <section id="sejarah" class="history-section">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <span class="pre-title">AWAL MULA KAMI</span>
                    <h2 class="section-title">Dari Pelatihan Hingga Kemandirian</h2>
                </div>
            </div>
            <div class="row align-items-center g-5">
                <div class="col-lg-5 text-center">
                    <img src="uploads/logo.png" class="img-fluid rounded-3 shadow-lg history-image" alt="Kegiatan Kelompok Wanita Tani Wonomulyo">
                </div>
                <div class="col-lg-7">
                    <div class="history-content">
                        <h3 class="history-subtitle">Terbentuk pada 4 Januari 2023</h3>
                        <p class="history-text">
                            KWT Wonomulyo, yang beranggotakan 10 orang, lahir dari gagasan setelah mengikuti pelatihan di BLK Wonojati. Kami memilih untuk fokus mengolah singkong yang melimpah di desa kami sebagai cara mengangkat potensi lokal. Dari bahan baku asli Wonomulyo ini, kami menciptakan beragam produk seperti keripik, samiler, stik, jemblem, dan getuk.
                        </p>
                        <blockquote class="history-quote">
                            <i class="fas fa-quote-left"></i>
                            Setiap produk yang kami hasilkan adalah cerita tentang kerja keras, kebersamaan, dan cinta kami pada hasil bumi lokal.
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Activities Section -->
    <section id="kegiatan" class="activities-section">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center animated" data-animation="fadeInUp">
                    <span class="pre-title">APA YANG KAMI LAKUKAN</span>
                    <h2 class="section-title">Kegiatan Utama Kami</h2>
                </div>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6 animated" data-animation="fadeInUp" style="animation-delay: 0.1s;">
                    <div class="activity-card">
                        <div class="activity-icon"><i class="fas fa-leaf"></i></div>
                        <div class="activity-content">
                            <h4 class="activity-title">Budidaya Singkong</h4>
                            <p>Mengembangkan singkong yang segar, sehat untuk keluarga dan ramah lingkungan.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 animated" data-animation="fadeInUp" style="animation-delay: 0.2s;">
                    <div class="activity-card">
                        <div class="activity-icon"><i class="fas fa-box-open"></i></div>
                        <div class="activity-content">
                            <h4 class="activity-title">Pengolahan Produk</h4>
                            <p>Menciptakan produk olahan bernilai tambah dari hasil panen, seperti keripik, stik, dan olahan lainnya.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 animated" data-animation="fadeInUp" style="animation-delay: 0.3s;">
                    <div class="activity-card">
                        <div class="activity-icon"><i class="fas fa-store"></i></div>
                        <div class="activity-content">
                            <h4 class="activity-title">Pemasaran Bersama</h4>
                            <p>Menjual produk secara kolektif untuk menjangkau pasar yang lebih luas dan mendapatkan harga yang adil.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Excellence Section -->
    <section id="keunggulan" class="excellence-section-revamped">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5 animated" data-animation="fadeInLeft">
                    <div class="excellence-image-wrapper">
                        <div class="image-background-shape"></div>
                        <img src="uploads/petani.jpeg" class="img-fluid rounded-3 shadow-lg" alt="Kelompok Wanita Tani di kebun">
                    </div>
                </div>
                <div class="col-lg-7 animated" data-animation="fadeInRight">
                    <div class="excellence-content">
                        <span class="pre-title">MENGAPA KAMI BERBEDA</span>
                        <h2 class="section-title">Dibangun dari Hati, untuk Negeri</h2>
                        <p class="lead text-muted mb-4">
                            Kami percaya bahwa kekuatan gotong royong dan kearifan lokal adalah fondasi untuk menciptakan kemandirian ekonomi yang berkelanjutan.
                        </p>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <div class="feature-icon"><i class="fas fa-seedling"></i></div>
                                    <h5 class="feature-title">Produk Segar & Sehat</h5>
                                    <p class="feature-text">Hasil panen langsung dari kebun kami yang diolah secara alami.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <div class="feature-icon"><i class="fas fa-hand-holding-heart"></i></div>
                                    <h5 class="feature-title">Ramah Lingkungan</h5>
                                    <p class="feature-text">Menggunakan pupuk organik dan metode pertanian berkelanjutan.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                                    <h5 class="feature-title">Pemberdayaan Komunitas</h5>
                                    <p class="feature-text">Setiap pembelian mendukung langsung ekonomi para wanita tani.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <a href="#kontak" class="feature-card action-card">
                                    <div class="action-content">
                                        <h5 class="feature-title text-white">Dukung Kami</h5>
                                        <p class="feature-text text-white-50">Jadilah bagian dari perubahan positif ini.</p>
                                        <span class="action-arrow"><i class="fas fa-arrow-right"></i></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="produk" class="products-section">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <span class="pre-title">PILIHAN TERBAIK</span>
                    <h2 class="section-title">Produk Unggulan Kami</h2>
                </div>
            </div>
            
            <?php if (empty($produk_unggulan)): ?>
                <div class="alert alert-light text-center">
                    Saat ini belum ada produk unggulan yang ditampilkan.
                </div>
            <?php else: ?>
                <div class="products-scroll-wrapper">
                    <div class="row">
                        <?php foreach($produk_unggulan as $index => $item): ?>
                            <div class="col-product-item animated" style="animation-delay: <?= $index * 0.1 ?>s;">
                                <div class="product-card">
                                    <div class="product-img-wrapper">
                                        <?php if (!empty($item['gambar']) && file_exists('uploads/produk/' . $item['gambar'])): ?>
                                            <img src="uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" class="product-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                                        <?php else: ?>
                                            <div class="product-img-placeholder">
                                                <i class="fas fa-leaf fa-3x"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="product-badge">Tersedia</div>
                                    </div>
                                    <div class="product-card-body">
                                        <p class="product-brand">RBY SNACK</p>
                                        <h5 class="product-title"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                                        <p class="product-desc"><?= htmlspecialchars($item['deskripsi']) ?></p>
                                        <div class="product-footer">
                                            <div class="product-price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></div>
                                            <a href="https://api.whatsapp.com/send?phone=<?= $nomor_wa ?>&text=Halo%2C%20saya%20tertarik%20untuk%20memesan%20produk%20*<?= urlencode($item['nama_produk']) ?>*." target="_blank" class="btn btn-primary w-100">
                                                <i class="fab fa-whatsapp me-2"></i>Pesan Sekarang
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="text-center mt-5">
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
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <span class="pre-title">KABAR TERKINI</span>
                    <h2 class="section-title">Kegiatan Terbaru kami</h2>
                </div>
            </div>
            <div class="row">
                <?php if(empty($berita)): ?>
                    <div class="col-12 text-center py-5 animated">
                        <h4>Belum ada berita</h4>
                        <p class="text-muted">Kegiatan dan informasi terbaru dari kelompok akan ditampilkan di sini.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($berita as $index => $item): ?>
                        <div class="col-md-4 mb-4 animated" style="animation-delay: <?= $index * 0.1 ?>s;">
                            <div class="news-card">
                                <?php if(!empty($item['gambar']) && file_exists('uploads/' . $item['gambar'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($item['gambar']) ?>" class="news-img" alt="<?= htmlspecialchars($item['judul']) ?>">
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
                                    <p class="news-text"><?= htmlspecialchars(substr(strip_tags($item['isi']), 0, 120)) ?>...</p>
                                    <a href="detail_berita.php?id=<?= urlencode($item['id_berita']) ?>" class="read-more">
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
    <section id="kontak" class="contact-section-revamped">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center animated" data-animation="fadeInUp">
                    <span class="pre-title">MARI TERHUBUNG</span>
                    <h2 class="section-title">Kunjungi & Hubungi Kami</h2>
                    <p class="lead text-muted">Kami sangat senang jika Anda ingin berkunjung, membeli produk, atau sekadar berdiskusi.</p>
                </div>
            </div>
            <div class="contact-panel-wrapper shadow">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="contact-info-panel">
                            <h3 class="panel-title">Informasi Kontak</h3>
                            <div class="contact-item">
                                <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
                                <div class="text-box">
                                    <strong>Alamat</strong>
                                    <p>Desa Robyong RT47, RW12 Wonomulyo, Poncokusumo, Malang</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="icon-box"><i class="fab fa-whatsapp"></i></div>
                                <div class="text-box">
                                    <strong>WhatsApp</strong>
                                    <p><a href="https://api.whatsapp.com/send?phone=<?= $nomor_wa ?>&text=Halo%20KWT%20Wonomulyo" target="_blank">+<?= substr($nomor_wa, 0, 2) . ' ' . substr($nomor_wa, 2) ?></a></p>
                                </div>
                            </div>
                            <hr class="my-4">
                            <h4 class="panel-subtitle">Jam Operasional</h4>
                            <ul class="list-unstyled op-hours">
                                <li><span>Senin - Sabtu</span> <span class="badge bg-primary-soft">08:00 - 16:00</span></li>
                                <li><span>Minggu & Hari Libur</span> <span class="badge bg-secondary-soft">Tutup</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="map-wrapper">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7901.205012555523!2d112.75963249748747!3d-8.039851971532162!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd624ff2e6802dd%3A0xdf8faadaaa7043b2!2sRobyong%2C%20Wonomulyo%2C%20Kec.%20Poncokusumo%2C%20Kabupaten%20Malang%2C%20Jawa%20Timur!5e0!3m2!1sid!2sid!4v1751857417844!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer" class="footer-revamped">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4 col-md-12">
                    <div class="footer-widget footer-brand">
                        <h4 class="footer-logo">
                            <i class="fas fa-leaf"></i>KWT Wonomulyo
                        </h4>
                        <p class="footer-about">
                            Sebuah komunitas wanita tani yang berdaya, menghasilkan produk lokal berkualitas dengan semangat gotong royong dan cinta pada alam.
                        </p>
                        <div class="social-links">
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://api.whatsapp.com/send?phone=<?= $nomor_wa ?>" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h5 class="footer-title">Navigasi</h5>
                        <ul class="footer-links">
                            <li><a href="#">Beranda</a></li>
                            <li><a href="#keunggulan">Keunggulan</a></li>
                            <li><a href="#kegiatan">Kegiatan</a></li>
                            <li><a href="#produk">Produk</a></li>
                            <li><a href="#kontak">Kontak Kami</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h5 class="footer-title">Hubungi Kami</h5>
                        <ul class="footer-contact-info">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Wonomulyo, Poncokusumo, Malang</span>
                            </li>
                            <li>
                                <i class="fas fa-phone-alt"></i>
                                <a href="https://api.whatsapp.com/send?phone=<?= $nomor_wa ?>" target="_blank">+<?= substr($nomor_wa, 0, 2) . ' ' . substr($nomor_wa, 2) ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="copyright-text">
                    © <?= date('Y') ?> Kelompok Wanita Tani Wonomulyo. Hak Cipta Dilindungi.
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar');
            const animatedElements = document.querySelectorAll('.animated');

            // Efek navbar saat scroll
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Animasi saat elemen terlihat di layar
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