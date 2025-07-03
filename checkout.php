<?php
require_once 'includes/koneksi.php';
require_once 'includes/functions.php';
require_once 'includes/config_midtrans.php';

if (!isset($_GET['id_produk']) || empty($_GET['id_produk'])) {
    header("Location: produk.php");
    exit();
}

$id_produk = (int)$_GET['id_produk'];
$query = "SELECT * FROM produk WHERE id_produk = ? AND stok > 0";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan atau stok habis!'); window.location.href='produk.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- URL JavaScript Midtrans. Gunakan 'app.midtrans.com' untuk production -->
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="<?= MIDTRANS_CLIENT_KEY ?>"></script>
    <title>Checkout - <?= htmlspecialchars($produk['nama_produk']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>:root{--primary:#2e7d32;--secondary:#ff9800;--shadow:0 4px 12px rgba(0,0,0,0.08);} body{background-color:#f8f9fa;} .checkout-card{background:white;border-radius:15px;box-shadow:var(--shadow);}</style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="checkout-card">
                    <div class="card-header bg-primary text-white p-4">
                        <h2 class="h3 mb-0"><i class="fas fa-shopping-cart me-2"></i>Formulir Checkout</h2>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <img src="uploads/produk/<?= htmlspecialchars($produk['gambar']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
                            </div>
                            <div class="col-md-9">
                                <h3><?= htmlspecialchars($produk['nama_produk']) ?></h3>
                                <p class="text-muted d-none d-md-block"><?= htmlspecialchars($produk['deskripsi']) ?></p>
                                <h4 class="text-primary fw-bold">Rp <span id="harga-produk"><?= number_format($produk['harga'], 0, ',', '.') ?></span></h4>
                            </div>
                        </div>

                        <form id="payment-form">
                            <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
                            <input type="hidden" name="harga_satuan" id="harga-satuan" value="<?= $produk['harga'] ?>">

                            <h5 class="mb-3 border-bottom pb-2">Detail Pemesan</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_pemesan" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_pemesan" name="nama_pemesan" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No. Handphone (WA Aktif)</label>
                                <input type="tel" class="form-control" id="no_hp" name="no_hp" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Lengkap Pengiriman</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                            </div>
                            <div class="row align-items-end bg-light p-3 rounded">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="jumlah" class="form-label">Jumlah</label>
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" value="1" min="1" max="<?= $produk['stok'] ?>" required>
                                </div>
                                <div class="col-md-8 text-md-end">
                                    <h4 class="mb-0">Total Harga: <span class="text-primary fw-bold" id="total-harga">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></span></h4>
                                </div>
                            </div>
                            <button type="submit" id="pay-button" class="btn btn-success w-100 fw-bold py-2 mt-4">
                                <i class="fas fa-shield-alt me-2"></i> Lanjutkan ke Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $('#jumlah').on('input', function() {
            let jumlah = parseInt($(this).val());
            let harga = parseInt($('#harga-satuan').val());
            let stok = parseInt($(this).attr('max'));
            
            if (jumlah > stok) {
                alert('Jumlah melebihi stok yang tersedia (' + stok + ')');
                $(this).val(stok);
                jumlah = stok;
            }
            if(jumlah < 1) {
                $(this).val(1);
                jumlah = 1;
            }

            let total = jumlah * harga;
            $('#total-harga').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
        });

        $('#payment-form').on('submit', function (event) {
            event.preventDefault();
            $('#pay-button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

            $.ajax({
                url: 'midtrans_handler.php',
                method: 'POST',
                data: $(this).serialize(),
                cache: false,
                success: function(data) {
                    try {
                        var result = JSON.parse(data);
                        if (result.status === 'success') {
                            snap.pay(result.snap_token, {
                                onSuccess: function(result){
                                    window.location.href = 'finish.php?order_id=' + result.order_id + '&status=success';
                                },
                                onPending: function(result){
                                    window.location.href = 'finish.php?order_id=' + result.order_id + '&status=pending';
                                },
                                onError: function(result){
                                    window.location.href = 'finish.php?order_id=' + result.order_id + '&status=error';
                                },
                                onClose: function(){
                                    alert('Anda menutup popup tanpa menyelesaikan pembayaran.');
                                    $('#pay-button').prop('disabled', false).html('<i class="fas fa-shield-alt me-2"></i> Lanjutkan ke Pembayaran');
                                }
                            });
                        } else {
                            alert('Gagal: ' + result.message);
                            $('#pay-button').prop('disabled', false).html('<i class="fas fa-shield-alt me-2"></i> Lanjutkan ke Pembayaran');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan parsing data. Cek console log.');
                        console.log(data); // Untuk debugging
                        $('#pay-button').prop('disabled', false).html('<i class="fas fa-shield-alt me-2"></i> Lanjutkan ke Pembayaran');
                    }
                },
                error: function(xhr, status, error) {
                    alert("Terjadi kesalahan AJAX: " + status + " " + error);
                    $('#pay-button').prop('disabled', false).html('<i class="fas fa-shield-alt me-2"></i> Lanjutkan ke Pembayaran');
                }
            });
        });
    </script>
</body>
</html>