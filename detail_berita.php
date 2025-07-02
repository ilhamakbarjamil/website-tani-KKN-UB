<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// Periksa apakah parameter id ada
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_berita = bersihkan_input($_GET['id']);

// Ambil berita berdasarkan ID
$query = "SELECT * FROM berita WHERE id_berita = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_berita);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$berita = mysqli_fetch_assoc($result);

if (!$berita) {
    header("Location: index.php");
    exit();
}

// Ambil semua berita untuk bagian terkait
$query_semua = "SELECT * FROM berita WHERE id_berita != ? ORDER BY tanggal DESC LIMIT 3";
$stmt_semua = mysqli_prepare($koneksi, $query_semua);
mysqli_stmt_bind_param($stmt_semua, "i", $id_berita);
mysqli_stmt_execute($stmt_semua);
$berita_terkait = mysqli_stmt_get_result($stmt_semua);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($berita['judul']) ?> - Petani Maju</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Semua style dari index.php */
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
        .page-header {
            background: linear-gradient(rgba(46, 125, 50, 0.9), rgba(46, 125, 50, 0.9));
            padding: 100px 0 60px;
            color: white;
            margin-bottom: 40px;
        }

        /* Detail Berita */
        .news-detail-section {
            padding: 60px 0;
        }

        .news-detail-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 40px;
        }

        .news-detail-img {
            width: 100%;
            height: 450px;
            object-fit: cover;
        }

        .news-detail-body {
            padding: 40px;
        }

        .news-detail-date {
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .news-detail-date i {
            margin-right: 8px;
        }

        .news-detail-title {
            font-size: 2.2rem;
            margin-bottom: 20px;
            color: var(--primary-dark);
        }

        .news-detail-content {
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .news-detail-content p {
            margin-bottom: 20px;
        }

        .related-news-section {
            background-color: #f0f7f0;
            padding: 60px 0;
        }

        .related-news-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 40px;
            text-align: center;
            color: var(--primary-dark);
        }

        .related-news-title::after {
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

        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 70px 0 20px;
            position: relative;
        }

        .footer-content {
            margin-bottom: 40px;
        }

        .footer-logo {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .footer-logo i {
            margin-right: 10px;
            font-size: 2rem;
        }

        .footer-about {
            margin-bottom: 25px;
            line-height: 1.7;
            opacity: 0.9;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            transition: var(--transition);
            text-decoration: none;
        }

        .social-links a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        .footer-title {
            font-size: 1.3rem;
            margin-bottom: 25px;
            color: white;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
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
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            opacity: 0.8;
        }

        .breadcrumb {
            background: transparent;
            margin-bottom: 0;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            color: white;
        }

        .breadcrumb-item.active {
            color: rgba(255, 255, 255, 0.9);
        }

        @media (max-width: 767px) {
            .news-detail-img {
                height: 250px;
            }
            
            .news-detail-body {
                padding: 25px;
            }
            
            .news-detail-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-tractor me-2"></i>Petani Maju
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#berita">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#kontak">Kontak</a>
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

    <!-- Page Header -->
    <section class="page-header">
        <div class="container text-center">
            <h1>Detail Berita</h1>
            <nav aria-label="breadcrumb" class="d-flex justify-content-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="index.php#berita">Berita</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars(substr($berita['judul'], 0, 30)) ?>...</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- News Detail -->
    <section class="news-detail-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="news-detail-card">
                        <?php if(!empty($berita['gambar'])): ?>
                            <img src="uploads/<?= htmlspecialchars($berita['gambar']) ?>" class="news-detail-img" alt="<?= htmlspecialchars($berita['judul']) ?>">
                        <?php else: ?>
                            <div class="news-detail-img bg-secondary d-flex align-items-center justify-content-center">
                                <i class="fas fa-image fa-5x text-white"></i>
                            </div>
                        <?php endif; ?>
                        <div class="news-detail-body">
                            <div class="news-detail-date">
                                <i class="fas fa-calendar me-1"></i>
                                <?= date('d M Y', strtotime($berita['tanggal'])) ?>
                            </div>
                            <h1 class="news-detail-title"><?= htmlspecialchars($berita['judul']) ?></h1>
                            <div class="news-detail-content">
                                <?= nl2br(htmlspecialchars($berita['isi'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related News -->
    

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row footer-content">
                <div class="col-md-6">
                    <div class="footer-logo">
                        <i class="fas fa-tractor me-2"></i>Petani Maju
                    </div>
                    <p class="footer-about">Petani Maju adalah portal informasi pertanian desa yang bertujuan untuk memberdayakan masyarakat dan menyebarkan informasi bermanfaat melalui berita serta kegiatan terkini.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5 class="footer-title">Navigasi</h5>
                    <ul class="footer-links">
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="index.php#berita">Berita</a></li>
                        <li><a href="index.php#kontak">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="footer-title">Kontak</h5>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-envelope me-2"></i>email@desa.id</a></li>
                        <li><a href="#"><i class="fas fa-phone me-2"></i>+62 812-3456-7890</a></li>
                        <li><a href="#"><i class="fas fa-map-marker-alt me-2"></i>Desa Sejahtera, Indonesia</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                &copy; <?= date('Y') ?> Petani Maju. Semua hak dilindungi.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
