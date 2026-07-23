<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id_berkas = $_GET['id'] ?? 0;

// Menarik data berkas & data pendaftar sekaligus
$query = mysqli_query($koneksi, "
    SELECT b.*, u.email 
    FROM berkas_pendaftaran b 
    JOIN users u ON u.id = b.user_id 
    WHERE b.id = '$id_berkas'
");
$data = mysqli_fetch_assoc($query);

// Jika ID tidak ditemukan/salah
if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='admin-dashboard.php';</script>";
    exit();
}

$daftar_berkas = [
    'Surat Permohonan Walikota'               => $data['surat_walikota'],
    'Scan Asli Biodata Pribadi'               => $data['biodata_pribadi'],
    'Pas Foto Ukuran 3x4'                     => $data['pas_foto'],
    'Scan Asli KK'                            => $data['kk'],
    'Scan Asli KTP'                           => $data['ktp'],
    'Scan Asli KTM'                           => $data['ktm'],
    'Surat Keterangan Masih Kuliah'           => $data['ket_kuliah'],
    'Scan Asli DTSEN'                         => $data['dtsen'],
    'Pernyataan Tidak Menerima Beasiswa Lain' => $data['pernyataan_beasiswa_lain'],
    'Pernyataan Orang Tua Tidak ASN'          => $data['pernyataan_orangtua_asn'],
    'Scan Asli Transkrip Nilai'               => $data['transkrip_nilai'],
    'Bukti Pembayaran UKT Terakhir'           => $data['bukti_spp'],
    'Scan Asli Buku Rekening'                 => $data['buku_rekening'],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Berkas - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container my-4">
        <a href="admin-dashboard.php" class="btn btn-secondary mb-3">&larr; Kembali ke Dashboard</a>

        <!-- KARTU INFORMASI DATA DIRI PENDAFTAR -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Data Diri Pendaftar</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="35%">Nama Lengkap</th>
                                <td width="5%">:</td>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($data['nama_lengkap'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>NIK</th>
                                <td>:</td>
                                <td><?= htmlspecialchars($data['nik'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>NIM</th>
                                <td>:</td>
                                <td><?= htmlspecialchars($data['nim'] ?? '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="35%">Kampus / PT</th>
                                <td width="5%">:</td>
                                <td><?= htmlspecialchars($data['nama_kampus'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>Hobi</th>
                                <td>:</td>
                                <td><?= htmlspecialchars($data['hobi'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>Email Akun</th>
                                <td>:</td>
                                <td><?= htmlspecialchars($data['email']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL VERIFIKASI BERKAS -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Pemeriksaan Berkas Persyaratan</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Berkas Persyaratan</th>
                            <th width="20%">Lihat File</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($daftar_berkas as $nama_berkas => $filename): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $nama_berkas; ?></td>
                                <td>
                                    <?php if (!empty($filename)): ?>
                                        <a href="uploads/<?= $filename; ?>" target="_blank" class="btn btn-sm btn-outline-primary">Buka File</a>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Belum diunggah</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- FORM VERIFIKASI ADMIN -->
                <form action="process-verifikasi.php" method="POST" class="mt-4">
                    <input type="hidden" name="pendaftaran_id" value="<?= $data['id']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan / Alasan (Jika Tidak Layak):</label>
                        <textarea class="form-control" name="catatan_admin" rows="3" placeholder="Contoh: File KTP buram, mohon upload ulang."><?= htmlspecialchars($data['catatan_admin'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="status" value="Layak" class="btn btn-success btn-lg flex-fill">✓ LAYAK (Disetujui)</button>
                        <button type="submit" name="status" value="Tidak Layak" class="btn btn-danger btn-lg flex-fill">✕ TIDAK LAYAK (Ditolak)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>