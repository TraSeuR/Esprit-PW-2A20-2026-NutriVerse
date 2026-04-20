<?php

class recette
{
    //private khtrr nodkhlolhom bl gettrs
    
    private string $nom;
    private string $description;
    private string $etapes;
    private string $temps_preparation;
    private string $categorie;
    private string $image;

    public function __construct(
        string $nom,
        string $description,
        string $etapes,
        string $temps_preparation,
        string $categorie,
        string $image
    ) {
        $this->nom = $nom;
        $this->description = $description;
        $this->etapes = $etapes;
        $this->temps_preparation = $temps_preparation;
        $this->categorie = $categorie;
        $this->image = $image;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getEtapes()
    {
        return $this->etapes;
    }

    public function getTemps()
    {
        return $this->temps_preparation;
    }

    public function getCategorie()
    {
        return $this->categorie;
    }

    public function getImage()
    {
        return $this->image;
    }
}

?>