<?php

class LivraisonModel
{
    private $id_livraison;
    private $date_livraison;
    private $status_livraison;
    private $adresse_livraison;
    private $nom_livreur;
    private $id_commande;

    // C'est ici qu'on prépare un objet "Livraison" avec toutes ses informations (date, adresse, livreur, etc.).
    public function __construct($id_livraison = null, $date_livraison = null, $status_livraison = null, $adresse_livraison = null, $nom_livreur = null, $id_commande = null)
    {
        $this->id_livraison = $id_livraison;
        $this->date_livraison = $date_livraison;
        $this->status_livraison = $status_livraison;
        $this->adresse_livraison = $adresse_livraison;
        $this->nom_livreur = $nom_livreur;
        $this->id_commande = $id_commande;
    }

    // Ces fonctions servent à lire ou à changer l'identifiant de la livraison.
    public function getIdLivraison() { return $this->id_livraison; }
    public function setIdLivraison($id_livraison) { $this->id_livraison = $id_livraison; }

    // Ces fonctions servent à lire ou à changer la date prévue de livraison.
    public function getDateLivraison() { return $this->date_livraison; }
    public function setDateLivraison($date_livraison) { $this->date_livraison = $date_livraison; }

    // Ces fonctions servent à lire ou à changer l'état actuel (ex: "en cours").
    public function getStatusLivraison() { return $this->status_livraison; }
    public function setStatusLivraison($status_livraison) { $this->status_livraison = $status_livraison; }

    // Ces fonctions servent à lire ou à changer l'adresse où livrer.
    public function getAdresseLivraison() { return $this->adresse_livraison; }
    public function setAdresseLivraison($adresse_livraison) { $this->adresse_livraison = $adresse_livraison; }

    // Ces fonctions servent à lire ou à changer le nom de la personne qui livre.
    public function getNomLivreur() { return $this->nom_livreur; }
    public function setNomLivreur($nom_livreur) { $this->nom_livreur = $nom_livreur; }

    // Ces fonctions servent à savoir à quelle commande cette livraison appartient.
    public function getIdCommande() { return $this->id_commande; }
    public function setIdCommande($id_commande) { $this->id_commande = $id_commande; }
}
