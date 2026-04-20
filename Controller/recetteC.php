<?php
include("../../config.php");

class recetteC
{
   public function addRecette($recette)
{
    $db = config::getConnexion();

    try {
        $req = $db->prepare('
            INSERT INTO recette (nom, description, etapes, temps_preparation, categorie, images)
            VALUES (:n,:d,:e,:t,:c,:i)
        ');

        $req->execute([
            'n' => $recette->getNom(),
            'd' => $recette->getDescription(),
            'e' => $recette->getEtapes(),
            't' => $recette->getTemps(),
            'c' => $recette->getCategorie(),
            'i' => $recette->getImage()
        ]);

    } catch (Exception $e) {
        die('Erreur: ' . $e->getMessage());
    }
}

    public function listeRecette()
    {
        $db = config::getConnexion();

        try {
            $liste = $db->query('SELECT * FROM recette ORDER BY id_recette ASC');
            return $liste;

        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteRecette($id)
    {
        $db = config::getConnexion();

        try {

            // 🔥 supprimer relations (jointure)
            $req2 = $db->prepare("DELETE FROM recette_ingredient WHERE id_recette = :id");
            $req2->execute(['id' => $id]);

            // 🔥 supprimer recette
            $req = $db->prepare('DELETE FROM recette WHERE id_recette = :id');
            $req->execute(['id' => $id]);

        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getRecette($id)
    {
        $db = config::getConnexion();

        try {
            $req = $db->prepare('
                SELECT * FROM recette
                WHERE id_recette = :id
            ');

            $req->execute([
                'id' => $id
            ]);

            return $req->fetch();

        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function updateRecette($id, $recette)
{
    $db = config::getConnexion();

    try {
        $req = $db->prepare('
            UPDATE recette
            SET nom = :n,
                description = :d,
                etapes = :e,
                temps_preparation = :t,
                categorie = :c,
                images = :i
            WHERE id_recette = :id
        ');

        $req->execute([
            'id' => $id,
            'n' => $recette->getNom(),
            'd' => $recette->getDescription(),
            'e' => $recette->getEtapes(),
            't' => $recette->getTemps(),
            'c' => $recette->getCategorie(),
            'i' => $recette->getImage()
        ]);

    } catch (Exception $e) {
        die('Erreur: ' . $e->getMessage());
    }
}
    

public function listes($categorie, $search)
{
    $db = config::getConnexion();

    $sql = "SELECT * FROM recette WHERE 1=1";

    if ($categorie != "" && $categorie != "all") {
        $sql .= " AND categorie = '$categorie'";
    }

    if ($search != "") {
        $sql .= " AND nom LIKE '%$search%'";
    }

    return $db->query($sql);
}

public function getrecetteD($id)
{
    $db = config::getConnexion();

    $req = $db->prepare("SELECT * FROM recette WHERE id_recette = :id");
    $req->execute(['id' => $id]);

    return $req->fetch();
}

}
?>