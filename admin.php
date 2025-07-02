<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';

// Proses login
$error = '';
if (isset($_POST['login'])) {
    $username = bersihkan_input($_POST['username']);
    $password = bersihkan_input($_POST['password']);
    
    $query = "SELECT * FROM admin WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);
    
    if (!$result) {
        $error = "Error database: " . mysqli_error($koneksi);
    } else {
        $admin = mysqli_fetch_assoc($result);
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['id_admin'];
        } else {
            $error = "Username atau password salah!";
        }
    }
}

// Proses logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    header("Location: index.php");
    exit();
}

// Proses tambah berita
$error_upload = '';
$success_msg = '';

if (isset($_POST['tambah_berita']) && isset($_SESSION['admin'])) {
    $judul = bersihkan_input($_POST['judul']);
    $isi = bersihkan_input($_POST['isi']);
    $gambar = '';
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['name']) {
        $target_dir = "uploads/";
        
        // Buat folder jika belum ada
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $error_upload = "Gagal membuat folder uploads";
            }
        }
        
        // Cek apakah folder ada dan bisa ditulisi
        if (is_dir($target_dir) && is_writable($target_dir)) {
            $result = upload_gambar($_FILES['gambar'], $target_dir);
            
            if (isset($result['error'])) {
                $error_upload = $result['error'];
            } else {
                $gambar = $result['success'];
            }
        } else {
            $error_upload = "Folder uploads tidak ada atau tidak bisa ditulisi";
        }
    }
    
    if (empty($error_upload)) {
        $query = "INSERT INTO berita (judul, isi, gambar) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $judul, $isi, $gambar);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Berita berhasil ditambahkan!";
            } else {
                $error_upload = "Gagal menyimpan ke database: " . mysqli_error($koneksi);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $error_upload = "Error persiapan query: " . mysqli_error($koneksi);
        }
    }
}

// Proses hapus berita
if (isset($_GET['hapus']) && isset($_SESSION['admin'])) {
    $id = bersihkan_input($_GET['hapus']);
    $query = "DELETE FROM berita WHERE id_berita = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    header("Location: admin.php");
    exit();
}

// Ambil semua berita
$berita = ambil_semua_berita($koneksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Website Petani</title>
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

        body {
            background-color: #f0f7f0;
            min-height: 100vh;
            padding-top: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
        }

        .admin-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.6rem;
            font-weight: 600;
        }

        .admin-title i {
            font-size: 1.8rem;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            padding: 8px 16px;
            border-radius: 50px;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .admin-panel {
            background: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 25px;
            margin-bottom: 25px;
        }

        .panel-title {
            position: relative;
            padding-bottom: 12px;
            margin-bottom: 20px;
            color: var(--primary-dark);
            font-weight: 600;
        }

        .panel-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary);
            border-radius: 2px;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 6px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
        }

        .btn-success {
            background: var(--primary);
            border: none;
        }

        .btn-success:hover {
            background: var(--primary-dark);
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 15px 20px;
        }

        .table-title {
            margin: 0;
            font-weight: 600;
            font-size: 1.3rem;
        }

        .table {
            margin: 0;
        }

        .table thead {
            background-color: #e8f5e9;
        }

        .table th {
            font-weight: 600;
            color: var(--primary-dark);
            padding: 12px 15px;
        }

        .table td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        .news-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .login-container {
            max-width: 500px;
            width: 100%;
            margin: 50px auto;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            text-align: center;
            padding: 25px 20px;
        }

        .login-title {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .login-subtitle {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .login-body {
            padding: 25px;
        }

        .login-logo {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 15px;
            text-align: center;
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
        }
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
                        <div class="login-logo">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3 class="panel-title">Login Admin</h3>
                    </div>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required
                                   placeholder="Masukkan username" value="admin">
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required
                                   placeholder="Masukkan password" value="petani123">
                        </div>
                        
                        <button type="submit" name="login" class="btn btn-primary btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i> Login
                        </button>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted">Default login: admin / petani123</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Admin Panel -->
        <div class="admin-header">
            <div class="admin-title">
                <i class="fas fa-tractor"></i>
                <span>Admin Panel - Petani Maju</span>
            </div>
            <a href="?logout=1" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
        
        <?php if($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success_msg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if($error_upload): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error_upload ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Form Tambah Berita -->
        <div class="admin-panel">
            <h3 class="panel-title">Tambah Berita Baru</h3>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Berita</label>
                            <input type="text" class="form-control" id="judul" name="judul" required
                                   placeholder="Masukkan judul berita">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar (Opsional)</label>
                            <input class="form-control" type="file" id="gambar" name="gambar" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB</small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="isi" class="form-label">Isi Berita</label>
                    <textarea class="form-control" id="isi" name="isi" rows="6" required
                              placeholder="Tulis isi berita di sini"></textarea>
                </div>
                
                <button type="submit" name="tambah_berita" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i> Tambah Berita
                </button>
            </form>
        </div>
        
        <!-- Daftar Berita -->
        <div class="admin-panel">
            <h3 class="panel-title">Daftar Berita</h3>
            
            <?php if(empty($berita)): ?>
                <div class="alert alert-info">Belum ada berita. Silakan tambahkan berita baru.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($berita as $item): ?>
                                <tr>
                                    <td>
                                        <?php if($item['gambar']): ?>
                                            <img src="uploads/<?= $item['gambar'] ?>" class="news-img" alt="<?= $item['judul'] ?>">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center news-img">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $item['judul'] ?></td>
                                    <td><?= date('d M Y', strtotime($item['tanggal'])) ?></td>
                                    <td>
                                        <a href="?hapus=<?= $item['id_berita'] ?>" class="btn btn-danger btn-action"
                                           onclick="return confirm('Yakin ingin menghapus berita ini?')">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </a>
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
    <script>
        // Menampilkan pesan error dengan animasi
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
            
            // Validasi form
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const inputs = form.querySelectorAll('input[required], textarea[required]');
                    let valid = true;
                    
                    inputs.forEach(input => {
                        if (!input.value.trim()) {
                            valid = false;
                            input.classList.add('is-invalid');
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });
                    
                    if (!valid) {
                        e.preventDefault();
                        alert('Harap isi semua field yang wajib diisi!');
                    }
                });
            });
            
            // Validasi ukuran file gambar
            const fileInput = document.getElementById('gambar');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        // Ukuran maksimal 2MB
                        const maxSize = 2 * 1024 * 1024; // 2MB
                        
                        if (file.size > maxSize) {
                            alert('Ukuran file terlalu besar. Maksimal 2MB');
                            this.value = ''; // Reset input file
                        }
                        
                        // Validasi tipe file
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('Format file tidak didukung. Gunakan JPG, JPEG, atau PNG');
                            this.value = ''; // Reset input file
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>