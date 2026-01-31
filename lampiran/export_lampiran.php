<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('ID tidak valid');

$l = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM lampiran WHERE id=$id"));
$kets = mysqli_query($conn, "SELECT * FROM lampiran_keterangan WHERE lampiran_id=$id");

$phpWord = new PhpWord();
$section = $phpWord->addSection(['orientation' => 'portrait']);

$section->addText(
    $l['judul'],
    ['bold' => true, 'size' => 14],
    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
);

while ($k = mysqli_fetch_assoc($kets)) {
    $section->addText(
        $k['keterangan'],
        ['bold' => true, 'size' => 11],
        ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
    );

    $ds = mysqli_query($conn, "SELECT * FROM lampiran_diameter WHERE keterangan_id={$k['id']}");
    while ($d = mysqli_fetch_assoc($ds)) {

        $section->addText("> Diameter {$d['diameter']} cm", ['italic' => true]);

        $table = $section->addTable(['borderSize' => 6, 'cellMargin' => 80]);
        $i = 0;
        $fs = mysqli_query($conn, "SELECT * FROM lampiran_foto WHERE diameter_id={$d['id']}");
        while ($f = mysqli_fetch_assoc($fs)) {
            if ($i % 5 == 0) $table->addRow();
            $cell = $table->addCell(1800);

            $imgPath = realpath(__DIR__ . '/../' . $f['file']);
            if ($imgPath && file_exists($imgPath)) {
                [$w, $h] = getimagesize($imgPath);

                if ($h > $w) {
                    $cell->addImage($imgPath, [
                        'height'    => 100,
                        'alignment' => Jc::CENTER
                    ]);
                } else {
                    $cell->addImage($imgPath, [
                        'width'     => 80,
                        'alignment' => Jc::CENTER
                    ]);
                }
            }
            $i++;
        }
    }
    $section->addTextBreak(1);
}

function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '_', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^_\w]+~', '', $text);
    $text = trim($text, '_');
    $text = preg_replace('~_+~', '_', $text);
    return strtolower($text);
}

$filename = slugify($l['judul']) ?: "lampiran_$id";
$tmp = sys_get_temp_dir() . "/$filename.docx";

$phpWord->save($tmp, 'Word2007');

header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header("Content-Disposition: attachment; filename=$filename.docx");
header("Content-Length: " . filesize($tmp));
readfile($tmp);
unlink($tmp);
exit;
