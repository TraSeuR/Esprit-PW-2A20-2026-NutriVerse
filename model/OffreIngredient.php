<?php
require_once __DIR__ . '/../config/database.php';

class OffreIngredient {
    private $id_offre;
    private $id_user;
    private $ingredient;
    private $categorie;
    private $quantite;
    private $unite_mesure;
    private $localisation;
    private $date_publication;
    private $etat;
    private $type_offre;
    private $description;
    private $latitude;
    private $longitude;

    public function __construct($id_user = null, $ingredient = null, $categorie = null, $quantite = null, $unite_mesure = null, $localisation = null, $etat = 'disponible', $type_offre = 'échange', $description = null, $id_offre = null, $latitude = null, $longitude = null) {
        $this->id_offre = $id_offre;
        $this->id_user = $id_user;
        $this->ingredient = $ingredient;
        $this->categorie = $categorie;
        $this->quantite = $quantite;
        $this->unite_mesure = $unite_mesure;
        $this->localisation = $localisation;
        $this->etat = $etat;
        $this->type_offre = $type_offre;
        $this->description = $description;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    // Getters
    public function getIdOffre() { return $this->id_offre; }
    public function getIdUser() { return $this->id_user; }
    public function getIngredient() { return $this->ingredient; }
    public function getCategorie() { return $this->categorie; }
    public function getQuantite() { return $this->quantite; }
    public function getUniteMesure() { return $this->unite_mesure; }
    public function getLocalisation() { return $this->localisation; }
    public function getDatePublication() { return $this->date_publication; }
    public function getEtat() { return $this->etat; }
    public function getTypeOffre() { return $this->type_offre; }
    public function getDescription() { return $this->description; }
    public function getLatitude() { return $this->latitude; }
    public function getLongitude() { return $this->longitude; }

    // Setters
    public function setIdOffre($id) { $this->id_offre = $id; }
    public function setIdUser($id) { $this->id_user = $id; }
    public function setIngredient($val) { $this->ingredient = $val; }
    public function setCategorie($val) { $this->categorie = $val; }
    public function setQuantite($val) { $this->quantite = $val; }
    public function setUniteMesure($val) { $this->unite_mesure = $val; }
    public function setLocalisation($val) { $this->localisation = $val; }
    public function setDatePublication($val) { $this->date_publication = $val; }
    public function setEtat($val) { $this->etat = $val; }
    public function setTypeOffre($val) { $this->type_offre = $val; }
    public function setDescription($val) { $this->description = $val; }
    public function setLatitude($val) { $this->latitude = $val; }
    public function setLongitude($val) { $this->longitude = $val; }
}
?>
