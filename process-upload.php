<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Tangkap Data Teks dari Form
$nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
$nik          = mysqli_real_escape_string($koneksi, $_POST['nik']);
$nim          = mysqli_real_escape_string($koneksi, $_POST['nim']);
$nama_kampus  = mysqli_real_escape_string($koneksi, $_POST['nama_kampus']);
$hobi         = mysqli_real_escape_string($koneksi, $_POST['hobi']);

// 2. Cek apakah user sudah punya data pendaftaran di database
$cek_query = mysqli_query($koneksi, "SELECT * FROM berkas_pendaftaran WHERE user_id = '$user_id'");
$data_lama = mysqli_fetch_assoc($cek_query);

// Folder tujuan upload
$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Daftar nama input file
$file_fields = [
    'surat_walikota', 'biodata_pribadi', 'pas_foto', 'kk', 'ktp', 
    'ktm', 'ket_kuliah', 'dtsen', 'pernyataan_beasiswa_lain', 
    'pernyataan_orangtua_asn', 'transkrip_nilai', 'bukti_spp', 'buku_rekening'
];

$file_uploaded = [];

// 3. Proses upload masing-masing file
foreach ($file_fields as $field) {
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
        $file_tmp   = $_FILES[$field]['tmp_name'];
        $file_ext   = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        
        // Buat nama file unik agar tidak bentrok (misal: 12_ktp_169000000.pdf)
        $new_name   = $user_id . '_' . $field . '_' . time() . '.' . $file_ext;
        $target_file = $target_dir . $new_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            $file_uploaded[$field] = $new_name;
        } else {
            $file_uploaded[$field] = $data_lama[$field] ?? null;
        }
    } else {
        // Jika file tidak diunggah ulang, gunakan nama file lama
        $file_uploaded[$field] = $data_lama[$field] ?? null;
    }
}

// 4. Update atau Insert ke Database
if ($data_lama) {
    // UPDATE data yang sudah ada
    $query = "UPDATE berkas_pendaftaran SET 
        nama_lengkap = '$nama_lengkap',
        nik = '$nik',
        nim = '$nim',
        nama_kampus = '$nama_kampus',
        hobi = '$hobi',
        surat_walikota = '{$file_uploaded['surat_walikota']}',
        biodata_pribadi = '{$file_uploaded['biodata_pribadi']}',
        pas_foto = '{$file_uploaded['pas_foto']}',
        kk = '{$file_uploaded['kk']}',
        ktp = '{$file_uploaded['ktp']}',
        ktm = '{$file_uploaded['ktm']}',
        ket_kuliah = '{$file_uploaded['ket_kuliah']}',
        dtsen = '{$file_uploaded['dtsen']}',
        pernyataan_beasiswa_lain = '{$file_uploaded['pernyataan_beasiswa_lain']}',
        pernyataan_orangtua_asn = '{$file_uploaded['pernyataan_orangtua_asn']}',
        transkrip_nilai = '{$file_uploaded['transkrip_nilai']}',
        bukti_spp = '{$file_uploaded['bukti_spp']}',
        buku_rekening = '{$file_uploaded['buku_rekening']}',
        status_verifikasi = 'Menunggu'
        WHERE user_id = '$user_id'";
} else {
    // INSERT data baru jika belum pernah upload
    $query = "INSERT INTO berkas_pendaftaran 
        (user_id, nama_lengkap, nik, nim, nama_kampus, hobi, surat_walikota, biodata_pribadi, pas_foto, kk, ktp, ktm, ket_kuliah, dtsen, pernyataan_beasiswa_lain, pernyataan_orangtua_asn, transkrip_nilai, bukti_spp, buku_rekening, status_verifikasi) 
        VALUES 
        ('$user_id', '$nama_lengkap', '$nik', '$nim', '$nama_kampus', '$hobi', 
        '{$file_uploaded['surat_walikota']}', '{$file_uploaded['biodata_pribadi']}', '{$file_uploaded['pas_foto']}', '{$file_uploaded['kk']}', '{$file_uploaded['ktp']}', '{$file_uploaded['ktm']}', '{$file_uploaded['ket_kuliah']}', '{$file_uploaded['dtsen']}', '{$file_uploaded['pernyataan_beasiswa_lain']}', '{$file_uploaded['pernyataan_orangtua_asn']}', '{$file_uploaded['transkrip_nilai']}', '{$file_uploaded['bukti_spp']}', '{$file_uploaded['buku_rekening']}', 'Menunggu')";
}

if (mysqli_query($koneksi, $query)) {
    header("Location: user-dashboard.php?status=success");
} else {
    echo "Gagal menyimpan data: " . mysqli_error($koneksi);
}
?>