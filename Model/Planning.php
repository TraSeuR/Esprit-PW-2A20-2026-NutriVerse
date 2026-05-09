<?php
class Planning
{
    private $id_planning;
    private $id_regime;
    private $programme_sport;
    private $sommeil;
    private $titre_planning;
    private $description;
    private $statut;
    private $commentaire;

    public function __construct(
        $id_planning     = null,
        $id_regime       = null,
        $programme_sport = null,
        $sommeil         = null,
        $titre_planning  = null,
        $description     = null,
        $statut          = 'en_attente',
        $commentaire     = null
    ) {
        $this->id_planning     = $id_planning;
        $this->id_regime       = $id_regime;
        $this->programme_sport = $programme_sport;
        $this->sommeil         = $sommeil;
        $this->titre_planning  = $titre_planning;
        $this->description     = $description;
        $this->statut          = $statut;
        $this->commentaire     = $commentaire;
    }

    // Getters
    public function getIdPlanning()    { return $this->id_planning; }
    public function getIdRegime()      { return $this->id_regime; }
    public function getProgrammeSport(){ return $this->programme_sport; }
    public function getSommeil()       { return $this->sommeil; }
    public function getTitrePlanning() { return $this->titre_planning; }
    public function getDescription()   { return $this->description; }
    public function getStatut()        { return $this->statut; }
    public function getCommentaire()   { return $this->commentaire; }

    // Setters
    public function setIdPlanning($id)     { $this->id_planning     = $id; }
    public function setIdRegime($id)       { $this->id_regime       = $id; }
    public function setProgrammeSport($ps) { $this->programme_sport = $ps; }
    public function setSommeil($s)         { $this->sommeil         = $s; }
    public function setTitrePlanning($t)   { $this->titre_planning  = $t; }
    public function setDescription($d)     { $this->description     = $d; }
    public function setStatut($statut)     { $this->statut          = $statut; }
    public function setCommentaire($c)     { $this->commentaire     = $c; }
}
?>
