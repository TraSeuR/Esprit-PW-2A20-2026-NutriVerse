<?php
ob_start();

include_once("../../../controller/recetteC.php");

$recetteC = new recetteC();

if (!isset($_GET['id'])) {
    exit;
}

$r = $recetteC->getRecette($_GET['id']);

if (!$r || empty($r['images'])) {
    exit;
}

$image = $r['images'];

ob_clean();

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_buffer($finfo, $image);
finfo_close($finfo);

header("Content-Type: " . $mime);
header("Content-Length: " . strlen($image));
echo $image;
exit;
?>
