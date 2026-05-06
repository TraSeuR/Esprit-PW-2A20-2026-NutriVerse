<?php
include("../../../controller/recetteC.php");
include("../../../model/recette.php");


$recetteC = new recetteC();

if (isset($_POST['id'])) {

    // récupr pic lkdima ml base
    $old = $recetteC->getRecette($_POST['id']);
    $image = $old['images'];

    // nvl pic si upload
    if (isset($_FILES['image']) && $_FILES['image']['tmp_name'] != "") {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    $recette = new recette(
        
        $_POST['nom'],
        $_POST['description'],
        $_POST['etapes'],
        $_POST['temps'],
        $_POST['categorie'],
        $image
    );

    // update fl base 
    $recetteC->updateRecette(
        $_POST['id'],
        $recette
    );
    $db = config::getConnexion();

// supprimer ingr lkdom
$del = $db->prepare("DELETE FROM recette_ingredient WHERE id_recette = :id");
$del->execute(['id' => $_POST['id']]);

//ajouter nv ingrd
if (isset($_POST['ingredient_nom'])) {

    for ($i = 0; $i < count($_POST['ingredient_nom']); $i++) {

        $nom = $_POST['ingredient_nom'][$i];
        $nom = strtolower(trim($nom)); 

        $qte = $_POST['ingredient_qte'][$i];
        $unite = $_POST['ingredient_unite'][$i];

        if ($nom != "" && $qte != "" && $unite != "") {

            $req = $db->prepare("SELECT id_ingredient FROM ingredient WHERE nom = :nom");
            $req->execute(['nom' => $nom]);
            $res = $req->fetch();

            if ($res) {
                $idIngredient = $res['id_ingredient'];
            } else {
                $insert = $db->prepare("INSERT INTO ingredient (nom) VALUES (:nom)");
                $insert->execute(['nom' => $nom]);
                $idIngredient = $db->lastInsertId();
            }

            $sql2 = "INSERT INTO recette_ingredient (id_recette, id_ingredient, quantite, unite)
                     VALUES (:r, :i, :qte, :u)";
            $query2 = $db->prepare($sql2);
            $query2->execute([
                'r' => $_POST['id'],
                'i' => $idIngredient,
                'qte' => $qte,
                'u' => $unite
            ]);
        }
    }
}

    header('Location: admin.php?msg=update');
    
}
?>
