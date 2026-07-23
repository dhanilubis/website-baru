<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query   = mysqli_query($koneksi, "SELECT * FROM berkas_pendaftaran WHERE user_id = '$user_id'");
$data    = mysqli_fetch_assoc($query);

$status_verifikasi = $data ? $data['status_verifikasi'] : 'Belum Upload';
$catatan           = $data ? $data['catatan_admin'] : '';

// Mengambil data terisi (jika ada)
$nama_lengkap = $data['nama_lengkap'] ?? $_SESSION['nama'];
$nik          = $data['nik'] ?? '';
$nim          = $data['nim'] ?? '';
$nama_kampus  = $data['nama_kampus'] ?? '';
$hobi         = $data['hobi'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pendaftar - Beasiswa Walikota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Portal Beasiswa</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3">Halo, <strong><?= htmlspecialchars($_SESSION['nama']); ?></strong></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Berkas dan data pendaftaran berhasil disimpan/diperbarui!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Status Banner Dynamic -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Status Pengajuan Berkas</h5>
                <?php if ($status_verifikasi == 'Belum Upload'): ?>
                    <span class="badge bg-secondary fs-6">Belum Mengunggah Berkas</span>
                <?php elseif ($status_verifikasi == 'Menunggu'): ?>
                    <span class="badge bg-warning text-dark fs-6">Sedang Diverifikasi Admin</span>
                <?php elseif ($status_verifikasi == 'Layak'): ?>
                    <span class="badge bg-success fs-6">LAYAK (Berkas Disetujui)</span>
                <?php elseif ($status_verifikasi == 'Tidak Layak'): ?>
                    <span class="badge bg-danger fs-6">TIDAK LAYAK (Ditolak)</span>
                <?php endif; ?>

                <?php if (!empty($catatan)): ?>
                    <div class="mt-3 alert alert-warning mb-0">
                        <strong>Catatan Admin Verifikator:</strong><br>
                        <?= nl2br(htmlspecialchars($catatan)); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Input Data & Upload Berkas -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Formulir Pendaftaran Beasiswa</h5>
                <small class="text-muted">Lengkapi data diri dan unggah 13 berkas persyaratan (Format: PDF / JPG / PNG, Max 2MB/file)</small>
            </div>
            <div class="card-body">
                <form action="process-upload.php" method="POST" enctype="multipart/form-data">
                    
                    <!-- SECTION 1: DATA DIRI PENDAFTAR -->
                    <h6 class="text-primary fw-bold mb-3">A. Data Diri Pendaftar</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($nama_lengkap); ?>" placeholder="Sesuai KTP" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">NIK (Nomor Induk Kependudukan)</label>
                            <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($nik); ?>" placeholder="16 Digit NIK" maxlength="16" pattern="[0-9]{16}" title="NIK harus berjumlah 16 digit angka" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">NIM (Nomor Induk Mahasiswa)</label>
                            <input type="text" name="nim" class="form-control" value="<?= htmlspecialchars($nim); ?>" placeholder="Masukkan NIM" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Nama Kampus / Perguruan Tinggi</label>
                            <input type="text" name="nama_kampus" class="form-control" value="<?= htmlspecialchars($nama_kampus); ?>" placeholder="Contoh: Universitas Indonesia" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Hobi</label>
                            <input type="text" name="hobi" class="form-control" value="<?= htmlspecialchars($hobi); ?>" placeholder="Contoh: Membaca, Olahraga" required>
                        </div>
                    </div>

                    <hr>

                    <!-- SECTION 2: UPLOAD BERKAS PERSYARATAN -->
                    <h6 class="text-primary fw-bold mb-3">B. Upload 13 Berkas Persyaratan</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">1. Surat Permohonan ditujukan kepada Walikota</label>
                            <input class="form-control" type="file" name="surat_walikota" <?= empty($data['surat_walikota']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['surat_walikota'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">2. Scan Asli Biodata Pribadi</label>
                            <input class="form-control" type="file" name="biodata_pribadi" <?= empty($data['biodata_pribadi']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['biodata_pribadi'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">3. Pas Foto Ukuran 3x4</label>
                            <input class="form-control" type="file" name="pas_foto" accept="image/*" <?= empty($data['pas_foto']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['pas_foto'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">4. Scan Asli Kartu Keluarga (KK)</label>
                            <input class="form-control" type="file" name="kk" <?= empty($data['kk']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['kk'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">5. Scan Asli KTP</label>
                            <input class="form-control" type="file" name="ktp" <?= empty($data['ktp']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['ktp'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">6. Scan Asli Kartu Mahasiswa (KTM)</label>
                            <input class="form-control" type="file" name="ktm" <?= empty($data['ktm']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['ktm'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">7. Scan Asli Keterangan Masih Kuliah</label>
                            <input class="form-control" type="file" name="ket_kuliah" <?= empty($data['ket_kuliah']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['ket_kuliah'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">8. Scan Asli DTSEN</label>
                            <input class="form-control" type="file" name="dtsen" <?= empty($data['dtsen']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['dtsen'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">9. Scan Asli Pernyataan Tidak Menerima Beasiswa Lain</label>
                            <input class="form-control" type="file" name="pernyataan_beasiswa_lain" <?= empty($data['pernyataan_beasiswa_lain']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['pernyataan_beasiswa_lain'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">10. Scan Asli Pernyataan Orang Tua Tidak ASN</label>
                            <input class="form-control" type="file" name="pernyataan_orangtua_asn" <?= empty($data['pernyataan_orangtua_asn']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['pernyataan_orangtua_asn'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">11. Scan Asli Transkrip Nilai</label>
                            <input class="form-control" type="file" name="transkrip_nilai" <?= empty($data['transkrip_nilai']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['transkrip_nilai'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">12. Scan Asli Bukti Pembayaran UKT Terakhir</label>
                            <input class="form-control" type="file" name="bukti_spp" <?= empty($data['bukti_spp']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['bukti_spp'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">13. Scan Asli Buku Rekening Aktif</label>
                            <input class="form-control" type="file" name="buku_rekening" <?= empty($data['buku_rekening']) ? 'required' : ''; ?>>
                            <?php if (!empty($data['buku_rekening'])): ?><small class="text-success">✔ File sudah diunggah</small><?php endif; ?>
                        </div>
                    </div>

                    <hr class="my-4">
                    <button type="submit" class="btn btn-success btn-lg w-100">Kirim Seluruh Berkas & Data Diri</button>
                </form>
            </div>
        </div>

    </div>

</body>
</html>