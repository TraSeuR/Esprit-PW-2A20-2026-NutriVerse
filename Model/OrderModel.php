<?php

class OrderModel
{
    private $id_commande;
    private $date_commande;
    private $statut_commande;
    private $montant_total;
    private $mode_paiement;
    private $adresse_livraison;
    private $id_utilisateur;
    private $nom_client;
    private $telephone_client;

    // C'est ici qu'on prépare un objet "Commande" avec toutes ses informations importantes (prix, adresse, nom, etc.).
    public function __construct($id_commande = null, $date_commande = null, $statut_commande = null, $montant_total = null, $mode_paiement = null, $adresse_livraison = null, $id_utilisateur = null, $nom_client = null, $telephone_client = null)
    {
        $this->id_commande = $id_commande;
        $this->date_commande = $date_commande;
        $this->statut_commande = $statut_commande;
        $this->montant_total = $montant_total;
        $this->mode_paiement = $mode_paiement;
        $this->adresse_livraison = $adresse_livraison;
        $this->id_utilisateur = $id_utilisateur;
        $this->nom_client = $nom_client;
        $this->telephone_client = $telephone_client;
    }

    // Ces fonctions servent à lire ou à changer l'identifiant de la commande.
    public function getIdCommande() { return $this->id_commande; }
    public function setIdCommande($id_commande) { $this->id_commande = $id_commande; }

    // Ces fonctions servent à lire ou à changer la date de la commande.
    public function getDateCommande() { return $this->date_commande; }
    public function setDateCommande($date_commande) { $this->date_commande = $date_commande; }

    // Ces fonctions servent à lire ou à changer l'état de la commande (ex: "en attente").
    public function getStatutCommande() { return $this->statut_commande; }
    public function setStatutCommande($statut_commande) { $this->statut_commande = $statut_commande; }

    // Ces fonctions servent à lire ou à changer le prix total à payer.
    public function getMontantTotal() { return $this->montant_total; }
    public function setMontantTotal($montant_total) { $this->montant_total = $montant_total; }

    // Ces fonctions servent à lire ou à changer la façon de payer (ex: "carte").
    public function getModePaiement() { return $this->mode_paiement; }
    public function setModePaiement($mode_paiement) { $this->mode_paiement = $mode_paiement; }

    // Ces fonctions servent à lire ou à changer l'adresse de livraison.
    public function getAdresseLivraison() { return $this->adresse_livraison; }
    public function setAdresseLivraison($adresse_livraison) { $this->adresse_livraison = $adresse_livraison; }

    // Ces fonctions servent à savoir quel utilisateur a passé la commande.
    public function getIdUtilisateur() { return $this->id_utilisateur; }
    public function setIdUtilisateur($id_utilisateur) { $this->id_utilisateur = $id_utilisateur; }

    // Ces fonctions servent à lire ou à changer le nom du client.
    public function getNomClient() { return $this->nom_client; }
    public function setNomClient($nom_client) { $this->nom_client = $nom_client; }

    // Ces fonctions servent à lire ou à changer le téléphone du client.
    public function getTelephoneClient() { return $this->telephone_client; }
    public function setTelephoneClient($telephone_client) { $this->telephone_client = $telephone_client; }
}
