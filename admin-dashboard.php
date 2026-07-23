<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$query = mysqli_query($koneksi, "
    SELECT b.id AS id_berkas, u.nama_lengkap, u.email, b.status_verifikasi, b.updated_at 
    FROM users u 
    JOIN berkas_pendaftaran b ON u.id = b.user_id 
    WHERE u.role = 'user'
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - SIP Beasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Admin Verifikator Beasiswa</a>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container my-4">
        <h4 class="mb-4">Daftar Pendaftar Beasiswa</h4>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="alert alert-success">Status kelayakan berhasil diperbarui!</div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Pendaftar</th>
                            <th>Email</th>
                            <th>Status Verifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <?php if ($row['status_verifikasi'] == 'Menunggu'): ?>
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    <?php elseif ($row['status_verifikasi'] == 'Layak'): ?>
                                        <span class="badge bg-success">Layak</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Tidak Layak</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="admin-verifikasi.php?id=<?= $row['id_berkas']; ?>" class="btn btn-sm btn-primary">Periksa 13 Berkas</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>