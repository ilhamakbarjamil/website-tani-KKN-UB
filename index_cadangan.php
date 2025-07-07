<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// Ambil 3 berita terbaru untuk ditampilkan di beranda
$query_berita = "SELECT * FROM berita ORDER BY tanggal DESC LIMIT 3";
$result_berita = mysqli_query($koneksi, $query_berita);
$berita = [];
if ($result_berita) {
    while ($row = mysqli_fetch_assoc($result_berita)) {
        $berita[] = $row;
    }
}


// Ambil 4 produk unggulan (terbaru dan stok tersedia) untuk ditampilkan di beranda
$query_produk = "SELECT * FROM produk WHERE stok > 0 ORDER BY created_at DESC LIMIT 4";
$result_produk = mysqli_query($koneksi, $query_produk);
$produk_unggulan = [];
if ($result_produk) {
    while ($row = mysqli_fetch_assoc($result_produk)) {
        $produk_unggulan[] = $row;
    }
}

// MODIFIKASI: Nomor WhatsApp untuk dihubungi
$nomor_wa = "6281234567890"; // Ganti dengan nomor WhatsApp KWT Wonomulyo

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelompok Wanita Tani Wonomulyo</title>
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

        /* === History Section === */
.history-section {
    padding: 80px 0;
    background-color: #f8f9fa; /* Warna latar belakang lembut untuk membedakan section */
    /* Jika Anda menggunakan variabel CSS, ganti dengan: background-color: var(--light); */
}

.history-section .section-title {
    margin-bottom: 0;
}

/* Penyesuaian untuk gambar di mobile */
.history-image {
    max-width: 400px; /* Batasi lebar maks gambar agar tidak terlalu besar di desktop */
    margin: 0 auto; /* Otomatis tengahkan gambar dalam kolomnya */
    margin-bottom: 30px; /* Beri jarak bawah saat di tampilan mobile */
}

