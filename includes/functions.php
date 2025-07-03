<?php
// Fungsi untuk upload gambar
function upload_gambar($file, $target_dir) {
    $gambar = '';
    
    if ($file['name']) {
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Validasi file gambar
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            return ['error' => 'File bukan gambar'];
        }
        
        // Format yang diperbolehkan
        $allowed = ['jpg', 'jpeg', 'png'];
        if (!in_array($imageFileType, $allowed)) {
            return ['error' => 'Format file tidak diizinkan. Gunakan JPG, JPEG, atau PNG'];
        }
        
        // Coba upload
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $gambar = basename($file["name"]);
        } else {
            return ['error' => 'Terjadi kesalahan saat mengupload gambar'];
        }
    }
    return ['success' => $gambar];
}

// Fungsi untuk mengambil semua berita
function ambil_semua_berita($koneksi) {
    $query = "SELECT * FROM berita ORDER BY tanggal DESC";
    $result = mysqli_query($koneksi, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Tambahkan fungsi ini di includes/functions.php

function ambil_semua_produk($koneksi) {
    $produk = [];
    $query = "SELECT * FROM produk ORDER BY created_at DESC";
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $produk[] = $row;
        }
    }
    return $produk;
}

// Fungsi ini juga akan berguna
function ambil_satu_kolom($tabel, $kolom, $where_kolom, $where_nilai, $koneksi) {
    $query = "SELECT $kolom FROM $tabel WHERE $where_kolom = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $where_nilai);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $data ? $data[$kolom] : null;
}
?>