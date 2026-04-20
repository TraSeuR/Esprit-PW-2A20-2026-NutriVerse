<?php
include("../../Controller/recetteC.php");
include("../../Model/recette.php");

if (
    isset($_POST['nom']) &&
    isset($_POST['description']) &&
    isset($_POST['etapes']) &&
    isset($_POST['temps']) &&
    isset($_POST['categorie'])
) {

    $image = null;

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

    $recetteC = new recetteC();
    $recetteC->addRecette($recette);

    $db = config::getConnexion();
    $id_recette = $db->lastInsertId();

    if (isset($_POST['ingredient_nom'])) {

        for ($i = 0; $i < count($_POST['ingredient_nom']); $i++) {

            $nom = trim($_POST['ingredient_nom'][$i]);
            $qte = $_POST['ingredient_qte'][$i];
            $unite = $_POST['ingredient_unite'][$i];

            if ($nom != "" && $qte != "" && $unite != "") {

                // 🔍 vérifier si ingrédient existe déjà
                $check = $db->prepare("SELECT id_ingredient FROM ingredient WHERE nom = :nom");
                $check->execute(['nom' => $nom]);
                $existing = $check->fetch();

                if ($existing) {
                    $idIngredient = $existing['id_ingredient'];
                } else {
                    // ➕ ajouter nouvel ingrédient (nom seulement)
                    $insert = $db->prepare("
                        INSERT INTO ingredient (nom)
                        VALUES (:nom)
                    ");

                    $insert->execute([
                        'nom' => $nom
                    ]);

                    $idIngredient = $db->lastInsertId();
                }

                // 🔗 liaison avec quantité + unité
                $link = $db->prepare("
                    INSERT INTO recette_ingredient (id_recette, id_ingredient, quantite, unite)
                    VALUES (:r, :i, :qte, :u)
                ");

                $link->execute([
                    'r' => $id_recette,
                    'i' => $idIngredient,
                    'qte' => $qte,
                    'u' => $unite
                ]);
            }
        }
    }

    header('Location: admin.php?msg=ajout');
}
?>