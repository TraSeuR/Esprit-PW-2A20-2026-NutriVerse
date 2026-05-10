<?php
require_once __DIR__ . '/../config/config.php';

class ingredientC {
    public function getIngredientsByRecette($idRecette) {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("
                SELECT i.nom, ri.quantite, ri.unite 
                FROM ingredient i
                JOIN recette_ingredient ri ON i.id_ingredient = ri.id_ingredient
                WHERE ri.id_recette = :id
            ");
            $query->execute(['id' => $idRecette]);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>
