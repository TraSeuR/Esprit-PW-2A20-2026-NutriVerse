<?php

require_once __DIR__ . "/../config.php";

class userC
{
    public function listUser()
    {
        $db = config::getConnexion();
        try {
            $stmt = $db->query(
                "SELECT u.id_user, u.nom, u.prenom, u.email, u.role,
                        u.etat_compte, u.date_inscription,
                        p.telephone, p.date_naissance, p.sexe,
                        p.poids, p.taille,
                        p.objectif_nutritionnel,
                        p.preference_alimentaire,
                        p.allergies
                 FROM user u
                 LEFT JOIN profile p ON u.id_user = p.id_user
                 ORDER BY u.id_user ASC"
            );
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function addUser($user)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("INSERT INTO user (nom, prenom, email, mot_de_passe, role, remember_me, etat_compte) VALUES (:nom, :prenom, :email, :mot_de_passe, :role, :remember_me, :etat_compte)");
            $req->execute([
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'mot_de_passe' => $user->getMotDePasse(),
                'role' => $user->getRole(),
                'remember_me' => $user->getRememberMe(),
                'etat_compte' => $user->getEtatCompte()
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function userLogin($email, $mot_de_passe)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM user WHERE email = :email");
            $req->execute([
                'email' => $email
            ]);
            $user = $req->fetch();
            
            if ($user) {
                if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
                    return $user;
                }
                // Fallback for non-hashed old passwords during transition
                if ($mot_de_passe === $user['mot_de_passe']) {
                    return $user;
                }
            }
            return false;
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function delateUser($id)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("DELETE FROM user WHERE id_user = :id");
            $req->execute([
                'id' => $id
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function getUserById($id)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM user WHERE id_user = :id");
            $req->execute([
                'id' => $id
            ]);
            return $req->fetch();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function updateUser($user, $id)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("UPDATE user SET nom = :nom, prenom = :prenom, email = :email, mot_de_passe = :mot_de_passe, role = :role, remember_me = :remember_me, etat_compte = :etat_compte WHERE id_user = :id");
            $req->execute([
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'mot_de_passe' => $user->getMotDePasse(),
                'role' => $user->getRole(),
                'remember_me' => $user->getRememberMe(),
                'etat_compte' => $user->getEtatCompte(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
}
