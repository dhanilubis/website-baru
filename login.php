<?php
session_start();
include 'koneksi.php';

$pesan = "";

if (isset($_GET['msg']) && $_GET['msg'] == 'registered') {
    $pesan = "<div class='alert alert-success'>Pendaftaran berhasil, silakan login!</div>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama_lengkap'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin-dashboard.php");
            } else {
                header("Location: user-dashboard.php");
            }
            exit();
        } else {
            $pesan = "<div class='alert alert-danger'>Password salah!</div>";
        }
    } else {
        $pesan = "<div class='alert alert-danger'>Email tidak terdaftar!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login System - Beasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title text-center mb-4 fw-bold">Login Sistem</h4>
                        <?= $pesan; ?>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold">Masuk</button>
                        </form>
                        <hr>
                        <p class="text-center mb-0">Belum punya akun? <a href="register.php">Daftar Pendaftar</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>