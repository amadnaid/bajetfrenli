<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id     = (int)$_SESSION['user_id'];
$business_id = (int)$_POST['business_id'];
$caption     = mysqli_real_escape_string($conn, trim($_POST['caption'] ?? ''));

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $ext     = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ext, $allowed)) {
        $filename = time() . '_' . $user_id . '.' . $ext;
        $dest     = 'uploads/documentation/' . $filename;

        if (!is_dir('uploads/documentation')) {
            mkdir('uploads/documentation', 0755, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
            mysqli_query($conn,
                "INSERT INTO business_photos (business_id, user_id, foto, caption)
                 VALUES ($business_id, $user_id, '$filename', '$caption')"
            );
        }
    }
}

header("Location: index.php?photo_added=1&place=$business_id#budget");
exit;