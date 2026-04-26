<?php

class ingredientC
{
    
    public function addIngredient($nom)
    {
        $db = config::getConnexion();

        // verf si mwjoud deja 
        $req = $db->prepare("SELECT id_ingredient FROM ingredient WHERE nom = :nom");
        $req->execute(['nom' => $nom]);
        $res = $req->fetch();

        if ($res) {
            return $res['id_ingredient'];
        }

        // sinon cree
        $insert = $db->prepare("INSERT INTO ingredient (nom) VALUES (:nom)");
        $insert->execute(['nom' => $nom]);

        return $db->lastInsertId();
    }

    //  recupr ingrd
    public function getIngredientsByRecette($id_recette)
    {
        $db = config::getConnexion();

        $req = $db->prepare("
            SELECT i.nom, ri.quantite, ri.unite
            FROM ingredient i
            JOIN recette_ingredient ri 
            ON i.id_ingredient = ri.id_ingredient
            WHERE ri.id_recette = :id
        ");

        $req->execute(['id' => $id_recette]);

        return $req->fetchAll();
    }

   
    public function deleteByRecette($id_recette)
    {
        $db = config::getConnexion();

        $req = $db->prepare("
            DELETE FROM recette_ingredient WHERE id_recette = :id
        ");

        $req->execute(['id' => $id_recette]);
    }
}
?>