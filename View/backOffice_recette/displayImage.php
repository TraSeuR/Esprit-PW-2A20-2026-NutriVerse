<?php
include("../../Controller/recetteC.php");

$recetteC = new recetteC();

if (isset($_GET['id'])) {

    $r = $recetteC->getRecette($_GET['id']);

    if (!$r || empty($r['images'])) {
        die("Image introuvable");
    }

   
    $finfo = finfo_open();
    $mime = finfo_buffer($finfo, $r['images'], FILEINFO_MIME_TYPE);
    finfo_close($finfo);

    header("Content-Type: " . $mime);
    echo $r['images'];
}
?>