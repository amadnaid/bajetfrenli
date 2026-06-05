<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama_pemilik = mysqli_real_escape_string(
        $conn,
        $_POST['nama_pemilik']
    );

    $nama_usaha = mysqli_real_escape_string(
        $conn,
        $_POST['nama_usaha']
    );

    $email = mysqli_real_escape_string(
        $conn,
        $_POST['email']
    );

    $telepon = mysqli_real_escape_string(
        $conn,
        $_POST['telepon']
    );

    $alamat = mysqli_real_escape_string(
        $conn,
        $_POST['alamat']
    );

    $password = password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );

    $cek = mysqli_query(
        $conn,
        "SELECT * FROM sellers WHERE email='$email'"
    );

    if(mysqli_num_rows($cek) > 0){

        $error = "Email sudah digunakan.";

    } else {

        mysqli_query($conn,"
            INSERT INTO sellers
            (
                nama_pemilik,
                nama_usaha,
                email,
                password,
                nomor_telepon,
                alamat
            )
            VALUES
            (
                '$nama_pemilik',
                '$nama_usaha',
                '$email',
                '$password',
                '$telepon',
                '$alamat'
            )
        ");

        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Penjual</title>
<link rel="stylesheet" href="register_seller.css">
</head>
<body>

<div class="login-container">

    <h1>Daftarkan Usaha</h1>

    <?php
    if(isset($error)){
        echo "<p style='color:red'>$error</p>";
    }
    ?>

    <form method="POST">

        <input
            type="text"
            name="nama_pemilik"
            placeholder="Nama Pemilik"
            required>

        <input
            type="text"
            name="nama_usaha"
            placeholder="Nama Usaha"
            required>

        <input
            type="email"
            name="email"
            placeholder="Email"
            required>

        <input
            type="text"
            name="telepon"
            placeholder="Nomor Telepon"
            required>

        <textarea
            name="alamat"
            placeholder="Alamat Usaha"
            required></textarea>

        <input
            type="password"
            name="password"
            placeholder="Password"
            required>

        <button type="submit">
            Daftarkan Usaha
        </button>

    </form>

    <p>
        Sudah punya akun?
        <a href="login.php">Login</a>
    </p>

</div>

</body>
</html>