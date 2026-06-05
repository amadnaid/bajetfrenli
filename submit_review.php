<?php
require 'config.php';

if (!isset($_SESSION['seller_id'])) {
    header("Location: login.php");
    exit;
}

$seller_id   = (int)$_SESSION['seller_id'];
$category_id = (int)$_POST['category_id'];
$nama_tempat = mysqli_real_escape_string($conn, trim($_POST['nama_tempat']));
$deskripsi   = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
$alamat      = mysqli_real_escape_string($conn, trim($_POST['alamat']));
$harga_min   = (int)$_POST['harga_min'];
$harga_max   = (int)$_POST['harga_max'];
$jam_buka    = mysqli_real_escape_string($conn, trim($_POST['jam_buka'] ?? ''));
$kontak      = mysqli_real_escape_string($conn, trim($_POST['kontak'] ?? ''));

// Upload foto utama
$foto_utama = '';
if (isset($_FILES['foto_utama']) && $_FILES['foto_utama']['error'] === UPLOAD_ERR_OK) {
    $ext     = strtolower(pathinfo($_FILES['foto_utama']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ext, $allowed)) {
        $filename = time() . '_' . $seller_id . '.' . $ext;
        $dest     = 'uploads/businesses/' . $filename;

        if (!is_dir('uploads/businesses')) {
            mkdir('uploads/businesses', 0755, true);
        }

        if (move_uploaded_file($_FILES['foto_utama']['tmp_name'], $dest)) {
            $foto_utama = $filename;
        }
    }
}

mysqli_query($conn,
    "INSERT INTO businesses
     (seller_id, category_id, nama_tempat, deskripsi, alamat,
      harga_min, harga_max, jam_buka, kontak, foto_utama)
     VALUES
     ($seller_id, $category_id, '$nama_tempat', '$deskripsi', '$alamat',
      $harga_min, $harga_max, '$jam_buka', '$kontak', '$foto_utama')"
);

$new_id = mysqli_insert_id($conn);

header("Location: index.php?business_added=1&place=$new_id#budget");
exit;