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
    $admin = mysqli_fetch_assoc($result);
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = $admin['id_admin'];
    } else {
        $error = "Username atau password salah!";
    }
}

// Proses logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    header("Location: admin.php");
    exit();
}

// Proses tambah berita
if (isset($_POST['tambah_berita']) && isset($_SESSION['admin'])) {
    $judul = bersihkan_input($_POST['judul']);
    $isi = bersihkan_input($_POST['isi']);
    $gambar = '';
    
    if ($_FILES['gambar']['name']) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $result = upload_gambar($_FILES['gambar'], $target_dir);
        if (isset($result['error'])) {
            $error_upload = $result['error'];
        } else {
            $gambar = $result['success'];
        }
    }
    
    if (!isset($error_upload)) {
        $query = "INSERT INTO berita (judul, isi, gambar) VALUES ('$judul', '$isi', '$gambar')";
        mysqli_query($koneksi, $query);
        header("Location: admin.php?success=1");
        exit();
    }
}

// Proses hapus berita
if (isset($_GET['hapus']) && isset($_SESSION['admin'])) {
    $id = bersihkan_input($_GET['hapus']);
    $query = "DELETE FROM berita WHERE id_berita = $id";
    mysqli_query($koneksi, $query);
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
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-panel {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 50px auto;
            max-width: 800px;
        }
        
        .admin-container {
            min-height: 100vh;
            background-color: #f8f9fa;
            padding-top: 50px;
        }
    </style>
</head>
<body class="admin-container">
    <div class="container">
        <?php if(!isset($_SESSION['admin'])): ?>
            <!-- Form Login Admin -->
            <div class="admin-panel">
                <h2 class="section-title">Login Admin</h2>
                
                <?php if($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </button>
                </form>
            </div>
        <?php else: ?>
            <!-- Admin Panel -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-success"><i class="fas fa-tractor me-2"></i>Admin Panel</h1>
                <a href="?logout=1" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
            
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success">Berita berhasil ditambahkan!</div>
            <?php endif; ?>
            
            <?php if(isset($error_upload)): ?>
                <div class="alert alert-danger"><?= $error_upload ?></div>
            <?php endif; ?>
            
            <!-- Form Tambah Berita -->
            <div class="admin-panel mb-5">
                <h2 class="section-title">Tambah Berita Baru</h2>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Berita</label>
                        <input type="text" class="form-control" id="judul" name="judul" required>
                    </div>
                    <div class="mb-3">
                        <label for="isi" class="form-label">Isi Berita</label>
                        <textarea class="form-control" id="isi" name="isi" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar (Opsional)</label>
                        <input class="form-control" type="file" id="gambar" name="gambar" accept="image/*">
                    </div>
                    <button type="submit" name="tambah_berita" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Tambah Berita
                    </button>
                </form>
            </div>
            
            <!-- Daftar Berita -->
            <div class="admin-panel">
                <h2 class="section-title">Daftar Berita</h2>
                
                <?php if(empty($berita)): ?>
                    <div class="alert alert-info">Belum ada berita</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-success">
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
                                                <img src="uploads/<?= $item['gambar'] ?>" width="80" class="img-thumbnail">
                                            <?php else: ?>
                                                <div class="bg-secondary d-flex align-items-center justify-content-center" style="width:80px; height:60px;">
                                                    <i class="fas fa-image text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $item['judul'] ?></td>
                                        <td><?= date('d M Y', strtotime($item['tanggal'])) ?></td>
                                        <td>
                                            <a href="?hapus=<?= $item['id_berita'] ?>" class="btn btn-sm btn-danger">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>