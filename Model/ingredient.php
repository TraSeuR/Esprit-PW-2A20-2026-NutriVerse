<?php

class ingredient
{
    private $nom;
    private $quantite;
    private $unite;

    public function __construct($nom, $quantite, $unite)
    {
        $this->nom = $nom;
        $this->quantite = $quantite;
        $this->unite = $unite;
    }

    public function getNom() { return $this->nom; }
    public function getQuantite() { return $this->quantite; }
    public function getUnite() { return $this->unite; }
}
?>