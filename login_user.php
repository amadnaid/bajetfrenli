<?php
require 'config.php';

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($conn,
"SELECT * FROM users WHERE email='$email'");

$user = mysqli_fetch_assoc($query);

if($user){

    if(password_verify($password, $user['password'])){

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = 'user';

        header("Location: index.php");
        exit();

    }
}

echo "Email atau password salah";
?>