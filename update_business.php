<?php
require 'config.php';

if (!isset($_SESSION['seller_id'])) {
    header("Location: login.php");
    exit;
}

$seller_id   = (int)$_SESSION['seller_id'];
$business_id = (int)$_POST['business_id'];

// Pastikan usaha ini benar milik seller yang login
$cekQuery = mysqli_query($conn,
    "SELECT * FROM businesses WHERE business_id = $business_id AND seller_id = $seller_id"
);
if (mysqli_num_rows($cekQuery) === 0) {
    header("Location: profile_seller.php?error=" . urlencode("Akses ditolak."));
    exit;
}
$existing = mysqli_fetch_assoc($cekQuery);

$category_id = (int)$_POST['category_id'];
$nama_tempat = mysqli_real_escape_string($conn, trim($_POST['nama_tempat']));
$deskripsi   = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
$alamat      = mysqli_real_escape_string($conn, trim($_POST['alamat']));
$harga_min   = (int)$_POST['harga_min'];
$harga_max   = (int)$_POST['harga_max'];
$jam_buka    = mysqli_real_escape_string($conn, trim($_POST['jam_buka'] ?? ''));
$kontak      = mysqli_real_escape_string($conn, trim($_POST['kontak'] ?? ''));

// Handle upload foto baru
$fotoField = '';
if (isset($_FILES['foto_utama']) && $_FILES['foto_utama']['error'] === UPLOAD_ERR_OK) {
    $ext     = strtolower(pathinfo($_FILES['foto_utama']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ext, $allowed)) {
        if (!is_dir('uploads/businesses')) {
            mkdir('uploads/businesses', 0755, true);
        }

        // Hapus foto lama
        if ($existing['foto_utama'] && file_exists('uploads/businesses/' . $existing['foto_utama'])) {
            unlink('uploads/businesses/' . $existing['foto_utama']);
        }

        $filename  = 'biz_' . $business_id . '_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['foto_utama']['tmp_name'], 'uploads/businesses/' . $filename);
        $fotoField = ", foto_utama = '$filename'";
    }
}

mysqli_query($conn,
    "UPDATE businesses
     SET category_id  = $category_id,
         nama_tempat  = '$nama_tempat',
         deskripsi    = '$deskripsi',
         alamat       = '$alamat',
         harga_min    = $harga_min,
         harga_max    = $harga_max,
         jam_buka     = '$jam_buka',
         kontak       = '$kontak'
         $fotoField
     WHERE business_id = $business_id AND seller_id = $seller_id"
);

header("Location: edit_business.php?updated=1");
exit;