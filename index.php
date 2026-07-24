<?php
include 'koneksi.php';

// 1. Tentukan Kuota Maksimal Pendaftaran
$total_kuota = 50;

// 2. Hitung jumlah pendaftar (user dengan role 'user') dari database
$query_user = mysqli_query($koneksi, "SELECT COUNT(*) as total_pendaftar FROM users WHERE role = 'user'");
$data_user  = mysqli_fetch_assoc($query_user);

$pendaftar_masuk = $data_user['total_pendaftar'] ?? 0;

// 3. Hitung Sisa Kuota (Pastikan tidak minus)
$sisa_kuota = $total_kuota - $pendaftar_masuk;
if ($sisa_kuota < 0) {
    $sisa_kuota = 0;
}

// 4. Hitung Persentase Terisi untuk Progress Bar
$persen_terisi = ($pendaftar_masuk / $total_kuota) * 100;
if ($persen_terisi > 100) {
    $persen_terisi = 100;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerimaan Beasiswa Walikota Medan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons untuk pemanis icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .carousel-item img {
            height: 450px;
            object-fit: cover;
            filter: brightness(0.70);
        }
    </style>
</head>
<body class="bg-light">

    <!-- NAVBAR -->
   <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">BEASISWA PEMKO MEDAN</a>
            <div>
                <a href="login.php" class="btn btn-outline-light me-2">Masuk</a>
                
                <!-- Tombol Daftar Otomatis Nonaktif Jika Kuota Habis -->
                <?php if ($sisa_kuota > 0): ?>
                    <a href="register.php" class="btn btn-warning fw-bold">Daftar</a>
                <?php else: ?>
                    <button class="btn btn-secondary fw-bold" disabled>Pendaftaran Ditutup</button>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- CAROUSEL 4 SLIDE GAMBAR LANDMARK MEDAN -->
    <div id="beasiswaCarousel" class="carousel slide shadow-sm mb-4" data-bs-ride="carousel" data-bs-interval="3500">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#beasiswaCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#beasiswaCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#beasiswaCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#beasiswaCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1596402184320-417e7178b2cd?q=80&w=1200&auto=format&fit=crop" class="d-block w-100" alt="Istana Maimun Medan">
                <div class="carousel-caption d-none d-md-block">
                    <h3 class="fw-bold text-white">Istana Maimun - Warisan & Sejarah Kota Medan</h3>
                    <p>Dukungan penuh Pemerintah Kota Medan untuk pendidikan generasi muda Berkah.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?q=80&w=1200&auto=format&fit=crop" class="d-block w-100" alt="Masjid Raya Medan">
                <div class="carousel-caption d-none d-md-block">
                    <h3 class="fw-bold text-white">Masjid Raya Al-Mashun Medan</h3>
                    <p>Membangun SDM Kota Medan yang cerdas, berdaya saing, dan berakhlak mulia.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1200&auto=format&fit=crop" class="d-block w-100" alt="Mahasiswa & Pendidikan Medan">
                <div class="carousel-caption d-none d-md-block">
                    <h3 class="fw-bold text-white">Medan Kota Pelajar & Perguruan Tinggi</h3>
                    <p>Terbuka bagi seluruh mahasiswa berprestasi dan kurang mampu di Kota Medan.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1562774053-701939374585?q=80&w=1200&auto=format&fit=crop" class="d-block w-100" alt="Generasi Masa Depan Medan">
                <div class="carousel-caption d-none d-md-block">
                    <h3 class="fw-bold text-white">Menuju Medan Berkembang & Berkelanjutan</h3>
                    <p>Wujudkan impian kuliahmu melalui Program Beasiswa Walikota Medan.</p>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#beasiswaCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sebelumnya</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#beasiswaCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Selanjutnya</span>
        </button>
    </div>

    <!-- MAIN CONTENT -->
    <div class="container my-5 text-center">

        <!-- CARD INFORMASI KUOTA -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="card-body">
                        <h5 class="text-secondary fw-semibold mb-3">Informasi Kuota Pendaftaran</h5>
                        
                        <div class="d-flex justify-content-around align-items-center my-3">
                            <div>
                                <small class="text-muted d-block mb-1">Total Kuota</small>
                                <span class="badge bg-primary fs-5 px-3 py-2"><?= $total_kuota; ?> Pendaftar</span>
                            </div>
                            <div class="border-start border-end px-4">
                                <small class="text-muted d-block mb-1">Pendaftar Masuk</small>
                                <span class="badge bg-info text-dark fs-5 px-3 py-2"><?= $pendaftar_masuk; ?> Akun</span>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1">Sisa Kuota Penerimaan</small>
                                <span class="badge bg-success fs-5 px-3 py-2"><?= $sisa_kuota; ?> / <?= $total_kuota; ?></span>
                            </div>
                        </div>

                        <!-- Progress Bar Visualisasi Kuota -->
                        <div class="mt-4 px-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="fw-bold text-muted">Kapasitas Terisi</small>
                                <small class="fw-bold text-primary"><?= round($persen_terisi, 1); ?>%</small>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                                     role="progressbar" 
                                     style="width: <?= $persen_terisi; ?>%;" 
                                     aria-valuenow="<?= $persen_terisi; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BANNER UTAMA -->
        <div class="p-5 mb-4 bg-white rounded-3 shadow-sm">
            <h1 class="display-5 fw-bold text-primary">Program Beasiswa Pemko Medan</h1>
            <p class="col-md-8 fs-4 mx-auto text-secondary">
                Sistem Informasi Pendaftaran dan Verifikasi Beasiswa Online. Silakan daftarkan akun Anda dan lengkapi 13 berkas persyaratan yang diperlukan.
            </p>
            <?php if ($sisa_kuota > 0): ?>
                <a href="register.php" class="btn btn-primary btn-lg px-4 mt-3">Daftar Sekarang</a>
            <?php else: ?>
                <button class="btn btn-danger btn-lg px-4 mt-3" disabled>Kuota Pendaftaran Penuh</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
