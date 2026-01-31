<?php
require_once __DIR__ . '/../koneksi.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    echo "<script>alert('ID tidak valid'); window.location='index.php?page=tampil_lampiran';</script>";
    exit;
}

// Ambil semua file foto terlebih dahulu
$fotos = mysqli_query($conn, "
    SELECT f.file 
    FROM lampiran_foto f
    JOIN lampiran_diameter d ON f.diameter_id = d.id
    JOIN lampiran_keterangan k ON d.keterangan_id = k.id
    WHERE k.lampiran_id = $id
");

while ($f = mysqli_fetch_assoc($fotos)) {
    $path = __DIR__ . '/../' . $f['file'];
    if (file_exists($path)) {
        unlink($path);
    }
}

// Hapus data berurutan (child → parent)
mysqli_query($conn, "
    DELETE f FROM lampiran_foto f
    JOIN lampiran_diameter d ON f.diameter_id = d.id
    JOIN lampiran_keterangan k ON d.keterangan_id = k.id
    WHERE k.lampiran_id = $id
");

mysqli_query($conn, "
    DELETE d FROM lampiran_diameter d
    JOIN lampiran_keterangan k ON d.keterangan_id = k.id
    WHERE k.lampiran_id = $id
");

mysqli_query($conn, "DELETE FROM lampiran_keterangan WHERE lampiran_id = $id");
mysqli_query($conn, "DELETE FROM lampiran WHERE id = $id");

echo "<script>alert('Lampiran berhasil dihapus'); window.location='index.php?page=tampil_lampiran';</script>";
exit;
