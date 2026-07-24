<?php
session_start();
include 'koneksi.php';

// --- CEK KUOTA PENDAFTARAN ---
$total_kuota = 50;

$query_user = mysqli_query($koneksi, "SELECT COUNT(*) as total_pendaftar FROM users WHERE role = 'user'");
$data_user  = mysqli_fetch_assoc($query_user);

$pendaftar_masuk = $data_user['total_pendaftar'] ?? 0;
$sisa_kuota      = $total_kuota - $pendaftar_masuk;

$pesan = "";

// Jika kuota sudah habis, tampilkan peringatan
if ($sisa_kuota <= 0) {
    $pesan = "<div class='alert alert-danger text-center fw-bold mb-3'>Mohon maaf, kuota pendaftaran beasiswa sudah penuh!</div>";
}

// --- PROSES REGISTRASI ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Proteksi ganda di sisi backend: Batalkan proses jika kuota penuh
    if ($sisa_kuota <= 0) {
        echo "<script>
                alert('Pendaftaran gagal! Kuota pendaftaran sudah penuh.');
                window.location='index.php';
              </script>";
        exit();
    }

    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email        = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password     = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $pesan = "<div class='alert alert-danger'>Email sudah terdaftar!</div>";
    } else {
        $query = "INSERT INTO users (nama_lengkap, email, password, role) VALUES ('$nama_lengkap', '$email', '$password', 'user')";
        if (mysqli_query($koneksi, $query)) {
            header("Location: login.php?msg=registered");
            exit();
        } else {
            $pesan = "<div class='alert alert-danger'>Pendaftaran gagal!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun - Beasiswa Walikota Medan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="card-title text-center mb-1 fw-bold text-primary">Registrasi Akun Pendaftar</h4>
                        <p class="text-center text-muted small mb-3">Beasiswa Pemko Medan</p>
                        
                        <!-- Informasi Sisa Kuota Ringkas -->
                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded mb-3 border">
                            <small class="text-secondary fw-semibold">Sisa Kuota saat ini:</small>
                            <span class="badge <?= ($sisa_kuota > 0) ? 'bg-success' : 'bg-danger'; ?> fs-6">
                                <?= max(0, $sisa_kuota); ?> / <?= $total_kuota; ?>
                            </span>
                        </div>

                        <!-- Notifikasi Pesan Error / Kuota Penuh -->
                        <?= $pesan; ?>

                        <!-- FORM REGISTRASI -->
                        <form action="" method="POST">
                            <fieldset <?= ($sisa_kuota <= 0) ? 'disabled' : ''; ?>>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alamat Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                                </div>
                                
                                <?php if ($sisa_kuota > 0): ?>
                                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Daftar Akun</button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary w-100 fw-bold py-2" disabled>Pendaftaran Ditutup</button>
                                <?php endif; ?>
                            </fieldset>
                        </form>

                        <hr class="my-4">
                        
                        <div class="text-center">
                            <?php if ($sisa_kuota <= 0): ?>
                                <a href="index.php" class="btn btn-outline-primary btn-sm w-100 mb-2">&larr; Kembali ke Halaman Utama</a>
                            <?php endif; ?>
                            <p class="mb-0 text-muted">Sudah punya akun? <a href="login.php" class="fw-bold text-decoration-none">Masuk di sini</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
