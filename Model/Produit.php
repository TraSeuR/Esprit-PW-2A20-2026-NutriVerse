<?php

class Produit {
    private ?int $idproduit = null;
    private ?string $nom = null;
    private ?float $prix = null;
    private ?float $prix_original = null;
    private ?int $quantite_stock = null;
    private ?int $seuil_alerte = null;
    private ?string $categorie = null;
    private ?string $date_expiration = null;
    private ?string $statut = 'actif';

    public function __construct(?string $nom = null, ?float $prix = null, ?int $quantite_stock = null, ?int $seuil_alerte = null, ?string $categorie = null, ?string $date_expiration = null, ?string $statut = 'actif', ?float $prix_original = null) {
        $this->nom = $nom;
        $this->prix = $prix;
        $this->prix_original = $prix_original ?? $prix;
        $this->quantite_stock = $quantite_stock;
        $this->seuil_alerte = $seuil_alerte;
        $this->categorie = $categorie;
        $this->date_expiration = $date_expiration;
        $this->statut = $statut;
    }

    public function getIdProduit(): ?int { return $this->idproduit; }
    public function getNom(): ?string { return $this->nom; }
    public function getPrix(): ?float { return $this->prix; }
    public function getPrixOriginal(): ?float { return $this->prix_original; }
    public function getQuantiteStock(): ?int { return $this->quantite_stock; }
    public function getSeuilAlerte(): ?int { return $this->seuil_alerte; }
    public function getCategorie(): ?string { return $this->categorie; }
    public function getDateExpiration(): ?string { return $this->date_expiration; }
    public function getStatut(): ?string { return $this->statut; }

    public function setIdProduit(?int $idproduit): void { $this->idproduit = $idproduit; }
    public function setNom(?string $nom): void { $this->nom = $nom; }
    public function setPrix(?float $prix): void { $this->prix = $prix; }
    public function setPrixOriginal(?float $prix): void { $this->prix_original = $prix; }
    public function setQuantiteStock(?int $quantite): void { $this->quantite_stock = $quantite; }
    public function setSeuilAlerte(?int $seuil): void { $this->seuil_alerte = $seuil; }
    public function setCategorie(?string $cat): void { $this->categorie = $cat; }
    public function setDateExpiration(?string $date): void { $this->date_expiration = $date; }
    public function setStatut(?string $statut): void { $this->statut = $statut; }
}
?>
