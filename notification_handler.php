<?php
require_once 'includes/koneksi.php';
require_once 'includes/config_midtrans.php';
require_once 'vendor/autoload.php';

\Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;
\Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
$notif = new \Midtrans\Notification();

$transaction_status = $notif->transaction_status;
$fraud_status = $notif->fraud_status;
$order_id_full = $notif->order_id;

$parts = explode('-', $order_id_full);
if (count($parts) < 2 || !is_numeric($parts[1])) {
    http_response_code(400); // Bad Request
    error_log("Invalid Order ID format: $order_id_full");
    exit();
}
$id_pesanan = (int)$parts[1];

$status_to_update = 'pending';
if ($transaction_status == 'capture' || $transaction_status == 'settlement') {
    if ($fraud_status == 'accept') {
        $status_to_update = 'success';
    }
} else if ($transaction_status == 'cancel' || $transaction_status == 'deny' || $transaction_status == 'expire') {
    $status_to_update = 'failed';
}

update_pesanan($status_to_update, $id_pesanan, $koneksi);

function update_pesanan($status, $id_pesanan, $koneksi) {
    mysqli_begin_transaction($koneksi);
    try {
        $query_info = "SELECT id_produk, jumlah, status FROM pesanan WHERE id_pesanan = ? FOR UPDATE";
        $stmt_info = mysqli_prepare($koneksi, $query_info);
        mysqli_stmt_bind_param($stmt_info, "i", $id_pesanan);
        mysqli_stmt_execute($stmt_info);
        $pesanan = mysqli_stmt_get_result($stmt_info)->fetch_assoc();

        if ($pesanan && $pesanan['status'] == 'pending') {
            $query_update = "UPDATE pesanan SET status = ? WHERE id_pesanan = ?";
            $stmt_update = mysqli_prepare($koneksi, $query_update);
            mysqli_stmt_bind_param($stmt_update, "si", $status, $id_pesanan);
            mysqli_stmt_execute($stmt_update);

            if ($status == 'success') {
                $query_stok = "UPDATE produk SET stok = stok - ? WHERE id_produk = ?";
                $stmt_stok = mysqli_prepare($koneksi, $query_stok);
                mysqli_stmt_bind_param($stmt_stok, "ii", $pesanan['jumlah'], $pesanan['id_produk']);
                mysqli_stmt_execute($stmt_stok);
            }
        }
        mysqli_commit($koneksi);
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        error_log("Database transaction failed for order_id $id_pesanan: " . $e->getMessage());
        http_response_code(500); // Server error
    }
}
?>