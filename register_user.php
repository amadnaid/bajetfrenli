<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE email='$email'"
    );

    if(mysqli_num_rows($cek) > 0){
        $error = "Email sudah digunakan.";
    } else {

        mysqli_query($conn,"
            INSERT INTO users
            (nama,email,password)
            VALUES
            ('$nama','$email','$password')
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
<title>Daftar Pengguna</title>
<link rel="stylesheet" href="register_user.css">
</head>
<body>

<div class="login-container">

    <h1>Daftar Pengguna</h1>

    <?php
    if(isset($error)){
        echo "<p style='color:red'>$error</p>";
    }
    ?>

    <form method="POST">

        <input
            type="text"
            name="nama"
            placeholder="Nama Lengkap"
            required>

        <input
            type="email"
            name="email"
            placeholder="Email"
            required>

        <input
            type="password"
            name="password"
            placeholder="Password"
            required>

        <button type="submit">
            Daftar
        </button>

    </form>

    <p>
        Sudah punya akun?
        <a href="login.php">Login</a>
    </p>

</div>

</body>
</html>