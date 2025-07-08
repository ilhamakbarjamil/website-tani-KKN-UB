<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

$produk = ambil_semua_produk($koneksi);
$nomor_wa_pemesanan = '6282229820607';

// --- FUNGSI DIPINDAHKAN KE SINI (DI LUAR LOOP) ---
// Fungsi untuk format "1.2rb", "2.5rb", dll.
function format_terjual($angka) {
    if ($angka >= 1000) {
        // Membulatkan ke 1 desimal, misal: 1234 -> 1.2, 5678 -> 5.6
        return floor($angka / 100) / 10 . 'rb';
    }
    return $angka;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Produk Kami - KWT Wonomulyo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet" />
<style>
    :root {
        --primary: #2e7d32;
        --primary-dark: #1b5e20;
        --shopee-orange: #EE4D2D; /* Warna khas Shopee */
        --light: #f5f5f5; /* Background abu-abu muda */
        --dark: #333;
        --text-light: #888;
        --border-color: #e0e0e0;
        --shadow: 0 1px 1px 0 rgba(0,0,0,.05);
        --transition: all 0.2s ease-in-out;
        --border-radius: 4px; /* Radius lebih kecil */
    }

    body {
        font-family: 'Open Sans', 'Helvetica Neue', sans-serif;
        background-color: var(--light);
        color: var(--dark);
        font-size: 14px;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
    }

    a { text-decoration: none; color: inherit; }

    .navbar {
        background-color: white;
        box-shadow: 0 1px 1px rgba(0,0,0,.1);
    }
    .navbar .navbar-brand { font-size: 1.2rem; }
    .navbar .nav-link { font-size: 0.9rem; }

    .page-header {
        background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                    url('https://images.unsplash.com/photo-1464226184884-fa280b87c399?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80') center center/cover no-repeat;
        padding: 3rem 0;
        color: white;
        text-align: center;
    }
    .page-header h1 { color: white; }

    .products-section { padding: 24px 0; }

    .product-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        border: 1px solid transparent;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .product-card-link:hover .product-card {
        transform: translateY(-2px);
        border-color: var(--shopee-orange);
    }

    .product-img-wrapper {
        position: relative;
        padding-top: 100%; /* Membuat aspect ratio 1:1 */
        background-color: #f8f9fa;
    }
    .product-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-placeholder {
        position: absolute; top:0; left:0; width:100%; height:100%;
        display: flex; justify-content: center; align-items: center;
        font-size: 3rem; color: #adb5bd;
    }
    
    .product-label-diskon {
        position: absolute;
        top: 10px;
        left: -5px;
        background-color: var(--shopee-orange);
        color: white;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
        z-index: 2;
        box-shadow: 1px 1px 3px rgba(0,0,0,0.2);
    }
    .product-label-diskon .label-tail {
        position: absolute;
        left: 0px;
        bottom: -5px;
        width: 0;
        height: 0;
        border-top: 5px solid #9d311d; /* Warna lebih gelap untuk efek shadow */
        border-left: 5px solid transparent;
    }

    .product-card-body {
        padding: 8px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .product-title {
        font-size: 12px;
        font-weight: 400;
        line-height: 1.4;
        margin-bottom: 8px;
        height: 34px; /* 2 baris */
        color: var(--dark);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-price-section {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: auto; /* Mendorong ke bawah */
        margin-bottom: 8px;
    }
    .product-price {
        font-size: 16px;
        font-weight: 600;
        color: var(--shopee-orange);
    }
    .product-price-old {
        font-size: 12px;
        color: var(--text-light);
        text-decoration: line-through;
    }

    .product-meta-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        color: var(--text-light);
    }
    
    .footer {
        background: white;
        color: var(--text-light);
        padding: 20px 0;
        font-size: 0.8rem;
        text-align: center;
        border-top: 1px solid #eee;
        margin-top: 20px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 8px;
    }
    .empty-state i { font-size: 3rem; color: var(--primary); margin-bottom: 15px; }
    
    /* Responsive Grid */
    @media (max-width: 575.98px) {
        .row {
            --bs-gutter-x: 0.5rem;
            --bs-gutter-y: 0.5rem;
        }
        .product-title { font-size: 11px; height: 30px; }
        .product-price { font-size: 14px; }
    }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-leaf"></i> KWT Wonomulyo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
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
        <h1>Produk Olahan Singkong</h1>
        <p>Hasil karya Kelompok Wanita Tani Wonomulyo dari potensi lokal.</p>
    </div>
</header>

<!-- Main Product Grid -->
<section class="products-section">
    <div class="container">
        <?php if (empty($produk)): ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h4>Belum Ada Produk</h4>
                <p>Saat ini belum ada produk yang tersedia. Silakan cek kembali nanti.</p>
            </div>
        <?php else: ?>
            <div class="row g-2 g-md-3">
                <?php foreach($produk as $item): ?>
                <?php
                    // --- SIMULASI DATA TAMBAHAN UNTUK TAMPILAN ALA SHOPEE ---
                    $ada_diskon = rand(0, 1) === 1; // 50% kemungkinan produk punya diskon
                    $terjual = rand(50, 8000); 
                    $lokasi = "Malang";

                    $harga_asli = $item['harga'];
                    $harga_coret = 0;
                    if ($ada_diskon) {
                        $diskon_persen = rand(10, 35);
                        $harga_coret = $harga_asli;
                        $harga_asli = $harga_asli * (1 - ($diskon_persen / 100));
                    }
                    // --- FUNGSI DIHAPUS DARI SINI ---
                ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="https://wa.me/<?= $nomor_wa_pemesanan ?>?text=Halo%2C%20saya%20tertarik%20dengan%20produk%20'<?= urlencode($item['nama_produk']) ?>'" 
                           target="_blank" 
                           class="product-card-link">
                            <div class="product-card">
                                <?php if ($ada_diskon): ?>
                                    <div class="product-label-diskon">
                                        <span><?= $diskon_persen ?>%</span>
                                        <div class="label-tail"></div>
                                    </div>
                                <?php endif; ?>

                                <div class="product-img-wrapper">
                                    <?php if (!empty($item['gambar']) && file_exists('uploads/produk/' . $item['gambar'])): ?>
                                        <img src="uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" class="product-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>" loading="lazy" />
                                    <?php else: ?>
                                        <div class="product-placeholder"><i class="fas fa-leaf"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-card-body">
                                    <h5 class="product-title"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                                    
                                    <div class="product-price-section">
                                        <span class="product-price">Rp<?= number_format($harga_asli, 0, ',', '.') ?></span>
                                        <?php if ($harga_coret > 0): ?>
                                            <span class="product-price-old">Rp<?= number_format($harga_coret, 0, ',', '.') ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="product-meta-info">
                                        <span class="product-sold">Terjual <?= format_terjual($terjual) ?></span>
                                        <span class="product-location"><?= htmlspecialchars($lokasi) ?></span>
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
        <p>© <?= date('Y') ?> KWT Wonomulyo. All Rights Reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>