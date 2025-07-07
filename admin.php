<?php
// Pastikan session dimulai di awal file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// Proses login
$error = '';
if (isset($_POST['login'])) {
    $username = bersihkan_input($_POST['username']);
    $password = bersihkan_input($_POST['password']);
    
    $query = "SELECT * FROM admin WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        $error = "Error database: " . mysqli_error($koneksi);
    } else {
        $admin = mysqli_fetch_assoc($result);
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin.php"); 
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    }
    mysqli_stmt_close($stmt);
}

// Proses logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// ===================================================================
// INISIALISASI VARIABEL
// ===================================================================
$error_upload = '';
$success_msg = '';

// Jika tidak login, hentikan eksekusi script
if (!isset($_SESSION['admin_id'])) {
    // Biarkan script lanjut untuk menampilkan form login di HTML
} else {

    // Menampilkan pesan sukses dari redirect
    if (isset($_GET['status'])) {
        switch ($_GET['status']) {
            case 'produk_ditambah': $success_msg = "Produk baru berhasil ditambahkan!"; break;
            case 'produk_diupdate': $success_msg = "Produk berhasil diperbarui!"; break;
            case 'produk_dihapus': $success_msg = "Produk berhasil dihapus."; break;
            case 'berita_ditambah': $success_msg = "Berita baru berhasil ditambahkan!"; break;
            case 'berita_diupdate': $success_msg = "Berita berhasil diperbarui!"; break;
            case 'berita_dihapus': $success_msg = "Berita berhasil dihapus."; break;
        }
    }

    // ===================================================================
    // --- PENGAMBILAN DATA UNTUK DASHBOARD ---
    // ===================================================================
    $total_produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(id_produk) AS total FROM produk"))['total'] ?? 0;
    $total_berita = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(id_berita) AS total FROM berita"))['total'] ?? 0;
    
    $recent_produk = [];
    $result_produk = mysqli_query($koneksi, "SELECT id_produk, nama_produk, gambar, stok FROM produk ORDER BY id_produk DESC LIMIT 5");
    if($result_produk) {
        while($row = mysqli_fetch_assoc($result_produk)) {
            $recent_produk[] = $row;
        }
    }

    $recent_berita = [];
    $result_berita = mysqli_query($koneksi, "SELECT id_berita, judul, gambar, tanggal FROM berita ORDER BY id_berita DESC LIMIT 5");
    if($result_berita) {
        while($row = mysqli_fetch_assoc($result_berita)) {
            $recent_berita[] = $row;
        }
    }

    // ===================================================================
    // --- PROSES CRUD BERITA ---
    // ===================================================================
    if (isset($_POST['tambah_berita'])) {
        $judul = bersihkan_input($_POST['judul']);
        $isi = bersihkan_input($_POST['isi']);
        $gambar = '';
        if (isset($_FILES['gambar_berita']) && $_FILES['gambar_berita']['error'] == 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $result = upload_gambar($_FILES['gambar_berita'], $target_dir);
            if (isset($result['error'])) { $error_upload = $result['error']; } else { $gambar = $result['success']; }
        }
        if (empty($error_upload)) {
            $query = "INSERT INTO berita (judul, isi, gambar) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "sss", $judul, $isi, $gambar);
            if (mysqli_stmt_execute($stmt)) { header("Location: admin.php?status=berita_ditambah#berita-tab"); exit(); } 
            else { $error_upload = "Gagal menyimpan berita: " . mysqli_error($koneksi); }
            mysqli_stmt_close($stmt);
        }
    }

    if (isset($_GET['hapus_berita'])) {
        $id = bersihkan_input($_GET['hapus_berita']);
        $gambar = ambil_satu_kolom('berita', 'gambar', 'id_berita', $id, $koneksi);
        if ($gambar && file_exists("uploads/" . $gambar)) { unlink("uploads/" . $gambar); }
        $query = "DELETE FROM berita WHERE id_berita = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: admin.php?status=berita_dihapus#berita-tab");
        exit();
    }
    
    // ===================================================================
    // --- PROSES CRUD PRODUK ---
    // ===================================================================
    if (isset($_POST['tambah_produk'])) {
        $nama_produk = bersihkan_input($_POST['nama_produk']);
        $deskripsi = bersihkan_input($_POST['deskripsi']);
        $harga = (int)bersihkan_input($_POST['harga']);
        $stok = (int)bersihkan_input($_POST['stok']);
        $gambar = '';
        if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] == 0) {
            $target_dir = "uploads/produk/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $result = upload_gambar($_FILES['gambar_produk'], $target_dir);
            if (isset($result['error'])) { $error_upload = "Produk: " . $result['error']; } else { $gambar = $result['success']; }
        }
        if (empty($error_upload)) {
            $query = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "ssiis", $nama_produk, $deskripsi, $harga, $stok, $gambar);
            if (mysqli_stmt_execute($stmt)) { header("Location: admin.php?status=produk_ditambah#produk-tab"); exit(); } 
            else { $error_upload = "Gagal menyimpan produk: " . mysqli_error($koneksi); }
            mysqli_stmt_close($stmt);
        }
    }

    if (isset($_POST['update_produk'])) {
        $id_produk = (int)bersihkan_input($_POST['id_produk']);
        $nama_produk = bersihkan_input($_POST['nama_produk']);
        $deskripsi = bersihkan_input($_POST['deskripsi']);
        $harga = (int)bersihkan_input($_POST['harga']);
        $stok = (int)bersihkan_input($_POST['stok']);
        $gambar_lama = bersihkan_input($_POST['gambar_lama']);
        $gambar = $gambar_lama;
        if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] == 0) {
            $target_dir = "uploads/produk/";
            $result = upload_gambar($_FILES['gambar_produk'], $target_dir);
            if (isset($result['error'])) { $error_upload = "Produk: " . $result['error']; } 
            else { $gambar = $result['success']; if ($gambar_lama && file_exists($target_dir . $gambar_lama)) { unlink($target_dir . $gambar_lama); } }
        }
        if (empty($error_upload)) {
            $query = "UPDATE produk SET nama_produk=?, deskripsi=?, harga=?, stok=?, gambar=? WHERE id_produk=?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "ssiisi", $nama_produk, $deskripsi, $harga, $stok, $gambar, $id_produk);
            if (mysqli_stmt_execute($stmt)) { header("Location: admin.php?status=produk_diupdate#produk-tab"); exit(); } 
            else { $error_upload = "Gagal mengupdate produk: " . mysqli_error($koneksi); }
            mysqli_stmt_close($stmt);
        }
    }
    
    if (isset($_GET['hapus_produk'])) {
        $id = bersihkan_input($_GET['hapus_produk']);
        $gambar = ambil_satu_kolom('produk', 'gambar', 'id_produk', $id, $koneksi);
        if ($gambar && file_exists("uploads/produk/" . $gambar)) { unlink("uploads/produk/" . $gambar); }
        $query = "DELETE FROM produk WHERE id_produk = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: admin.php?status=produk_dihapus#produk-tab");
        exit();
    }
    
    // Ambil data untuk form edit
    $produk_diedit = null;
    if (isset($_GET['edit_produk'])) {
        $id_produk_edit = bersihkan_input($_GET['edit_produk']);
        $query = "SELECT * FROM produk WHERE id_produk = ?";
        $stmt = mysqli_prepare($koneksi, $query); mysqli_stmt_bind_param($stmt, "i", $id_produk_edit); mysqli_stmt_execute($stmt);
        $produk_diedit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    }
    
    $berita_diedit = null; 
    if (isset($_GET['edit_berita'])) {
        $id_berita_edit = bersihkan_input($_GET['edit_berita']);
        $query = "SELECT * FROM berita WHERE id_berita = ?";
        $stmt = mysqli_prepare($koneksi, $query); mysqli_stmt_bind_param($stmt, "i", $id_berita_edit); mysqli_stmt_execute($stmt);
        $berita_diedit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    }
    
    // Ambil semua data setelah proses
    $berita = ambil_semua_berita($koneksi);
    $produk = ambil_semua_produk($koneksi);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Petani Maju</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2e7d32; --primary-dark: #1b5e20; --primary-light: #4caf50;
            --secondary: #0288d1; --light: #f8f9fa; --dark: #343a40;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 8px 25px rgba(0,0,0,0.1);
        }
        body { background-color: #f4f7f6; padding-top: 20px; font-family: 'Segoe UI', Tahoma, Verdana, sans-serif; }
        .admin-container { max-width: 1200px; margin: 0 auto; }
        .admin-header { background: linear-gradient(135deg, var(--primary-dark), var(--primary)); color: white; padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; box-shadow: var(--shadow-lg); }
        .admin-title { font-size: 1.6rem; font-weight: 600; }
        .btn-logout { background: rgba(255, 255, 255, 0.2); border: none; border-radius: 50px; transition: all 0.3s ease; }
        .btn-logout:hover { background: rgba(255, 255, 255, 0.3); transform: translateY(-2px); }
        .panel-title { position: relative; padding-bottom: 12px; margin-bottom: 25px; color: var(--primary-dark); font-weight: 600; font-size: 1.4rem; }
        .panel-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 50px; height: 3px; background: var(--primary); border-radius: 2px; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25); }
        .table-hover tbody tr:hover { background-color: #f1f8e9; }
        .news-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .login-container { max-width: 450px; }
        .nav-tabs .nav-link { color: var(--primary-dark); }
        .nav-tabs .nav-link.active { color: var(--dark); background-color: #fff; border-color: #dee2e6 #dee2e6 #fff; font-weight: 600;}
        .tab-content { background-color: #fff; padding: 25px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 0.375rem 0.375rem; box-shadow: var(--shadow); }
        .stat-card { background-color: var(--light); padding: 20px; border-radius: 10px; display: flex; align-items: center; gap: 20px; border-left: 5px solid; transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
        .stat-card.produk { border-color: var(--primary); }
        .stat-card.berita { border-color: var(--secondary); }
        .stat-card .icon { font-size: 2.5rem; padding: 15px; border-radius: 50%; color: white; }
        .stat-card.produk .icon { background-color: var(--primary); }
        .stat-card.berita .icon { background-color: var(--secondary); }
        .stat-card .info .stat-number { font-size: 2rem; font-weight: 700; }
        .stat-card .info .stat-label { color: #6c757d; }
        .stok-habis { color: #dc3545; font-weight: bold; }
        .stok-tersedia { color: #198754; }
    </style>
</head>
<body class="admin-container">
    <?php if(!isset($_SESSION['admin_id'])): ?>
        <div class="login-container mx-auto mt-5">
            <div class="p-4 bg-white rounded shadow-sm">
                <div class="text-center mb-4">
                    <h2 class="panel-title" style="font-size:1.6rem;"><i class="fas fa-tractor me-2"></i>Petani Maju</h2>
                    <p class="text-muted">Silakan login untuk mengakses Admin Panel</p>
                </div>
                 <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                <form method="POST">
                    <div class="mb-3"><label for="username" class="form-label">Username</label><input type="text" class="form-control" id="username" name="username" required></div>
                    <div class="mb-3"><label for="password" class="form-label">Password</label><input type="password" class="form-control" id="password" name="password" required></div>
                    <button type="submit" name="login" class="btn btn-primary w-100 py-2 mt-2"><i class="fas fa-sign-in-alt me-2"></i> Login</button>
                </form>
            </div>
        </div>
    <?php else: 
        // Logika untuk menentukan Tab yang Aktif
        $tab_produk_aktif = isset($_GET['edit_produk']) || isset($_POST['tambah_produk']) || isset($_POST['update_produk']);
        $tab_berita_aktif = isset($_GET['edit_berita']) || isset($_POST['tambah_berita']) || isset($_POST['update_berita']);

        $dashboard_active = ''; $produk_active = ''; $berita_active = '';

        if ($tab_produk_aktif) { $produk_active = 'active'; } 
        elseif ($tab_berita_aktif) { $berita_active = 'active'; } 
        else { $dashboard_active = 'active'; }
    ?>
        <div class="admin-header">
            <div class="admin-title"><i class="fas fa-user-shield me-2"></i><span>Admin Panel</span></div>
            <a href="?logout=1" class="btn btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <?php if($success_msg): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?= htmlspecialchars($success_msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if($error_upload): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?= htmlspecialchars($error_upload) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

        <ul class="nav nav-tabs" id="adminTab" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link <?= $dashboard_active ?>" id="dashboard-tab-button" data-bs-toggle="tab" data-bs-target="#dashboard-tab" type="button" role="tab">Dashboard</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link <?= $produk_active ?>" id="produk-tab-button" data-bs-toggle="tab" data-bs-target="#produk-tab" type="button" role="tab">Manajemen Produk</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link <?= $berita_active ?>" id="berita-tab-button" data-bs-toggle="tab" data-bs-target="#berita-tab" type="button" role="tab">Manajemen Berita</button></li>
        </ul>

        <div class="tab-content" id="adminTabContent">
            <!-- ======================= TAB DASHBOARD ======================= -->
            <div class="tab-pane fade show <?= $dashboard_active ?>" id="dashboard-tab" role="tabpanel">
                <h3 class="panel-title">Dashboard</h3>
                <p class="mb-4 text-muted">Selamat datang, <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>.</p>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0"><div class="stat-card produk"><div class="icon"><i class="fas fa-leaf"></i></div><div class="info"><div class="stat-number"><?= $total_produk ?></div><div class="stat-label">Total Produk</div></div></div></div>
                    <div class="col-md-6"><div class="stat-card berita"><div class="icon"><i class="fas fa-newspaper"></i></div><div class="info"><div class="stat-number"><?= $total_berita ?></div><div class="stat-label">Total Berita</div></div></div></div>
                </div>
                <hr>
                <h4 class="mt-4 mb-3">Aktivitas Terbaru</h4>
                <div class="row">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h5 class="mb-3 small text-muted"><i class="fas fa-shopping-basket text-success me-2"></i>Produk Terbaru</h5>
                        <ul class="list-group list-group-flush">
                            <?php if(empty($recent_produk)): ?><li class="list-group-item">Belum ada produk.</li><?php else: ?>
                                <?php foreach($recent_produk as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if($item['gambar']): ?><img src="uploads/produk/<?= $item['gambar'] ?>" class="news-img me-2" alt="..."><?php endif; ?>
                                        <strong><?= htmlspecialchars($item['nama_produk']) ?></strong>
                                    </div>
                                    <span class="badge bg-success rounded-pill">Stok: <?= $item['stok'] ?></span>
                                </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <h5 class="mb-3 small text-muted"><i class="fas fa-bullhorn text-info me-2"></i>Berita Terbaru</h5>
                        <ul class="list-group list-group-flush">
                            <?php if(empty($recent_berita)): ?><li class="list-group-item">Belum ada berita.</li><?php else: ?>
                                <?php foreach($recent_berita as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong><?= htmlspecialchars($item['judul']) ?></strong>
                                    <span class="badge bg-secondary rounded-pill"><?= date('d M Y', strtotime($item['tanggal'])) ?></span>
                                </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ======================= TAB PRODUK ======================= -->
            <div class="tab-pane fade show <?= $produk_active ?>" id="produk-tab" role="tabpanel">
                <h3 class="panel-title"><?= $produk_diedit ? 'Edit Produk' : 'Tambah Produk Baru' ?></h3>
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($produk_diedit): ?><input type="hidden" name="id_produk" value="<?= $produk_diedit['id_produk'] ?>"><input type="hidden" name="gambar_lama" value="<?= $produk_diedit['gambar'] ?>"><?php endif; ?>
                    <div class="mb-3"><label for="nama_produk" class="form-label">Nama Produk</label><input type="text" class="form-control" id="nama_produk" name="nama_produk" required value="<?= htmlspecialchars($produk_diedit['nama_produk'] ?? '') ?>"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="harga" class="form-label">Harga (Rp)</label><input type="number" class="form-control" id="harga" name="harga" required value="<?= htmlspecialchars($produk_diedit['harga'] ?? '') ?>"><small class="form-text text-muted">Masukkan angka saja.</small></div>
                        <div class="col-md-6 mb-3"><label for="stok" class="form-label">Stok</label><input type="number" class="form-control" id="stok" name="stok" required value="<?= htmlspecialchars($produk_diedit['stok'] ?? '0') ?>"></div>
                    </div>
                    <div class="mb-3"><label for="gambar_produk" class="form-label">Gambar Produk <?= $produk_diedit ? '(Ganti, Opsional)' : '' ?></label><input class="form-control" type="file" id="gambar_produk" name="gambar_produk" accept="image/*"><?php if ($produk_diedit && $produk_diedit['gambar']): ?><small class="text-muted">Gambar saat ini: <?= htmlspecialchars($produk_diedit['gambar']) ?></small><?php endif; ?></div>
                    <div class="mb-3"><label for="deskripsi_produk" class="form-label">Deskripsi</label><textarea class="form-control" id="deskripsi_produk" name="deskripsi" rows="4" required><?= htmlspecialchars($produk_diedit['deskripsi'] ?? '') ?></textarea></div>
                    <?php if ($produk_diedit): ?><button type="submit" name="update_produk" class="btn btn-primary"><i class="fas fa-save me-2"></i> Simpan</button><a href="admin.php" class="btn btn-secondary">Batal</a><?php else: ?><button type="submit" name="tambah_produk" class="btn btn-success"><i class="fas fa-plus me-2"></i> Tambah</button><?php endif; ?>
                </form>
                <hr class="my-5">
                <div id="daftar-produk"><h4 class="panel-title">Daftar Produk</h4><?php if(empty($produk)): ?><div class="alert alert-info">Belum ada produk.</div><?php else: ?><div class="table-responsive"><table class="table table-hover"><thead><tr><th>Gambar</th><th>Nama</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead><tbody><?php foreach($produk as $item): ?><tr><td><?php if($item['gambar']): ?><img src="uploads/produk/<?= $item['gambar'] ?>" class="news-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>"><?php else: ?><div class="bg-light d-flex align-items-center justify-content-center news-img"><i class="fas fa-image text-muted"></i></div><?php endif; ?></td><td class="align-middle"><?= htmlspecialchars($item['nama_produk']) ?></td><td class="align-middle">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td><td class="align-middle"><span class="<?= $item['stok'] > 0 ? 'stok-tersedia' : 'stok-habis'; ?>"><?= $item['stok'] > 0 ? $item['stok'] : 'Habis'; ?></span></td><td class="align-middle"><a href="?edit_produk=<?= $item['id_produk'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a><a href="?hapus_produk=<?= $item['id_produk'] ?>" class="btn btn-danger btn-sm ms-1" onclick="return confirm('Yakin ingin menghapus produk ini?')"><i class="fas fa-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div>
            </div>

            <!-- ======================= TAB BERITA ======================= -->
            <div class="tab-pane fade show <?= $berita_active ?>" id="berita-tab" role="tabpanel">
                <h3 class="panel-title"><?= $berita_diedit ? 'Edit Berita' : 'Tambah Berita Baru' ?></h3>
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($berita_diedit): ?><input type="hidden" name="id_berita" value="<?= $berita_diedit['id_berita'] ?>"><input type="hidden" name="gambar_lama" value="<?= $berita_diedit['gambar'] ?>"><?php endif; ?>
                    <div class="mb-3"><label for="judul_berita" class="form-label">Judul Berita</label><input type="text" class="form-control" id="judul_berita" name="judul" required value="<?= htmlspecialchars($berita_diedit['judul'] ?? '') ?>"></div>
                    <div class="mb-3"><label for="gambar_berita" class="form-label">Gambar Berita <?= $berita_diedit ? '(Ganti, Opsional)' : '' ?></label><input class="form-control" type="file" id="gambar_berita" name="gambar_berita" accept="image/*"><?php if ($berita_diedit && $berita_diedit['gambar']): ?><small class="text-muted">Gambar saat ini: <?= htmlspecialchars($berita_diedit['gambar']) ?></small><?php endif; ?></div>
                    <div class="mb-3"><label for="isi_berita" class="form-label">Isi Berita</label><textarea class="form-control" id="isi_berita" name="isi" rows="5" required><?= htmlspecialchars($berita_diedit['isi'] ?? '') ?></textarea></div>
                    <?php if ($berita_diedit): ?><button type="submit" name="update_berita" class="btn btn-primary"><i class="fas fa-save me-2"></i> Simpan</button><a href="admin.php#berita-tab" class="btn btn-secondary">Batal</a><?php else: ?><button type="submit" name="tambah_berita" class="btn btn-success"><i class="fas fa-plus me-2"></i> Tambah</button><?php endif; ?>
                </form>
                <hr class="my-5">
                <div id="daftar-berita"><h4 class="panel-title">Daftar Berita</h4><?php if(empty($berita)): ?><div class="alert alert-info">Belum ada berita.</div><?php else: ?><div class="table-responsive"><table class="table table-hover"><thead><tr><th>Gambar</th><th>Judul</th><th>Tanggal</th><th>Aksi</th></tr></thead><tbody><?php foreach($berita as $item): ?><tr><td><?php if($item['gambar']): ?><img src="uploads/<?= $item['gambar'] ?>" class="news-img" alt="<?= htmlspecialchars($item['judul']) ?>"><?php else: ?><div class="bg-light d-flex align-items-center justify-content-center news-img"><i class="fas fa-image text-muted"></i></div><?php endif; ?></td><td class="align-middle"><strong><?= htmlspecialchars($item['judul']) ?></strong><p class="small text-muted mb-0"><?= htmlspecialchars(substr($item['isi'], 0, 100)) ?>...</p></td><td class="align-middle small text-muted"><?= date('d M Y', strtotime($item['tanggal'])) ?></td><td class="align-middle"><a href="?edit_berita=<?= $item['id_berita'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a><a href="?hapus_berita=<?= $item['id_berita'] ?>" class="btn btn-danger btn-sm ms-1" onclick="return confirm('Yakin ingin menghapus berita ini?')"><i class="fas fa-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div>
            </div>
        </div>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>