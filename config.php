<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "surabaya_life_budget");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}