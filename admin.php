<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// Proses login (DITINGKATKAN DENGAN PREPARED STATEMENT)
$error = '';
if (isset($_POST['login'])) {
    $username = bersihkan_input($_POST['username']);
    $password = bersihkan_input($_POST['password']);
    
    $query = "SELECT * FROM admin WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $admin = mysqli_fetch_assoc($result);
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = $admin['id_admin'];
        header("Location: admin.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
    mysqli_stmt_close($stmt);
}

// Proses logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin']);
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
    // --- PROSES CRUD PRODUK ---
    // ===================================================================

    // Proses tambah produk
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
            if (isset($result['error'])) {
                $error_upload = "Produk: " . $result['error'];
            } else {
                $gambar = $result['success'];
            }
        }

        if (empty($error_upload)) {
            $query = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "ssiis", $nama_produk, $deskripsi, $harga, $stok, $gambar);
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
        $nama_produk = bersihkan_input($_POST['nama_produk']);
        $deskripsi = bersihkan_input($_POST['deskripsi']);
        $harga = (int)bersihkan_input($_POST['harga']);
        $stok = (int)bersihkan_input($_POST['stok']);
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
                if ($gambar_lama && file_exists($target_dir . $gambar_lama)) {
                    unlink($target_dir . $gambar_lama);
                }
            }
        }

        if (empty($error_upload)) {
            $query = "UPDATE produk SET nama_produk=?, deskripsi=?, harga=?, stok=?, gambar=? WHERE id_produk=?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "ssiisi", $nama_produk, $deskripsi, $harga, $stok, $gambar, $id_produk);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: admin.php?status=produk_diupdate#panel-produk");
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
        $gambar = ambil_satu_kolom('produk', 'gambar', 'id_produk', $id, $koneksi);
        if ($gambar && file_exists("uploads/produk/" . $gambar)) {
            unlink("uploads/produk/" . $gambar);
        }
        $query = "DELETE FROM produk WHERE id_produk = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: admin.php?status=produk_dihapus#panel-produk");
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

    // ===================================================================
    // --- PROSES CRUD BERITA ---
    // ===================================================================
    if (isset($_POST['tambah_berita'])) {
        // Logika tambah berita (sudah ada)
    }
    if (isset($_GET['hapus_berita'])) {
        // Logika hapus berita (sudah ada, perlu sedikit perbaikan)
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
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
            --light: #f8f9fa;
            --dark: #343a40;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        html { scroll-behavior: smooth; }
        body { background-color: #f0f7f0; min-height: 100vh; padding-top: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-container { max-width: 1200px; margin: 0 auto; }
        .admin-header { background: linear-gradient(135deg, var(--primary-dark), var(--primary)); color: white; padding: 20px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; box-shadow: var(--shadow); }
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
        .prod-img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; }
        .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px; }
        .login-container { max-width: 500px; width: 100%; margin: 50px auto; }
        .login-card { background: white; border-radius: 15px; box-shadow: var(--shadow); overflow: hidden; }
        .login-header { background: linear-gradient(135deg, var(--primary-dark), var(--primary)); color: white; text-align: center; padding: 25px 20px; }
    </style>
