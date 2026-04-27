<?php
require_once __DIR__ . '/../config/database.php';

class Echange {
    private $id_echange;
    private $id_offre_demandeur;
    private $id_offre_offreur;
    private $id_demandeur;
    private $id_offreur;
    private $message;
    private $date_demande;
    private $statut;
    private $note_demandeur;
    private $note_offreur;

    public function __construct($id_offre_demandeur = null, $id_offre_offreur = null, $id_demandeur = null, $id_offreur = null, $message = null, $statut = 'en_attente', $id_echange = null) {
        $this->id_offre_demandeur = $id_offre_demandeur;
        $this->id_offre_offreur = $id_offre_offreur;
        $this->id_demandeur = $id_demandeur;
        $this->id_offreur = $id_offreur;
        $this->message = $message;
        $this->statut = $statut;
        $this->id_echange = $id_echange;
    }

    // Getters
    public function getIdEchange() { return $this->id_echange; }
    public function getIdOffreDemandeur() { return $this->id_offre_demandeur; }
    public function getIdOffreOffreur() { return $this->id_offre_offreur; }
    public function getIdDemandeur() { return $this->id_demandeur; }
    public function getIdOffreur() { return $this->id_offreur; }
    public function getMessage() { return $this->message; }
    public function getDateDemande() { return $this->date_demande; }
    public function getStatut() { return $this->statut; }
    public function getNoteDemandeur() { return $this->note_demandeur; }
    public function getNoteOffreur() { return $this->note_offreur; }

    // Setters
    public function setIdEchange($id) { $this->id_echange = $id; }
    public function setIdOffreDemandeur($id) { $this->id_offre_demandeur = $id; }
    public function setIdOffreOffreur($id) { $this->id_offre_offreur = $id; }
    public function setIdDemandeur($id) { $this->id_demandeur = $id; }
    public function setIdOffreur($id) { $this->id_offreur = $id; }
    public function setMessage($msg) { $this->message = $msg; }
    public function setDateDemande($date) { $this->date_demande = $date; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setNoteDemandeur($note) { $this->note_demandeur = $note; }
    public function setNoteOffreur($note) { $this->note_offreur = $note; }
}
?>
