<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>:root{--success:#2e7d32;--warning:#ffc107;--danger:#dc3545;} body{background-color:#f8f9fa;}</style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card text-center" style="width: 25rem;">
        <?php
            $status = $_GET['status'] ?? 'error';
            $order_id = $_GET['order_id'] ?? 'N/A';
            
            if ($status == 'success') {
                echo '<div class="card-header bg-success text-white"><h2><i class="fas fa-check-circle"></i> Pembayaran Berhasil</h2></div>';
                echo '<div class="card-body"><p>Terima kasih! Pesanan Anda dengan ID <strong>' . htmlspecialchars($order_id) . '</strong> telah kami terima dan akan segera diproses.</p>';
            } elseif ($status == 'pending') {
                echo '<div class="card-header bg-warning text-dark"><h2><i class="fas fa-hourglass-half"></i> Pembayaran Tertunda</h2></div>';
                echo '<div class="card-body"><p>Pesanan Anda dengan ID <strong>' . htmlspecialchars($order_id) . '</strong> menunggu pembayaran. Segera selesaikan pembayaran Anda.</p>';
            } else {
                echo '<div class="card-header bg-danger text-white"><h2><i class="fas fa-times-circle"></i> Pembayaran Gagal</h2></div>';
                echo '<div class="card-body"><p>Maaf, terjadi masalah dengan pembayaran untuk pesanan ID <strong>' . htmlspecialchars($order_id) . '</strong>. Silakan coba lagi atau hubungi kami.</p>';
            }
        ?>
        <div class="card-footer">
            <a href="produk.php" class="btn btn-primary">Belanja Lagi</a>
        </div>
    </div>
</body>
</html>