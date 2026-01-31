<?php
require_once __DIR__ . '/../koneksi.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('ID tidak valid');

// Ambil data lampiran
$lampiran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM lampiran WHERE id=$id"));
$kets = mysqli_query($conn, "SELECT * FROM lampiran_keterangan WHERE lampiran_id=$id");

// Handle submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $judul = mysqli_real_escape_string($conn, trim($_POST['judul']));
    mysqli_query($conn, "UPDATE lampiran SET judul='$judul' WHERE id=$id");

    // Ambil semua file foto lama untuk dihapus
    $oldFotos = mysqli_query($conn, "
        SELECT f.file 
        FROM lampiran_foto f
        JOIN lampiran_diameter d ON f.diameter_id = d.id
        JOIN lampiran_keterangan k ON d.keterangan_id = k.id
        WHERE k.lampiran_id = $id
    ");
    while ($f = mysqli_fetch_assoc($oldFotos)) {
        $path = __DIR__ . '/../' . $f['file'];
        if (file_exists($path)) unlink($path);
    }

    // Hapus data lama (child → parent)
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

    // Insert ulang data baru
    if (!empty($_POST['keterangan'])) {
        foreach ($_POST['keterangan'] as $ki => $ket) {

            $ket = mysqli_real_escape_string($conn, trim($ket));
            if ($ket === '') continue;

            mysqli_query($conn, "INSERT INTO lampiran_keterangan (lampiran_id, keterangan)
                                 VALUES ($id, '$ket')");
            $keterangan_id = mysqli_insert_id($conn);

            if (!empty($_POST['diameter'][$ki])) {
                foreach ($_POST['diameter'][$ki] as $di => $diam) {

                    $diam = mysqli_real_escape_string($conn, $diam);
                    mysqli_query($conn, "INSERT INTO lampiran_diameter (keterangan_id, diameter)
                                         VALUES ($keterangan_id, '$diam')");
                    $diameter_id = mysqli_insert_id($conn);

                    if (!empty($_FILES['foto']['name'][$ki][$di])) {
                        foreach ($_FILES['foto']['name'][$ki][$di] as $fi => $name) {

                            if ($_FILES['foto']['error'][$ki][$di][$fi] !== UPLOAD_ERR_OK) continue;

                            $tmp = $_FILES['foto']['tmp_name'][$ki][$di][$fi];
                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                            $new = time() . "_{$ki}_{$di}_{$fi}." . $ext;

                            if (!is_dir("uploads")) mkdir("uploads", 0777, true);

                            if (move_uploaded_file($tmp, "uploads/$new")) {
                                mysqli_query($conn, "INSERT INTO lampiran_foto (diameter_id, file)
                                                     VALUES ($diameter_id, 'uploads/$new')");
                            }
                        }
                    }
                }
            }
        }
    }

    echo "<script>alert('Lampiran berhasil diperbarui'); window.location='index.php?page=tampil_lampiran';</script>";
    exit;
}
?>

<div class="container-fluid">

    <h1 class="h3 mb-3 text-gray-800">Edit Lampiran</h1>

    <form method="post" enctype="multipart/form-data">

        <div class="form-group">
            <label>Judul Lampiran</label>
            <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($lampiran['judul']) ?>" required>
        </div>

        <hr>

        <div id="keterangan-wrapper">
            <?php $ki = 0;
            while ($k = mysqli_fetch_assoc($kets)) : ?>
                <div class="keterangan-item border p-3 mb-3">
                    <textarea name="keterangan[<?= $ki ?>]" class="form-control mb-2" required><?= htmlspecialchars($k['keterangan']) ?></textarea>
                    <div class="diameter-wrapper">
                        <div class="diameter-item mb-2">
                            <select name="diameter[<?= $ki ?>][]" class="form-control mb-1" required>
                                <option value="15-20">15–20</option>
                                <option value="21-30">21–30</option>
                                <option value="31-50">31–50</option>
                                <option value=">50">&gt;50</option>
                            </select>
                            <input type="file" name="foto[<?= $ki ?>][0][]" multiple class="form-control">
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary addDiameter">+ Tambah Diameter</button>
                </div>
            <?php $ki++;
            endwhile; ?>
        </div>

        <button type="button" id="addKeterangan" class="btn btn-sm btn-info mb-3">+ Tambah Keterangan</button>

        <br>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="index.php?page=tampil_lampiran" class="btn btn-secondary">Batal</a>

    </form>
</div>

<script>
    let kIndex = <?= $ki ?>;

    document.getElementById('addKeterangan').onclick = function() {
        let html = `
    <div class="keterangan-item border p-3 mb-3">
        <textarea name="keterangan[${kIndex}]" class="form-control mb-2" required></textarea>
        <div class="diameter-wrapper">
            <div class="diameter-item mb-2">
                <select name="diameter[${kIndex}][]" class="form-control mb-1" required>
                    <option value="15-20">15–20</option>
                    <option value="21-30">21–30</option>
                    <option value="31-50">31–50</option>
                    <option value=">50">&gt;50</option>
                </select>
                <input type="file" name="foto[${kIndex}][0][]" multiple class="form-control">
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-secondary addDiameter">+ Tambah Diameter</button>
    </div>`;
        document.getElementById('keterangan-wrapper').insertAdjacentHTML('beforeend', html);
        kIndex++;
    };

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('addDiameter')) {
            let wrap = e.target.closest('.keterangan-item').querySelector('.diameter-wrapper');
            let ki = [...document.querySelectorAll('.keterangan-item')].indexOf(e.target.closest('.keterangan-item'));
            let di = wrap.children.length;

            wrap.insertAdjacentHTML('beforeend', `
        <div class="diameter-item mb-2">
            <select name="diameter[${ki}][]" class="form-control mb-1" required>
                <option value="15-20">15–20</option>
                <option value="21-30">21–30</option>
                <option value="31-50">31–50</option>
                <option value=">50">&gt;50</option>
            </select>
            <input type="file" name="foto[${ki}][${di}][]" multiple class="form-control">
        </div>`);
        }
    });
</script>