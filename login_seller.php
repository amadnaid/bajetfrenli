<?php
require 'config.php';

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($conn,
"SELECT * FROM sellers WHERE email='$email'");

$seller = mysqli_fetch_assoc($query);

if($seller){

    if(password_verify($password, $seller['password'])){

        $_SESSION['seller_id'] = $seller['seller_id'];
        $_SESSION['nama_usaha'] = $seller['nama_usaha'];
        $_SESSION['role'] = 'seller';

        header("Location: index.php");
        exit();

    }
}

echo "Email atau password salah";
?>