</head>
<body class="admin-container">
    <?php if(!isset($_SESSION['admin'])): ?>
        <!-- Login Form -->
        <div class="login-container">
            <div class="login-card">
                 <div class="login-header">
                    <h2 class="h3 fw-bold"><i class="fas fa-tractor me-2"></i>Petani Maju</h2>
                    <p class="mb-0">Admin Panel</p>
                </div>
                <div class="login-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-2x text-primary"></i>
                        <h3 class="panel-title mt-2">Login Admin</h3>
                    </div>
                    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required placeholder="Masukkan username">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password">
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100 fw-bold py-2"><i class="fas fa-sign-in-alt me-2"></i> Login</button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Admin Panel -->
        <header class="admin-header">
            <div class="admin-title"><i class="fas fa-user-shield"></i><span>Admin Panel - Petani Maju</span></div>
            <a href="?logout=1" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </header>
        
        <?php if($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success_msg ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        <?php if($error_upload): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error_upload ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <!-- =================================================================== -->
        <!-- FORM PRODUK (TAMBAH/EDIT) -->
        <!-- =================================================================== -->
        <section id="form-produk" class="admin-panel">
            <h3 class="panel-title"><?= $produk_diedit ? 'Edit Produk' : 'Tambah Produk Baru' ?></h3>
            <form method="POST" enctype="multipart/form-data" action="admin.php#form-produk">
                <?php if ($produk_diedit): ?>
                    <input type="hidden" name="id_produk" value="<?= $produk_diedit['id_produk'] ?>">
                    <input type="hidden" name="gambar_lama" value="<?= $produk_diedit['gambar'] ?>">
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="nama_produk" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="nama_produk" name="nama_produk" required value="<?= htmlspecialchars($produk_diedit['nama_produk'] ?? '') ?>" placeholder="e.g., Bibit Jagung Hibrida B-52">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="deskripsi_produk" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi_produk" name="deskripsi" rows="3" required placeholder="Deskripsi singkat mengenai keunggulan produk"><?= htmlspecialchars($produk_diedit['deskripsi'] ?? '') ?></textarea>
                </div>
                <div class="row">
                     <div class="col-md-4 mb-3">
                        <label for="harga" class="form-label">Harga (Rp)</label>
                        <input type="number" class="form-control" id="harga" name="harga" required value="<?= $produk_diedit['harga'] ?? '' ?>" placeholder="e.g., 95000">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" required value="<?= $produk_diedit['stok'] ?? '0' ?>" placeholder="e.g., 100">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="gambar_produk" class="form-label">Gambar Produk <?= $produk_diedit ? '(Ganti)' : '' ?></label>
                        <input class="form-control" type="file" id="gambar_produk" name="gambar_produk" accept="image/*">
                         <?php if ($produk_diedit && $produk_diedit['gambar']): ?>
                            <small class="text-muted">Gambar saat ini: <a href="uploads/produk/<?= $produk_diedit['gambar']?>" target="_blank"><?= $produk_diedit['gambar'] ?></a></small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($produk_diedit): ?>
                    <button type="submit" name="update_produk" class="btn btn-primary"><i class="fas fa-save me-2"></i> Simpan Perubahan</button>
                    <a href="admin.php#panel-produk" class="btn btn-secondary">Batal Edit</a>
                <?php else: ?>
                    <button type="submit" name="tambah_produk" class="btn btn-success"><i class="fas fa-plus me-2"></i> Tambah Produk</button>
                <?php endif; ?>
            </form>
        </section>

        <!-- =================================================================== -->
        <!-- DAFTAR PRODUK -->
        <!-- =================================================================== -->
        <section id="panel-produk" class="admin-panel">
            <h3 class="panel-title">Daftar Produk</h3>
             <?php if(empty($produk)): ?>
                <div class="alert alert-info">Belum ada produk. Silakan tambahkan melalui form di atas.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($produk as $item): ?>
                                <tr>
                                    <td>
                                        <?php if($item['gambar']): ?>
                                            <img src="uploads/produk/<?= $item['gambar'] ?>" class="prod-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center prod-img"><i class="fas fa-leaf text-muted"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($item['nama_produk']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars(substr($item['deskripsi'], 0, 50)) ?>...</small>
                                    </td>
                                    <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                    <td><?= $item['stok'] ?></td>
                                    <td>
                                        <a href="admin.php?edit_produk=<?= $item['id_produk'] ?>#form-produk" class="btn btn-warning btn-sm btn-action"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="admin.php?hapus_produk=<?= $item['id_produk'] ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Yakin ingin menghapus produk ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
        
        <hr class="my-5">

        <!-- Panel Berita (Tidak ada perubahan signifikan, hanya penyesuaian) -->
        <section id="panel-berita" class="admin-panel">
            <h3 class="panel-title">Manajemen Berita</h3>
            <!-- ... Form dan Tabel Berita Anda bisa ditempatkan di sini ... -->
        </section>

    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Validasi Sisi Klien (Client-side)
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('gambar_produk');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const maxSize = 2 * 1024 * 1024; // 2MB
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

                    if (file.size > maxSize) {
                        alert('Ukuran file terlalu besar. Maksimal 2MB.');
                        this.value = ''; // Reset input
                    }
                    if (!allowedTypes.includes(file.type)) {
                        alert('Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
                        this.value = ''; // Reset input
                    }
                }
            });
        }
    });
    </script>
</body>
</html>