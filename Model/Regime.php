<?php
class Regime
{
    private $id_regime;
    private $nom;
    private $type;
    private $calorie_jour;
    private $proteine;
    private $glucide;
    private $lipides;
    private $description;
    private $heures_semaine;

    public function __construct(
        $id_regime      = null,
        $nom            = null,
        $type           = null,
        $calorie_jour   = null,
        $proteine       = null,
        $glucide        = null,
        $lipides        = null,
        $description    = null,
        $heures_semaine = null
    ) {
        $this->id_regime      = $id_regime;
        $this->nom            = $nom;
        $this->type           = $type;
        $this->calorie_jour   = $calorie_jour;
        $this->proteine       = $proteine;
        $this->glucide        = $glucide;
        $this->lipides        = $lipides;
        $this->description    = $description;
        $this->heures_semaine = $heures_semaine;
    }

    // Getters
    public function getIdRegime()    { return $this->id_regime; }
    public function getNom()         { return $this->nom; }
    public function getType()        { return $this->type; }
    public function getCalorieJour() { return $this->calorie_jour; }
    public function getProteine()    { return $this->proteine; }
    public function getGlucide()     { return $this->glucide; }
    public function getLipides()       { return $this->lipides; }
    public function getDescription()   { return $this->description; }
    public function getHeuresSemaine() { return $this->heures_semaine; }

    // Setters
    public function setIdRegime($id)      { $this->id_regime    = $id; }
    public function setNom($nom)          { $this->nom          = $nom; }
    public function setType($type)        { $this->type         = $type; }
    public function setCalorieJour($cal)  { $this->calorie_jour = $cal; }
    public function setProteine($p)       { $this->proteine     = $p; }
    public function setGlucide($g)        { $this->glucide      = $g; }
    public function setLipides($l)          { $this->lipides        = $l; }
    public function setDescription($d)      { $this->description    = $d; }
    public function setHeuresSemaine($h)    { $this->heures_semaine = $h; }
}
?>
