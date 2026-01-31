<?php
include "koneksi.php";
include "template/header.php";
?>

<div class="container-fluid">
    <?php
    $page = $_GET['page'] ?? 'dashboard';

    switch ($page) {

        case "dashboard":
            include "dashboard.php";
            break;

        case "tampil_lampiran":
            include "lampiran/tampil_lampiran.php";
            break;

        case "tambah_lampiran":
            include "lampiran/tambah_lampiran.php";
            break;

        case "edit_lampiran":
            include "lampiran/edit_lampiran.php";
            break;

        case "hapus_lampiran":
            include "lampiran/hapus_lampiran.php";
            break;

        default:
            include "dashboard.php";
            break;
    }
    ?>
</div>

<?php include "template/footer.php"; ?>