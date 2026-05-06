<?php

class OrderDetailModel
{
    private $id_ligne;
    private $id_commande;
    private $id_produit;
    private $quantite;
    private $prix_unitaire;
    private $nom_produit; // For display purposes

    // C'est ici qu'on prépare un objet qui représente une ligne d'une commande (un produit précis et sa quantité).
    public function __construct($id_ligne = null, $id_commande = null, $id_produit = null, $quantite = null, $prix_unitaire = null, $nom_produit = null)
    {
        $this->id_ligne = $id_ligne;
        $this->id_commande = $id_commande;
        $this->id_produit = $id_produit;
        $this->quantite = $quantite;
        $this->prix_unitaire = $prix_unitaire;
        $this->nom_produit = $nom_produit;
    }

    // Ces fonctions servent à lire ou à changer l'identifiant de la ligne de commande.
    public function getIdLigne() { return $this->id_ligne; }
    public function setIdLigne($id_ligne) { $this->id_ligne = $id_ligne; }

    // Ces fonctions servent à savoir à quelle commande cette ligne appartient.
    public function getIdCommande() { return $this->id_commande; }
    public function setIdCommande($id_commande) { $this->id_commande = $id_commande; }

    // Ces fonctions servent à savoir quel produit a été acheté.
    public function getIdProduit() { return $this->id_produit; }
    public function setIdProduit($id_produit) { $this->id_produit = $id_produit; }

    // Ces fonctions servent à lire ou à changer le nombre d'articles achetés.
    public function getQuantite() { return $this->quantite; }
    public function setQuantite($quantite) { $this->quantite = $quantite; }

    // Ces fonctions servent à lire ou à changer le prix de l'article au moment de l'achat.
    public function getPrixUnitaire() { return $this->prix_unitaire; }
    public function setPrixUnitaire($prix_unitaire) { $this->prix_unitaire = $prix_unitaire; }

    // Ces fonctions servent à lire ou à changer le nom du produit (pour l'affichage).
    public function getNomProduit() { return $this->nom_produit; }
    public function setNomProduit($nom_produit) { $this->nom_produit = $nom_produit; }
}
