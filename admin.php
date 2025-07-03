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
            $_SESSION['admin'] = $admin['id_admin'];
            header("Location: admin.php"); // Redirect setelah login berhasil
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

// Jika tidak login, hentikan eksekusi script lebih lanjut
if (!isset($_SESSION['admin'])) {
    // Biarkan script lanjut untuk menampilkan form login di HTML
} else {

    // ===================================================================
    // --- PROSES CRUD BERITA ---
    // ===================================================================

    // Proses tambah berita
    if (isset($_POST['tambah_berita'])) {
        $judul = bersihkan_input($_POST['judul']);
        $isi = bersihkan_input($_POST['isi']);
        $gambar = '';
        
        if (isset($_FILES['gambar_berita']) && $_FILES['gambar_berita']['error'] == 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $result = upload_gambar($_FILES['gambar_berita'], $target_dir);
            if (isset($result['error'])) {
                $error_upload = $result['error'];
            } else {
                $gambar = $result['success'];
            }
        }
        
        if (empty($error_upload)) {
            $query = "INSERT INTO berita (judul, isi, gambar) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "sss", $judul, $isi, $gambar);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Berita berhasil ditambahkan!";
            } else {
                $error_upload = "Gagal menyimpan ke database: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Proses update berita
    if (isset($_POST['update_berita'])) {
        $id_berita = bersihkan_input($_POST['id_berita']);
        $judul = bersihkan_input($_POST['judul']);
        $isi = bersihkan_input($_POST['isi']);
        $gambar_lama = bersihkan_input($_POST['gambar_lama']);
        $gambar = $gambar_lama;

        if (isset($_FILES['gambar_berita']) && $_FILES['gambar_berita']['error'] == 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $result = upload_gambar($_FILES['gambar_berita'], $target_dir);
            if (isset($result['error'])) {
                $error_upload = "Berita: " . $result['error'];
            } else {
                $gambar = $result['success'];
                // Hapus gambar lama jika upload baru berhasil
                if ($gambar_lama && file_exists($target_dir . $gambar_lama)) {
                    unlink($target_dir . $gambar_lama);
                }
            }
        }

        if (empty($error_upload)) {
            $query = "UPDATE berita SET judul=?, isi=?, gambar=? WHERE id_berita=?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $judul, $isi, $gambar, $id_berita);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: admin.php?status=berita_diupdate#daftar-berita");
                exit();
            } else {
                $error_upload = "Gagal mengupdate berita: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
    }


    // Proses hapus berita
    if (isset($_GET['hapus_berita'])) {
        $id = bersihkan_input($_GET['hapus_berita']);
        // Hapus file gambar dari server
        $gambar = ambil_satu_kolom('berita', 'gambar', 'id_berita', $id, $koneksi);
        if ($gambar && file_exists("uploads/" . $gambar)) {
            unlink("uploads/" . $gambar);
        }
        $query = "DELETE FROM berita WHERE id_berita = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: admin.php?status=berita_dihapus#daftar-berita");
        exit();
    }
    
    // Ambil data untuk form edit berita
    $berita_diedit = null;
    if (isset($_GET['edit_berita'])) {
        $id_berita_edit = bersihkan_input($_GET['edit_berita']);
        $query = "SELECT * FROM berita WHERE id_berita = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_berita_edit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $berita_diedit = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }


    // ===================================================================
    // --- PROSES CRUD PRODUK ---
    // ===================================================================

    // Proses tambah produk
    if (isset($_POST['tambah_produk'])) {
        $nama = bersihkan_input($_POST['nama']);
        $kategori = bersihkan_input($_POST['kategori']);
        $deskripsi = bersihkan_input($_POST['deskripsi']);
        $harga = bersihkan_input($_POST['harga']);
        $gambar = '';
        
        if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] == 0) {
            $target_dir = "uploads/produk/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $result = upload_gambar($_FILES['gambar_produk'], $target_dir);
            if (isset($result['error'])) {
                $error_upload = "Produk: " . $result['error'];
            } else {
                $gambar = $result['success'];
            }
        }

        if (empty($error_upload)) {
            $query = "INSERT INTO produk (nama, kategori, deskripsi, harga, gambar) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "sssss", $nama, $kategori, $deskripsi, $harga, $gambar);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Produk berhasil ditambahkan!";
            } else {
                $error_upload = "Gagal menyimpan produk: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Proses update produk
    if (isset($_POST['update_produk'])) {
        $id_produk = bersihkan_input($_POST['id_produk']);
        $nama = bersihkan_input($_POST['nama']);
        $kategori = bersihkan_input($_POST['kategori']);
        $deskripsi = bersihkan_input($_POST['deskripsi']);
        $harga = bersihkan_input($_POST['harga']);
        $gambar_lama = bersihkan_input($_POST['gambar_lama']);
        $gambar = $gambar_lama;

        if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] == 0) {
            $target_dir = "uploads/produk/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $result = upload_gambar($_FILES['gambar_produk'], $target_dir);
            if (isset($result['error'])) {
                $error_upload = "Produk: " . $result['error'];
            } else {
                $gambar = $result['success'];
                // Hapus gambar lama jika upload berhasil
                if ($gambar_lama && file_exists($target_dir . $gambar_lama)) {
                    unlink($target_dir . $gambar_lama);
                }
            }
        }

        if (empty($error_upload)) {
            $query = "UPDATE produk SET nama=?, kategori=?, deskripsi=?, harga=?, gambar=? WHERE id_produk=?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "sssssi", $nama, $kategori, $deskripsi, $harga, $gambar, $id_produk);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: admin.php?status=produk_diupdate");
                exit();
            } else {
                $error_upload = "Gagal mengupdate produk: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Proses hapus produk
    if (isset($_GET['hapus_produk'])) {
        $id = bersihkan_input($_GET['hapus_produk']);
        // Hapus file gambar dari server
        $gambar = ambil_satu_kolom('produk', 'gambar', 'id_produk', $id, $koneksi);
        if ($gambar && file_exists("uploads/produk/" . $gambar)) {
            unlink("uploads/produk/" . $gambar);
        }
        $query = "DELETE FROM produk WHERE id_produk = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: admin.php?status=produk_dihapus");
        exit();
    }
    
    // Ambil data untuk form edit produk
    $produk_diedit = null;
    if (isset($_GET['edit_produk'])) {
        $id_produk_edit = bersihkan_input($_GET['edit_produk']);
        $query = "SELECT * FROM produk WHERE id_produk = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_produk_edit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $produk_diedit = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }


    // Ambil semua data setelah proses
    $berita = ambil_semua_berita($koneksi);
    $produk = ambil_semua_produk($koneksi); // Asumsi fungsi ini ada di functions.php
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
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
            --primary-light: #4caf50;
            --light: #f8f9fa;
            --dark: #343a40;
            --text: #333;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        html { scroll-behavior: smooth; } /* Untuk navigasi ke anchor link */
        body { background-color: #f0f7f0; min-height: 100vh; padding-top: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-container { max-width: 1200px; margin: 0 auto; }
        .admin-header { background: linear-gradient(135deg, var(--primary-dark), var(--primary)); color: white; padding: 20px; border-radius: 10px 10px 0 0; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; box-shadow: var(--shadow); }
        .admin-title { display: flex; align-items: center; gap: 12px; font-size: 1.6rem; font-weight: 600; }
        .admin-title i { font-size: 1.8rem; }
        .btn-logout { background: rgba(255, 255, 255, 0.2); border: none; padding: 8px 16px; border-radius: 50px; color: white; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; gap: 8px; }
        .btn-logout:hover { background: rgba(255, 255, 255, 0.3); transform: translateY(-2px); }
        .admin-panel { background: white; border-radius: 10px; box-shadow: var(--shadow); padding: 25px; margin-bottom: 25px; }
        .panel-title { position: relative; padding-bottom: 12px; margin-bottom: 20px; color: var(--primary-dark); font-weight: 600; }
        .panel-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 50px; height: 3px; background: var(--primary); border-radius: 2px; }
        .form-label { font-weight: 500; color: var(--dark); margin-bottom: 6px; }
        .form-control, .form-select { border-radius: 8px; padding: 10px 15px; border: 2px solid #e0e0e0; transition: all 0.3s; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25); }
        .btn-primary, .btn-success { background: var(--primary); border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; transition: all 0.3s; }
        .btn-primary:hover, .btn-success:hover { background: var(--primary-dark); transform: translateY(-3px); }
        .table { margin: 0; }
        .table thead { background-color: #e8f5e9; }
        .table th { font-weight: 600; color: var(--primary-dark); padding: 12px 15px; }
        .table td { padding: 12px 15px; vertical-align: middle; }
        .news-img { width: 80px; height: 60px; object-fit: cover; border-radius: 6px; }
        .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px; }
        .login-container { max-width: 500px; width: 100%; margin: 50px auto; }
        .login-card { background: white; border-radius: 15px; box-shadow: var(--shadow); overflow: hidden; }
        .login-header { background: linear-gradient(135deg, var(--primary-dark), var(--primary)); color: white; text-align: center; padding: 25px 20px; }
        .login-title { font-size: 1.6rem; font-weight: 600; margin-bottom: 8px; }
        .login-subtitle { opacity: 0.9; font-size: 0.95rem; }
        .login-body { padding: 25px; }
        .login-logo { font-size: 2.2rem; color: var(--primary); margin-bottom: 15px; text-align: center; }
        .btn-login { width: 100%; padding: 10px; font-weight: 500; }
        .form-group { margin-bottom: 20px; }
    </style>
</head>
<body class="admin-container">
    <?php if(!isset($_SESSION['admin'])): ?>
        <!-- Login Form -->
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h2 class="login-title"><i class="fas fa-tractor me-2"></i>Petani Maju</h2>
                    <p class="login-subtitle">Admin Panel</p>
                </div>
                <div class="login-body">
                    <div class="text-center mb-4">
                        <div class="login-logo"><i class="fas fa-lock"></i></div>
                        <h3 class="panel-title">Login Admin</h3>
                    </div>
                    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required placeholder="Masukkan username">
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password">
                        </div>
                        <button type="submit" name="login" class="btn btn-primary btn-login"><i class="fas fa-sign-in-alt me-2"></i> Login</button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Admin Panel -->
        <div class="admin-header">
            <div class="admin-title"><i class="fas fa-user-shield"></i><span>Admin Panel - Petani Maju</span></div>
            <a href="?logout=1" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        
        <?php if($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success_msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        <?php if($error_upload): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error_upload) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <!-- =================================================================== -->
        <!-- FORM PRODUK (TAMBAH/EDIT) -->
        <!-- =================================================================== -->
        <div class="admin-panel" id="panel-produk">
            <h3 class="panel-title"><?= $produk_diedit ? 'Edit Produk' : 'Tambah Produk Baru' ?></h3>
            <form method="POST" enctype="multipart/form-data" action="admin.php#panel-produk">
                <?php if ($produk_diedit): ?>
                    <input type="hidden" name="id_produk" value="<?= $produk_diedit['id_produk'] ?>">
                    <input type="hidden" name="gambar_lama" value="<?= $produk_diedit['gambar'] ?>">
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($produk_diedit['nama'] ?? '') ?>" placeholder="e.g., Bibit Jagung Hibrida">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="kategori" name="kategori" required value="<?= htmlspecialchars($produk_diedit['kategori'] ?? '') ?>" placeholder="e.g., Bibit Unggul">
                    </div>
                </div>
                <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="text" class="form-control" id="harga" name="harga" required value="<?= htmlspecialchars($produk_diedit['harga'] ?? '') ?>" placeholder="e.g., Rp 95.000 /kg">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gambar_produk" class="form-label">Gambar Produk <?= $produk_diedit ? '(Ganti, Opsional)' : '(Opsional)' ?></label>
                        <input class="form-control" type="file" id="gambar_produk" name="gambar_produk" accept="image/*">
                         <?php if ($produk_diedit && $produk_diedit['gambar']): ?>
                            <small class="text-muted">Gambar saat ini: <?= htmlspecialchars($produk_diedit['gambar']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="deskripsi_produk" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi_produk" name="deskripsi" rows="4" required placeholder="Deskripsi singkat produk"><?= htmlspecialchars($produk_diedit['deskripsi'] ?? '') ?></textarea>
                </div>
                
                <?php if ($produk_diedit): ?>
                    <button type="submit" name="update_produk" class="btn btn-primary"><i class="fas fa-save me-2"></i> Simpan Perubahan</button>
                    <a href="admin.php#daftar-produk" class="btn btn-secondary">Batal Edit</a>
                <?php else: ?>
                    <button type="submit" name="tambah_produk" class="btn btn-success"><i class="fas fa-plus me-2"></i> Tambah Produk</button>
                <?php endif; ?>
            </form>
        </div>

        <!-- DAFTAR PRODUK -->
        <div class="admin-panel" id="daftar-produk">
            <h3 class="panel-title">Daftar Produk</h3>
             <?php if(empty($produk)): ?>
                <div class="alert alert-info">Belum ada produk. Silakan tambahkan.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($produk as $item): ?>
                                <tr>
                                    <td>
                                        <?php if($item['gambar']): ?>
                                            <img src="uploads/produk/<?= $item['gambar'] ?>" class="news-img" alt="<?= htmlspecialchars($item['nama']) ?>">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center news-img"><i class="fas fa-image text-muted"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($item['nama']) ?></td>
                                    <td><?= htmlspecialchars($item['kategori']) ?></td>
                                    <td><?= htmlspecialchars($item['harga']) ?></td>
                                    <td>
                                        <a href="?edit_produk=<?= $item['id_produk'] ?>#panel-produk" class="btn btn-warning btn-sm btn-action"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="?hapus_produk=<?= $item['id_produk'] ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Yakin ingin menghapus produk ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <hr class="my-5">

        <!-- =================================================================== -->
        <!-- FORM BERITA (TAMBAH/EDIT) -->
        <!-- =================================================================== -->
        <div class="admin-panel" id="panel-berita">
            <h3 class="panel-title"><?= $berita_diedit ? 'Edit Berita' : 'Tambah Berita Baru' ?></h3>
            <form method="POST" enctype="multipart/form-data" action="admin.php#panel-berita">
                 <?php if ($berita_diedit): ?>
                    <input type="hidden" name="id_berita" value="<?= $berita_diedit['id_berita'] ?>">
                    <input type="hidden" name="gambar_lama" value="<?= $berita_diedit['gambar'] ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label for="judul_berita" class="form-label">Judul Berita</label>
                    <input type="text" class="form-control" id="judul_berita" name="judul" required value="<?= htmlspecialchars($berita_diedit['judul'] ?? '') ?>" placeholder="Judul berita atau artikel">
                </div>
                <div class="mb-3">
                    <label for="gambar_berita" class="form-label">Gambar Berita <?= $berita_diedit ? '(Ganti, Opsional)' : '(Opsional)' ?></label>
                    <input class="form-control" type="file" id="gambar_berita" name="gambar_berita" accept="image/*">
                    <?php if ($berita_diedit && $berita_diedit['gambar']): ?>
                        <small class="text-muted">Gambar saat ini: <?= htmlspecialchars($berita_diedit['gambar']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="isi_berita" class="form-label">Isi Berita</label>
                    <textarea class="form-control" id="isi_berita" name="isi" rows="5" required placeholder="Tuliskan isi berita di sini..."><?= htmlspecialchars($berita_diedit['isi'] ?? '') ?></textarea>
                </div>
                
                <?php if ($berita_diedit): ?>
                    <button type="submit" name="update_berita" class="btn btn-primary"><i class="fas fa-save me-2"></i> Simpan Perubahan</button>
                    <a href="admin.php#daftar-berita" class="btn btn-secondary">Batal Edit</a>
                <?php else: ?>
                    <button type="submit" name="tambah_berita" class="btn btn-success"><i class="fas fa-plus me-2"></i> Tambah Berita</button>
                <?php endif; ?>
            </form>
        </div>

        <!-- =================================================================== -->
        <!-- DAFTAR BERITA -->
        <!-- =================================================================== -->
        <div class="admin-panel" id="daftar-berita">
            <h3 class="panel-title">Daftar Berita</h3>
            <?php if(empty($berita)): ?>
                <div class="alert alert-info">Belum ada berita. Silakan tambahkan.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Judul</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($berita as $item): ?>
                            <tr>
                                <td>
                                    <?php if($item['gambar']): ?>
                                        <img src="uploads/<?= $item['gambar'] ?>" class="news-img" alt="<?= htmlspecialchars($item['judul']) ?>">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center news-img"><i class="fas fa-image text-muted"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($item['judul']) ?></strong>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars(substr($item['isi'], 0, 100)) ?>...</p>
                                </td>
                                <td><?= date('d M Y, H:i', strtotime($item['tanggal'])) ?></td>
                                <td>
                                    <a href="?edit_berita=<?= $item['id_berita'] ?>#panel-berita" class="btn btn-warning btn-sm btn-action"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="?hapus_berita=<?= $item['id_berita'] ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Yakin ingin menghapus berita ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>