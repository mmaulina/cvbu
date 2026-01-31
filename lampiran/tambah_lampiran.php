<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Cegah double submit
    if (isset($_SESSION['lampiran_submitted'])) {
        header("Location: index.php?page=tampil_lampiran");
        exit;
    }
    $_SESSION['lampiran_submitted'] = true;

    $judul = mysqli_real_escape_string($conn, trim($_POST['judul']));

    // Simpan lampiran
    mysqli_query($conn, "INSERT INTO lampiran (judul) VALUES ('$judul')");
    $lampiran_id = mysqli_insert_id($conn);

    if (!empty($_POST['keterangan'])) {
        foreach ($_POST['keterangan'] as $ki => $ket) {

            $ket = mysqli_real_escape_string($conn, trim($ket));
            if ($ket === '') continue;

            mysqli_query($conn, "INSERT INTO lampiran_keterangan (lampiran_id, keterangan)
                                 VALUES ($lampiran_id, '$ket')");
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

                            if (!is_dir("uploads")) {
                                mkdir("uploads", 0777, true);
                            }

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

    unset($_SESSION['lampiran_submitted']);
    echo "<script>window.location.href='index.php?page=tampil_lampiran';</script>";
    exit;
}
?>


<div class="container-fluid">

    <h1 class="h3 mb-3 text-gray-800">Tambah Lampiran</h1>

    <form method="post" enctype="multipart/form-data">

        <div class="form-group">
            <label>Judul Lampiran</label>
            <input type="text" name="judul" class="form-control" required>
        </div>

        <hr>

        <div id="keterangan-wrapper">

            <div class="keterangan-item border p-3 mb-3">
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan[0]" class="form-control" rows="2" required></textarea>
                    <small class="form-text text-muted">
                        Contoh: PPS01 - KALAWA 01/01/2026
                    </small>
                </div>

                <div class="diameter-wrapper">

                    <div class="diameter-item mb-2">
                        <label>Diameter</label>
                        <select name="diameter[0][]" class="form-control mb-1" required>
                            <option value="15-20">15–20</option>
                            <option value="21-30">21–30</option>
                            <option value="31-50">31–50</option>
                            <option value=">50">&gt;50</option>
                        </select>

                        <div class="custom-file">
                            <input type="file"
                                name="foto[0][0][]"
                                class="custom-file-input"
                                id="foto_0_0"
                                multiple
                                accept="image/*">
                            <label class="custom-file-label" for="foto_0_0">Pilih foto…</label>
                        </div>

                    </div>

                </div>

                <button type="button" class="btn btn-sm btn-secondary addDiameter">+ Tambah Diameter</button>
            </div>

        </div>

        <button type="button" id="addKeterangan" class="btn btn-sm btn-info mb-3">
            + Tambah Keterangan
        </button>

        <br>

        <button type="submit" class="btn btn-primary" onclick="this.disabled=true; this.form.submit();">
            Simpan
        </button>
        <a href="index.php?page=tampil_lampiran" class="btn btn-secondary">Batal</a>

    </form>
</div>

<script>
let kIndex = 1;

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

                <div class="custom-file">
                    <input type="file"
                           name="foto[${kIndex}][0][]"
                           class="custom-file-input"
                           id="foto_${kIndex}_0"
                           multiple
                           accept="image/*">
                    <label class="custom-file-label" for="foto_${kIndex}_0">Pilih foto…</label>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-sm btn-secondary addDiameter">+ Tambah Diameter</button>
    </div>`;
    document.getElementById('keterangan-wrapper').insertAdjacentHTML('beforeend', html);
    kIndex++;
};

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('addDiameter')) {
        let item = e.target.closest('.keterangan-item');
        let wrap = item.querySelector('.diameter-wrapper');
        let ki = [...document.querySelectorAll('.keterangan-item')].indexOf(item);
        let di = wrap.children.length;

        wrap.insertAdjacentHTML('beforeend', `
        <div class="diameter-item mb-2">
            <select name="diameter[${ki}][]" class="form-control mb-1" required>
                <option value="15-20">15–20</option>
                <option value="21-30">21–30</option>
                <option value="31-50">31–50</option>
                <option value=">50">&gt;50</option>
            </select>

            <div class="custom-file">
                <input type="file"
                       name="foto[${ki}][${di}][]"
                       class="custom-file-input"
                       id="foto_${ki}_${di}"
                       multiple
                       accept="image/*">
                <label class="custom-file-label" for="foto_${ki}_${di}">Pilih foto…</label>
            </div>
        </div>`);
    }
});

// Update label saat file dipilih
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('custom-file-input')) {
        let names = Array.from(e.target.files).map(f => f.name).join(', ');
        e.target.nextElementSibling.innerText = names || 'Pilih foto…';
    }
});
</script>
