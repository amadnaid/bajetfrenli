<?php

require 'config.php';

if(!isset($_SESSION['seller_id'])){
    exit;
}

$seller_id = $_SESSION['seller_id'];

$category_id = (int)$_POST['category_id'];

$nama_tempat =
mysqli_real_escape_string(
    $conn,
    $_POST['nama_tempat']
);

$deskripsi =
mysqli_real_escape_string(
    $conn,
    $_POST['deskripsi']
);

$alamat =
mysqli_real_escape_string(
    $conn,
    $_POST['alamat']
);

$harga_min = (int)$_POST['harga_min'];
$harga_max = (int)$_POST['harga_max'];

$jam_buka =
mysqli_real_escape_string(
    $conn,
    $_POST['jam_buka']
);

$kontak =
mysqli_real_escape_string(
    $conn,
    $_POST['kontak']
);

$foto = '';

if(isset($_FILES['foto_utama'])){

    $ext =
    pathinfo(
        $_FILES['foto_utama']['name'],
        PATHINFO_EXTENSION
    );

    $foto =
    time()
    .'_'
    .uniqid()
    .'.'
    .$ext;

    move_uploaded_file(

        $_FILES['foto_utama']['tmp_name'],

        "uploads/businesses/"
        .$foto

    );

}

mysqli_query(

    $conn,

    "INSERT INTO businesses
    (
        seller_id,
        category_id,
        nama_tempat,
        deskripsi,
        alamat,
        harga_min,
        harga_max,
        jam_buka,
        kontak,
        foto_utama
    )

    VALUES
    (
        '$seller_id',
        '$category_id',
        '$nama_tempat',
        '$deskripsi',
        '$alamat',
        '$harga_min',
        '$harga_max',
        '$jam_buka',
        '$kontak',
        '$foto'
    )"

);

header(
    "Location:index.php?business=success"
);

exit;