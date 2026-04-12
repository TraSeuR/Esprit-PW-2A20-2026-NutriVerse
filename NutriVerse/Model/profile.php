<?php

class profile
{

    private int $id_profile;
    private string $telephone;
    private string $date_naissance;
    private string $sexe;
    private float $poids;
    private float $taille;
    private string $objectif_nutritionnel;
    private string $preference_alimentaire;
    private string $allergies;
    private int $id_user;

    public function __construct(string $telephone, string $date_naissance, string $sexe, float $poids, float $taille, string $objectif_nutritionnel, string $preference_alimentaire, string $allergies, int $id_user)
    {
        $this->telephone = $telephone;
        $this->date_naissance = $date_naissance;
        $this->sexe = $sexe;
        $this->poids = $poids;
        $this->taille = $taille;
        $this->objectif_nutritionnel = $objectif_nutritionnel;
        $this->preference_alimentaire = $preference_alimentaire;
        $this->allergies = $allergies;
        $this->id_user = $id_user;
    }

    public function getId(): int
    {
        return $this->id_user;
    }

    public function setId(int $id): void
    {
        $this->id_user = $id;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getDateNaissance(): string
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(string $date_naissance): void
    {
        $this->date_naissance = $date_naissance;
    }

    public function getSexe(): string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): void
    {
        $this->sexe = $sexe;
    }

    public function getPoids(): float
    {
        return $this->poids;
    }

    public function setPoids(float $poids): void
    {
        $this->poids = $poids;
    }

    public function getTaille(): float
    {
        return $this->taille;
    }

    public function setTaille(float $taille): void
    {
        $this->taille = $taille;
    }

    public function getObjectifNutritionnel(): string
    {
        return $this->objectif_nutritionnel;
    }

    public function setObjectifNutritionnel(string $objectif_nutritionnel): void
    {
        $this->objectif_nutritionnel = $objectif_nutritionnel;
    }

    public function getPreferenceAlimentaire(): string
    {
        return $this->preference_alimentaire;
    }

    public function setPreferenceAlimentaire(string $preference_alimentaire): void
    {
        $this->preference_alimentaire = $preference_alimentaire;
    }

    public function getAllergies(): string
    {
        return $this->allergies;
    }

    public function setAllergies(string $allergies): void
    {
        $this->allergies = $allergies;
    }

    public function getIdUser(): int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): void
    {
        $this->id_user = $id_user;
    }

}
