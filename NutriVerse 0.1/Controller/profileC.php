<?php

require_once __DIR__ . "/../config.php";

class profileC
{
    public function listProfile()
    {
        $db = config::getConnexion();
        try {
            $list = $db->query("SELECT * FROM profile");
            return $list;
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function addProfile($profile)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("INSERT INTO profile (telephone, date_naissance, sexe, poids, taille, objectif_nutritionnel, preference_alimentaire, allergies, id_user) VALUES (:telephone, :date_naissance, :sexe, :poids, :taille, :objectif_nutritionnel, :preference_alimentaire, :allergies, :id_user)");
            $req->execute([
                'telephone' => $profile->getTelephone(),
                'date_naissance' => $profile->getDateNaissance(),
                'sexe' => $profile->getSexe(),
                'poids' => $profile->getPoids(),
                'taille' => $profile->getTaille(),
                'objectif_nutritionnel' => $profile->getObjectifNutritionnel(),
                'preference_alimentaire' => $profile->getPreferenceAlimentaire(),
                'allergies' => $profile->getAllergies(),
                'id_user' => $profile->getIdUser()
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function delateProfile($id_user)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("DELETE FROM profile WHERE id_user = :id_user");
            $req->execute([
                'id_user' => $id_user
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function getProfileById($id_user)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM profile WHERE id_user = :id_user");
            $req->execute([
                'id_user' => $id_user
            ]);
            return $req->fetch();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function updateProfile($profile, $id_user)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("UPDATE profile SET telephone = :telephone, date_naissance = :date_naissance, sexe = :sexe, poids = :poids, taille = :taille, objectif_nutritionnel = :objectif_nutritionnel, preference_alimentaire = :preference_alimentaire, allergies = :allergies, id_user = :id_user WHERE id_user = :id_user");
            $req->execute([
                'telephone' => $profile->getTelephone(),
                'date_naissance' => $profile->getDateNaissance(),
                'sexe' => $profile->getSexe(),
                'poids' => $profile->getPoids(),
                'taille' => $profile->getTaille(),
                'objectif_nutritionnel' => $profile->getObjectifNutritionnel(),
                'preference_alimentaire' => $profile->getPreferenceAlimentaire(),
                'allergies' => $profile->getAllergies(),
                'id_user' => $profile->getIdUser()
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
}
