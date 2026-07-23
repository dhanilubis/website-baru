<?php
$host     = "localhost";
$user     = "root";
$password = ""; // Kosongkan jika menggunakan XAMPP default
$database = "sip_beasiswa";

$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>