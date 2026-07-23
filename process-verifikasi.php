<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pendaftaran_id = $_POST['pendaftaran_id'];
    $status_baru    = $_POST['status'];
    $catatan_admin  = mysqli_real_escape_string($koneksi, $_POST['catatan_admin']);

    $query = "UPDATE berkas_pendaftaran 
              SET status_verifikasi = '$status_baru', 
                  catatan_admin = '$catatan_admin' 
              WHERE id = '$pendaftaran_id'";

    if (mysqli_query($koneksi, $query)) {
        header("Location: admin-dashboard.php?msg=updated");
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>