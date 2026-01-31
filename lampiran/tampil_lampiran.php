<?php
$lampiran = mysqli_query($conn, "
    SELECT id, judul
    FROM lampiran
    ORDER BY id DESC
");

if (!$lampiran) {
    die('Query lampiran gagal: ' . mysqli_error($conn));
}
?>


<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Lampiran Tagihan</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Lampiran</h6>
            <a href="index.php?page=tambah_lampiran" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Tambah Lampiran
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

                    <thead>
                        <tr>
                            <th style="width:5%">No</th>
                            <th style="width:25%">Nama Lampiran</th>
                            <th>Isi Lampiran</th>
                            <th style="width:10%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1;
                        while ($l = mysqli_fetch_assoc($lampiran)) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <a href="#collapse<?= $l['id'] ?>"
                                        data-toggle="collapse"
                                        aria-expanded="false"
                                        aria-controls="collapse<?= $l['id'] ?>">
                                        <i class="fas fa-chevron-down mr-1"></i>
                                        <?= htmlspecialchars($l['judul']) ?>
                                    </a>
                                </td>
                                <td>
                                    <!-- Collapse area -->
                                    <div id="collapse<?= $l['id'] ?>" class="collapse mt-2">
                                        <?php
                                        $kets = mysqli_query($conn, "
                                            SELECT id, keterangan
                                            FROM lampiran_keterangan
                                            WHERE lampiran_id = {$l['id']}
                                        ");
                                        while ($k = mysqli_fetch_assoc($kets)) :
                                        ?>
                                            <div class="border rounded p-2 mb-2">
                                                <strong>Keterangan:</strong><br>
                                                <?= nl2br(htmlspecialchars($k['keterangan'])) ?>

                                                <hr class="my-2">

                                                <?php
                                                $diams = mysqli_query($conn, "
                                                    SELECT id, diameter
                                                    FROM lampiran_diameter
                                                    WHERE keterangan_id = {$k['id']}
                                                ");
                                                while ($d = mysqli_fetch_assoc($diams)) :
                                                ?>
                                                    <div class="mb-2">
                                                        <span class="badge badge-info">
                                                            Diameter <?= htmlspecialchars($d['diameter']) ?>
                                                        </span><br>

                                                        <?php
                                                        $fotos = mysqli_query($conn, "
                                                            SELECT file FROM lampiran_foto
                                                            WHERE diameter_id = {$d['id']}
                                                        ");
                                                        while ($f = mysqli_fetch_assoc($fotos)) :
                                                        ?>
                                                            <img src="<?= htmlspecialchars($f['file']) ?>"
                                                                width="40"
                                                                style="border:1px solid #ccc; margin:2px;">
                                                        <?php endwhile; ?>
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="lampiran/export_lampiran.php?id=<?= $l['id'] ?>"
                                        class="btn btn-sm btn-primary"
                                        target="_blank">
                                        <i class="fas fa-file-word"></i>
                                    </a>
                                    <a href="index.php?page=hapus_lampiran&id=<?= $l['id'] ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>


                </table>
            </div>
        </div>
    </div>

</div>