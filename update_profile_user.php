<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id      = (int)$_SESSION['user_id'];
$nama         = mysqli_real_escape_string($conn, trim($_POST['nama']));
$email        = mysqli_real_escape_string($conn, trim($_POST['email']));
$password_lama = $_POST['password_lama'];
$password_baru = trim($_POST['password_baru'] ?? '');
$password_konfirm = trim($_POST['password_konfirm'] ?? '');

// Ambil data user saat ini untuk verifikasi password
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
$user      = mysqli_fetch_assoc($userQuery);

// Verifikasi password lama
if (!password_verify($password_lama, $user['password'])) {
    header("Location: profile_user.php?error=" . urlencode("Password saat ini salah."));
    exit;
}

// Cek duplikat email (kecuali email sendiri)
$cekEmail = mysqli_query($conn,
    "SELECT user_id FROM users WHERE email = '$email' AND user_id != $user_id"
);
if (mysqli_num_rows($cekEmail) > 0) {
    header("Location: profile_user.php?error=" . urlencode("Email sudah digunakan akun lain."));
    exit;
}

// Handle password baru
$passwordField = '';
if ($password_baru !== '') {
    if ($password_baru !== $password_konfirm) {
        header("Location: profile_user.php?error=" . urlencode("Konfirmasi password baru tidak cocok."));
        exit;
    }
    if (strlen($password_baru) < 6) {
        header("Location: profile_user.php?error=" . urlencode("Password baru minimal 6 karakter."));
        exit;
    }
    $hashed       = password_hash($password_baru, PASSWORD_DEFAULT);
    $passwordField = ", password = '$hashed'";
}

// Handle upload foto profil
$fotoField = '';
if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
    $ext     = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ext, $allowed)) {
        if (!is_dir('uploads/avatars')) {
            mkdir('uploads/avatars', 0755, true);
        }

        // Hapus foto lama kalau ada
        if ($user['foto_profil'] && file_exists('uploads/avatars/' . $user['foto_profil'])) {
            unlink('uploads/avatars/' . $user['foto_profil']);
        }

        $filename  = 'user_' . $user_id . '_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['foto_profil']['tmp_name'], 'uploads/avatars/' . $filename);
        $fotoField = ", foto_profil = '$filename'";
    }
}

// Update database
mysqli_query($conn,
    "UPDATE users
     SET nama = '$nama', email = '$email' $passwordField $fotoField
     WHERE user_id = $user_id"
);

// Update session
$_SESSION['nama'] = $nama;

header("Location: profile_user.php?updated=1");
exit;