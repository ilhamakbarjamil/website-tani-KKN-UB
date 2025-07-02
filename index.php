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
    <title>Website Petani - Maju Bersama Pertanian Indonesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
    <div class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold">Petani Indonesia Maju Bersama</h1>
            <p class="lead">Solusi terbaik untuk meningkatkan hasil pertanian dan kesejahteraan petani</p>
            <a href="https://wa.me/6281234567890" target="_blank" class="whatsapp-btn mt-3">
                <i class="fab fa-whatsapp me-2"></i>Konsultasi via WhatsApp
            </a>
        </div>
    </div>

    <!-- Fitur Unggulan -->
    <div class="container py-5">
        <h2 class="section-title">Layanan Kami</h2>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <h4>Penyuluhan Pertanian</h4>
                <p>Konsultasi dengan ahli pertanian untuk meningkatkan hasil panen</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h4>Distribusi Hasil Panen</h4>
                <p>Jaringan distribusi luas untuk memasarkan hasil pertanian</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon">
                    <i class="fas fa-book"></i>
                </div>
                <h4>Edukasi Modern</h4>
                <p>Pelatihan teknik pertanian modern untuk petani</p>
            </div>
        </div>
    </div>

    <!-- Berita Terbaru -->
    <div class="bg-light-green py-5" id="berita">
        <div class="container">
            <h2 class="section-title">Berita & Kegiatan Terbaru</h2>
            <div class="row">
                <?php if(empty($berita)): ?>
                    <div class="col-12 text-center py-5">
                        <h4>Belum ada berita</h4>
                        <p>Admin dapat menambahkan berita baru</p>
                    </div>
                <?php else: ?>
                    <?php foreach($berita as $item): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if($item['gambar']): ?>
                                    <img src="uploads/<?= $item['gambar'] ?>" class="card-img-top" alt="<?= $item['judul'] ?>">
                                <?php else: ?>
                                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height:200px;">
                                        <i class="fas fa-image fa-3x text-white"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= $item['judul'] ?></h5>
                                    <p class="card-text"><?= substr($item['isi'], 0, 100) ?>...</p>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?= date('d M Y', strtotime($item['tanggal'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kontak -->
    <div class="container py-5" id="kontak">
        <h2 class="section-title">Hubungi Kami</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="card-title"><i class="fas fa-map-marker-alt me-2"></i> Lokasi Kami</h4>
                        <p class="card-text">
                            Jl. Pertanian No. 123, Kec. Tani Makmur<br>
                            Kabupaten Agro Sejahtera, Provinsi Lumbung Pangan<br>
                            Indonesia 12345
                        </p>
                        <h4 class="card-title mt-4"><i class="fas fa-phone me-2"></i> Telepon</h4>
                        <p class="card-text">(021) 1234-5678</p>
                        <h4 class="card-title mt-4"><i class="fas fa-envelope me-2"></i> Email</h4>
                        <p class="card-text">info@petanimaju.id</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="card-title"><i class="fab fa-whatsapp me-2"></i> WhatsApp</h4>
                        <p class="card-text">Kirim pesan langsung via WhatsApp untuk konsultasi cepat</p>
                        <a href="https://wa.me/6281234567890" target="_blank" class="whatsapp-btn mt-3">
                            <i class="fab fa-whatsapp me-2"></i>Chat Sekarang
                        </a>
                        <div class="mt-4">
                            <h4 class="card-title"><i class="fas fa-clock me-2"></i> Jam Operasional</h4>
                            <p class="card-text">
                                Senin - Jumat: 08:00 - 16:00 WIB<br>
                                Sabtu: 08:00 - 14:00 WIB<br>
                                Minggu & Hari Libur: Tutup
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4><i class="fas fa-tractor me-2"></i>Petani Maju</h4>
                    <p>Meningkatkan kesejahteraan petani melalui teknologi dan inovasi pertanian modern.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h4>Link Cepat</h4>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Beranda</a></li>
                        <li><a href="#berita" class="text-white">Berita</a></li>
                        <li><a href="#kontak" class="text-white">Kontak</a></li>
                        <li><a href="admin.php" class="text-white">Admin</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h4>Media Sosial</h4>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-whatsapp fa-2x"></i></a>
                    </div>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center">
                <p>&copy; <?= date('Y') ?> Website Petani Maju. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>