.history-content .history-subtitle {
    font-weight: 700;
    color: var(--primary-dark, #1a4314); /* Ganti dengan variabel warna Anda */
    margin-bottom: 15px;
    font-size: 1.5rem;
}

.history-content .history-text {
    font-size: 1.05rem;
    line-height: 1.7;
    color: var(--text-light, #555);
    margin-bottom: 20px;
}

.history-content .history-quote {
    border-left: 4px solid var(--primary, #2e7d32); /* Ganti dengan variabel warna Anda */
    padding-left: 20px;
    margin-top: 25px;
    font-style: italic;
    color: #444;
}

.history-content .history-quote i {
    color: var(--primary-light, #60ad5e);
    margin-right: 8px;
}


/* --- Penyesuaian Responsif untuk History Section --- */

/* Untuk Tampilan Desktop (Layar besar) */
@media (min-width: 992px) {
    .history-section {
        padding: 100px 0;
    }
    
    /* Hilangkan margin bawah gambar karena sudah berdampingan */
    .history-image {
        margin-bottom: 0;
    }

    /* Konten di desktop rata kiri */
    .history-content {
        text-align: left;
    }
}

/* Untuk Tampilan Mobile (Otomatis dari Bootstrap) */
/* Pada layar di bawah 992px (breakpoint 'lg'), kolom gambar dan teks akan otomatis tersusun vertikal.
   Kelas .text-center pada kolom gambar dan .g-5 pada .row sudah menangani perataan dan jaraknya. 
   CSS di atas telah dirancang secara mobile-first, jadi tidak perlu media query khusus untuk mobile. */

        /* === Activities Section (New Design) === */
.activities-section {
    padding: 80px 0;
    background-color: #ffffff; /* Latar putih untuk kontras */
}

.activity-card {
    background-color: #fff;
    border: 1px solid var(--border, #e0e0e0); /* Gunakan variabel jika ada, jika tidak pakai #e0e0e0 */
    border-radius: 1rem; /* Sudut lebih membulat */
    padding: 40px 30px;
    text-align: center;
    height: 100%; /* Membuat semua card dalam satu baris sama tinggi */
    transition: var(--transition, 0.3s ease);
    position: relative;
    overflow: hidden;
}

.activity-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    border-color: var(--primary, #2e7d32);
}

.activity-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 25px auto; /* Otomatis ke tengah dan beri jarak bawah */
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    border-radius: 50%; /* Membuat ikon berada dalam lingkaran */
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 2.5rem; /* Ukuran ikon lebih besar */
    transition: var(--transition, 0.3s ease);
}

.activity-card:hover .activity-icon {
    background: var(--secondary, #ff8f00); /* Ganti warna saat hover */
    transform: rotate(15deg) scale(1.1);
}

.activity-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--primary-dark, #1a4314);
    margin-bottom: 15px;
}

.activity-card p {
    color: var(--text-light, #555);
    line-height: 1.6;
    margin-bottom: 0;
}

/* Penyesuaian responsif sudah ditangani oleh kelas Bootstrap, 
   namun kita pastikan card selalu sama tinggi di baris yang sama. */
.activities-section .row {
    display: flex;
    flex-wrap: wrap;
}

.activities-section .col-lg-4,
.activities-section .col-md-6 {
    display: flex;
    flex-direction: column;
}

        /* === Excellence Section (Why Choose Us) - REVISED & RESPONSIVE DESIGN === */

/* --- Base Styles (Mobile First) --- */
.excellence-section {
    padding: 60px 0; /* Padding lebih kecil untuk mobile */
    background-color: #ffffff;
    overflow: hidden; 
}

.excellence-image-wrapper {
    position: relative;
    padding: 15px; /* Padding lebih kecil untuk mobile */
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
    /* Menghapus max-height agar gambar proporsional di mobile */
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

/* Konten di mobile akan berada di bawah gambar, jadi kita beri margin atas */
.excellence-content {
    margin-top: 40px; 
}

.excellence-content .section-title {
    /* Judul di tengah pada tampilan mobile */
    text-align: center; 
    margin-bottom: 20px;
}

.excellence-content .section-title::after {
    /* Posisikan garis bawah di tengah */
    left: 50%;
    transform: translateX(-50%);
}

.excellence-content .lead {
    font-size: 1.05rem; /* Sedikit lebih kecil di mobile */
    text-align: center; /* Teks lead juga di tengah */
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
    width: 50px; /* Sedikit lebih kecil di mobile */
    height: 50px;
    background-color: var(--primary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: white;
    font-size: 1.4rem;
    transition: var(--transition);
}

.excellence-item:hover .icon-wrapper {
    background: var(--secondary);
    transform: rotate(10deg);
}

.excellence-item .item-title {
    font-size: 1.1rem; /* Sedikit lebih kecil di mobile */
    font-weight: 600;
    color: var(--primary-dark);
    margin-bottom: 5px;
}

.excellence-item .item-text {
    color: var(--text-light);
    line-height: 1.6;
    margin-bottom: 0;
}

/* Tombol di tengah pada mobile */
.excellence-section .btn {
    display: block;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
}

/* --- Desktop Styles (Layar Besar) --- */
@media (min-width: 992px) {
    .excellence-section {
        padding: 100px 0; /* Kembalikan padding besar untuk desktop */
    }

    /* Hilangkan margin atas karena layout sudah side-by-side */
    .excellence-content {
        margin-top: 0; 
    }

    .excellence-content .section-title {
        /* Kembalikan perataan ke kiri untuk desktop */
        text-align: left; 
    }

    .excellence-content .section-title::after {
        /* Kembalikan posisi garis bawah ke kiri */
        left: 0;
        transform: none;
    }
    
    .excellence-content .lead {
        text-align: left; /* Teks lead kembali ke kiri */
        font-size: 1.1rem;
    }

    .excellence-item .icon-wrapper {
        width: 60px; /* Kembalikan ukuran ikon untuk desktop */
        height: 60px;
        font-size: 1.6rem;
    }

    .excellence-item .item-title {
        font-size: 1.2rem; /* Kembalikan ukuran font judul item */
    }
    
    /* Tombol kembali ke kiri di desktop */
    .excellence-section .btn {
        display: inline-block;
        width: auto;
        margin-left: 0;
        margin-right: 0;
    }
}
        
            /* ============================================= */
/*       STYLING UNTUK PRODUCTS SECTION          */
/* ============================================= */

.products-section {
    padding: 80px 0;
    background-color: #f8f9fa; /* Warna latar yang lembut */
}

.products-section .section-title {
    margin-bottom: 50px;
}

.product-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease-in-out;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    width: 100%; /* Memastikan kartu mengisi kolomnya */
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.product-img-wrapper {
    position: relative;
    height: 220px;
    overflow: hidden; /* Penting untuk efek zoom gambar */
}

/* Efek overlay saat hover */
.product-img-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0,0,0,0.2), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .product-img-wrapper::after {
    opacity: 1;
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

/* Placeholder untuk produk tanpa gambar */
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
    background: var(--primary, #28a745);
    color: white;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    z-index: 2;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.product-card-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1; /* Kunci agar body mengisi sisa ruang */
}

/* Styling untuk Merk Produk */
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
    color: var(--primary-dark, #343a40);
    margin-bottom: 10px;
    line-height: 1.4;
    margin-top: 0;
}

.product-desc {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 20px;
    flex-grow: 1; /* Mendorong footer ke bawah */

    /* Trik untuk memotong teks setelah 3 baris dengan elipsis (...) */
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 54px; /* Kira-kira tinggi 3 baris tulisan */
}

/* Wrapper untuk harga dan tombol agar selalu di bawah */
.product-footer {
    margin-top: auto; /* Mendorong blok ini ke bawah */
    padding-top: 15px;
    border-top: 1px solid #f1f1f1;
}

.product-price {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--secondary, #007bff);
    margin-bottom: 15px;
}

.product-card .btn {
    font-weight: 600;
    padding: 10px 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.product-card .btn:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

/* Tombol Lihat Semua Produk */
.btn-outline-primary {
    transition: all 0.3s ease;
}
.btn-outline-primary:hover {
    transform: translateY(-3px);
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
            <!-- MODIFIKASI: Nama dan Ikon Brand -->
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
                        <a class="nav-link" href="#kegiatan">Kegiatan</a> <!-- MODIFIKASI: Dari Layanan ke Kegiatan -->
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
            <!-- MODIFIKASI: Judul dan Subjudul Hero Section -->
            <h1 class="hero-title">Dari Tangan Wanita, Untuk Kesejahteraan Bersama</h1>
            <p class="hero-subtitle">Kelompok Wanita Tani Wonomulyo berkomitmen menumbuhkan produk lokal berkualitas, memperkuat ekonomi keluarga, dan melestarikan lingkungan.</p>
            <div class="hero-buttons">
                 <a href="#kegiatan" class="btn-hero-primary">
                    <i class="fas fa-seedling me-2"></i>Lihat Kegiatan Kami
                </a>
                <a href="https://api.whatsapp.com/send?phone=<?= $nomor_wa ?>&text=Halo%20KWT%20Wonomulyo,%20saya%20tertarik%20untuk%20mengetahui%20lebih%20lanjut." target="_blank" class="btn-whatsapp">
                    <i class="fab fa-whatsapp me-2"></i>Hubungi Kami
                </a>
            </div>
            <!-- MODIFIKASI: Data Statistik yang lebih sesuai -->
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
        <!-- Judul Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <span class="pre-title">AWAL MULA KAMI</span>
                <h2 class="section-title">Dari Pelatihan Hingga Kemandirian</h2>
            </div>
        </div>

        <!-- Konten Sejarah -->
        <div class="row align-items-center g-5">
            <!-- Kolom Gambar -->
            <div class="col-lg-5 text-center">
                <!-- Ganti src dengan foto asli Anda. Foto tentang pengolahan singkong/kegiatan kelompok akan sangat bagus. -->
                <img src="uploads/logo.png" 
                     class="img-fluid rounded-3 shadow-lg history-image" 
                     alt="Anggota KWT Mekar Sari sedang mengolah singkong">
            </div>

            <!-- Kolom Teks -->
            <div class="col-lg-7">
                <div class="history-content">
                    <h3 class="history-subtitle">Terbentuk pada 4 Januari 2023</h3>
                    <p class="history-text">
                        KWT Mekar Sari, yang beranggotakan 10 orang, lahir dari gagasan setelah mengikuti pelatihan di BLK Wonojati. Kami memilih untuk fokus mengolah singkong yang melimpah di desa kami sebagai cara mengangkat potensi lokal. Dari bahan baku asli Wonomulyo ini, kami menciptakan beragam produk seperti keripik, samiler, stik, jemblem, dan getuk.
                    </p>
                    <!-- <p class="history-text">
                        Kami memutuskan untuk fokus pada singkong. Kenapa? Karena singkong melimpah di desa kami. Ini adalah cara kami untuk mengangkat produk lokal dan memastikan bahan baku produk kami benar-benar asli dari tanah Wonomulyo. Dari singkong sederhana, kami ciptakan berbagai olahan lezat seperti keripik, samiler (emping singkong), stik, jemblem, hingga getuk.
                    </p> -->
                    <blockquote class="history-quote">
                        <i class="fas fa-quote-left"></i>
                        Setiap produk yang kami hasilkan adalah cerita tentang kerja keras, kebersamaan, dan cinta kami pada hasil bumi lokal.
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Activities Section (Revised) -->
<section id="kegiatan" class="activities-section">
    <div class="container">
        <!-- Section Header -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center animated" data-animation="fadeInUp">
                <span class="pre-title">APA YANG KAMI LAKUKAN</span>
                <h2 class="section-title">Kegiatan Utama Kami</h2>
            </div>
        </div>

        <!-- Activity Cards -->
        <div class="row g-4 justify-content-center">
            <!-- Card 1: Budidaya Organik -->
            <div class="col-lg-4 col-md-6 animated" data-animation="fadeInUp" style="animation-delay: 0.1s;">
                <div class="activity-card">
                    <div class="activity-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div class="activity-content">
                        <h4 class="activity-title">Budidaya Organik</h4>
                        <p>Mengembangkan sayuran dan buah-buahan segar tanpa pestisida kimia, sehat untuk keluarga dan ramah lingkungan.</p>
                    </div>
                </div>
            </div>

            <!-- Card 2: Pengolahan Produk -->
            <div class="col-lg-4 col-md-6 animated" data-animation="fadeInUp" style="animation-delay: 0.2s;">
                <div class="activity-card">
                    <div class="activity-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="activity-content">
                        <h4 class="activity-title">Pengolahan Produk</h4>
                        <p>Menciptakan produk olahan bernilai tambah dari hasil panen, seperti keripik, jus, dan makanan tradisional.</p>
                    </div>
                </div>
            </div>

            <!-- Card 3: Pemasaran Bersama -->
            <div class="col-lg-4 col-md-6 animated" data-animation="fadeInUp" style="animation-delay: 0.3s;">
                <div class="activity-card">
                    <div class="activity-icon">
                        <!-- PERBAIKAN: Kelas Font Awesome yang benar adalah "fas fa-store" -->
                        <i class="fas fa-store"></i>
                    </div>
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
<section id="keunggulan" class="excellence-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Kolom Gambar -->
            <div class="col-lg-6 animated" style="animation-delay: 0.1s;">
                <div class="excellence-image-wrapper">
                    <div class="image-background-shape"></div>
                    <!-- MODIFIKASI: Menggunakan gambar placeholder yang relevan. Ganti dengan gambar Anda. -->
                    <img src="uploads/logo.png" class="img-fluid rounded-3 shadow-lg" alt="Kelompok Wanita Tani sedang bekerja di kebun">
                </div>
            </div>
            <!-- Kolom Konten -->
            <div class="col-lg-6 animated" style="animation-delay: 0.2s;">
                <div class="excellence-content">
                    <span class="pre-title">SEMANGAT KAMI</span>
                    <h2 class="section-title">Kekuatan Gotong Royong dan Kearifan Lokal</h2>
                    <p class="lead text-muted mb-4">
                       Kami percaya bahwa dengan bekerja bersama, para wanita di Wonomulyo dapat mandiri secara ekonomi sambil menjaga kelestarian alam untuk generasi mendatang.
                    </p>
                    <div class="vstack gap-3">
                        <div class="excellence-item">
                            <div class="d-flex align-items-start">
                                <div class="icon-wrapper flex-shrink-0">
                                    <i class="fas fa-seedling"></i>
                                </div>
                                <div>
                                    <h5 class="item-title">Produk Segar & Sehat</h5>
                                    <p class="item-text mb-0">Hasil panen langsung dari kebun kami, diolah dengan cara alami untuk menjaga kualitas terbaik.</p>
                                </div>
                            </div>
                        </div>
                        <div class="excellence-item">
                            <div class="d-flex align-items-start">
                                <div class="icon-wrapper flex-shrink-0">
                                    <i class="fas fa-hand-holding-heart"></i>
                                </div>
                                <div>
                                    <h5 class="item-title">Proses Ramah Lingkungan</h5>
                                    <p class="item-text mb-0">Menggunakan pupuk organik dan metode pertanian berkelanjutan yang menjaga kesuburan tanah.</p>
                                </div>
                            </div>
                        </div>
                        <div class="excellence-item">
                           <div class="d-flex align-items-start">
                                <div class="icon-wrapper flex-shrink-0">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h5 class="item-title">Pemberdayaan Komunitas</h5>
                                    <p class="item-text mb-0">Setiap pembelian produk mendukung langsung kemandirian ekonomi para wanita di desa kami.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#kontak" class="btn btn-primary mt-4 py-2 px-4 fw-bold">
                        Dukung Kami <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- ============================================= -->
<!--          START: PRODUCTS SECTION              -->
<!-- ============================================= -->
<section id="produk" class="products-section">
    <div class="container">
        <h2 class="section-title animated">Produk Unggulan Kami</h2>
        
        <?php if (empty($produk_unggulan)): ?>
            <div class="alert alert-light text-center">
                Saat ini belum ada produk unggulan yang ditampilkan.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($produk_unggulan as $index => $item): ?>
                    <div class="col-lg-3 col-md-6 mb-4 d-flex align-items-stretch animated" style="animation-delay: <?= $index * 0.1 ?>s;">
                        
                        <!-- Product Card Start -->
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <?php if (!empty($item['gambar']) && file_exists('uploads/produk/' . $item['gambar'])): ?>
                                    <img src="uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" class="product-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                                <?php else: ?>
                                    <!-- Placeholder yang lebih baik untuk produk tanpa gambar -->
                                    <div class="product-img-placeholder">
                                        <i class="fas fa-leaf fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="product-badge">Tersedia</div>
                            </div>

                            <div class="product-card-body">
                                <!-- Merk Produk -->
                                <p class="product-brand">RBY SNACK</p>
                                
                                <h5 class="product-title"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                                
                                <p class="product-desc"><?= htmlspecialchars($item['deskripsi']) ?></p>
                                
                                <!-- Footer kartu didorong ke bawah oleh flex-grow di .product-desc -->
                                <div class="product-footer">
                                    <div class="product-price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></div>
                                    <a href="https://api.whatsapp.com/send?phone=<?= $nomor_wa ?>&text=Halo%2C%20saya%20tertarik%20untuk%20memesan%20produk%20*<?= urlencode($item['nama_produk']) ?>*." target="_blank" class="btn btn-primary w-100">
                                        <i class="fab fa-whatsapp me-2"></i>Pesan Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Product Card End -->

                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5">
                <a href="produk.php" class="btn btn-outline-primary fw-bold py-2 px-4">
                    Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
<!-- ============================================= -->
<!--           END: PRODUCTS SECTION               -->
<!-- ============================================= -->
    <!-- News Section -->
    <section id="berita" class="news-section">
        <div class="container">
            <h2 class="section-title animated">Berita & Kegiatan Terbaru</h2>
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
                                    <img src="uploads/<?= $item['gambar'] ?>" class="news-img" alt="<?= htmlspecialchars($item['judul']) ?>">
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
    <section id="kontak" class="contact-section">
        <div class="container">
            <h2 class="section-title animated">Hubungi Kami</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-5 d-flex flex-column animated" style="animation-delay: 0.1s;">
                    <div class="contact-info-card bg-white p-4 p-md-5 shadow-sm rounded-3 h-100">
                        <h3 class="mb-4 text-primary">Informasi & Lokasi</h3>
                        <p class="text-muted mb-4">Kami sangat senang jika Anda ingin berkunjung, membeli produk, atau sekadar berdiskusi. Hubungi kami melalui detail di bawah ini.</p>
                        
                        <!-- MODIFIKASI: Detail kontak yang lebih sesuai -->
                        <div class="contact-item d-flex mb-4">
                            <i class="fas fa-map-marker-alt fa-2x text-primary me-4 mt-1"></i>
                            <div>
                                <strong>Alamat:</strong><br>
                                Dusun Wonomulyo, Desa Makmur Jaya, Kec. Sejahtera, Kabupaten Hijau, 55123
                            </div>
                        </div>
                        <div class="contact-item d-flex mb-4">
                            <i class="fas fa-envelope fa-2x text-primary me-4 mt-1"></i>
                            <div>
                                <strong>Email:</strong><br>
                                <a href="mailto:kwt.wonomulyo@email.com">kwt.wonomulyo@email.com</a>
                            </div>
                        </div>
                         <div class="contact-item d-flex mb-4">
                            <i class="fab fa-whatsapp fa-2x text-primary me-4 mt-1"></i>
                            <div>
                                <strong>WhatsApp:</strong><br>
                                <a href="https://api.whatsapp.com/send?phone=<?= $nomor_wa ?>&text=Halo%20KWT%20Wonomulyo" target="_blank">+<?= substr($nomor_wa, 0, 2) . ' ' . substr($nomor_wa, 2) ?></a>
                            </div>
                        </div>

                        <hr>
                        
                        <h4 class="h5 mt-4 mb-3 text-dark">Jam Operasional</h4>
                        <ul class="list-unstyled">
                            <li class="d-flex justify-content-between mb-2">
                                <span>Senin - Sabtu</span>
                                <span class="fw-bold">08:00 - 16:00</span>
                            </li>
                            <li class="d-flex justify-content-between text-muted">
                                <span>Minggu & Hari Libur</span>
                                <span class="fw-bold">Tutup</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

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
                    <!-- MODIFIKASI: Footer Branding -->
                    <div class="footer-logo">
                        <i class="fas fa-leaf"></i>KWT Wonomulyo
                    </div>
                    <p class="footer-about">Sebuah komunitas wanita tani yang berdaya, menghasilkan produk lokal berkualitas dengan semangat gotong royong dan cinta pada alam.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="https://api.whatsapp.com/send?phone=<?= $nomor_wa ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <h5 class="footer-title">Link Cepat</h5>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Beranda</a></li>
                        <li><a href="#kegiatan"><i class="fas fa-chevron-right me-2"></i>Kegiatan</a></li>
                        <li><a href="#produk"><i class="fas fa-chevron-right me-2"></i>Produk</a></li>
                        <li><a href="#berita"><i class="fas fa-chevron-right me-2"></i>Berita & Kegiatan</a></li>
                        <li><a href="#kontak"><i class="fas fa-chevron-right me-2"></i>Kontak Kami</a></li>
                        <li><a href="admin.php"><i class="fas fa-chevron-right me-2"></i>Admin Area</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="footer-title">Kegiatan Kami</h5>
                    <ul class="footer-links">
                        <li><a href="#kegiatan"><i class="fas fa-chevron-right me-2"></i>Budidaya Organik</a></li>
                        <li><a href="#kegiatan"><i class="fas fa-chevron-right me-2"></i>Pengolahan Hasil Panen</a></li>
                        <li><a href="#kegiatan"><i class="fas fa-chevron-right me-2"></i>Pemasaran Bersama</a></li>
                        <li><a href="#kontak"><i class="fas fa-chevron-right me-2"></i>Kunjungan & Edukasi</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <!-- MODIFIKASI: Copyright -->
                <p>© <?= date('Y') ?> Kelompok Wanita Tani Wonomulyo. Hak Cipta Dilindungi.</p>
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