<?php

require_once __DIR__ . "/../config.php";

class userC
{
    public function listUser()
    {
        $db = config::getConnexion();
        try {
            $list = $db->query("SELECT * FROM user");
            return $list;
        }
        catch (Exception $e) {
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
        }
        catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function userLogin($email, $mot_de_passe)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM user WHERE email = :email AND mot_de_passe = :mot_de_passe");
            $req->execute([
                'email' => $email,
                'mot_de_passe' => $mot_de_passe
            ]);
            return $req->fetch();
        }
        catch (Exception $e) {
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
        }
        catch (Exception $e) {
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
        }
        catch (Exception $e) {
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
        }
        catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
}
