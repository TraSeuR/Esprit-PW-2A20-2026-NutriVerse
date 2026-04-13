<?php
include("../../Controller/recetteC.php");

$recetteC = new recetteC();

if (isset($_GET['id'])) {
    $recette = $recetteC->getrecetteD($_GET['id']);
}
?>
<!--nverifiw eidha ken fama id fl url ken ey nodkhlou fl if >
<!-naytou lgetrecetteD w natiwha lid bch trajelna lesinfos corrsp w nestokiw fi $recette>