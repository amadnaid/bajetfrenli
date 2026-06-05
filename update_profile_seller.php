<?php
require 'config.php';

if (!isset($_SESSION['seller_id'])) {
    header("Location: login.php");
    exit;
}

$seller_id        = (int)$_SESSION['seller_id'];
$nama_pemilik     = mysqli_real_escape_string($conn, trim($_POST['nama_pemilik']));
$nama_usaha       = mysqli_real_escape_string($conn, trim($_POST['nama_usaha']));
$email            = mysqli_real_escape_string($conn, trim($_POST['email']));
$nomor_telepon    = mysqli_real_escape_string($conn, trim($_POST['nomor_telepon'] ?? ''));
$alamat           = mysqli_real_escape_string($conn, trim($_POST['alamat'] ?? ''));
$password_lama    = $_POST['password_lama'];
$password_baru    = trim($_POST['password_baru'] ?? '');
$password_konfirm = trim($_POST['password_konfirm'] ?? '');

// Ambil data seller untuk verifikasi
$sellerQuery = mysqli_query($conn, "SELECT * FROM sellers WHERE seller_id = $seller_id");
$seller      = mysqli_fetch_assoc($sellerQuery);

// Verifikasi password lama
if (!password_verify($password_lama, $seller['password'])) {
    header("Location: profile_seller.php?error=" . urlencode("Password saat ini salah."));
    exit;
}

// Cek duplikat email
$cekEmail = mysqli_query($conn,
    "SELECT seller_id FROM sellers WHERE email = '$email' AND seller_id != $seller_id"
);
if (mysqli_num_rows($cekEmail) > 0) {
    header("Location: profile_seller.php?error=" . urlencode("Email sudah digunakan akun lain."));
    exit;
}

// Handle password baru
$passwordField = '';
if ($password_baru !== '') {
    if ($password_baru !== $password_konfirm) {
        header("Location: profile_seller.php?error=" . urlencode("Konfirmasi password baru tidak cocok."));
        exit;
    }
    if (strlen($password_baru) < 6) {
        header("Location: profile_seller.php?error=" . urlencode("Password baru minimal 6 karakter."));
        exit;
    }
    $hashed        = password_hash($password_baru, PASSWORD_DEFAULT);
    $passwordField = ", password = '$hashed'";
}

mysqli_query($conn,
    "UPDATE sellers
     SET nama_pemilik = '$nama_pemilik',
         nama_usaha   = '$nama_usaha',
         email        = '$email',
         nomor_telepon = '$nomor_telepon',
         alamat       = '$alamat'
         $passwordField
     WHERE seller_id = $seller_id"
);

// Update session
$_SESSION['nama_usaha'] = $nama_usaha;

header("Location: profile_seller.php?updated=1");
exit;