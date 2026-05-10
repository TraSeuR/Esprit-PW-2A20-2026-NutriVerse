<?php

class Movement {
    private ?int $id = null;
    private ?int $id_produit = null;
    private ?string $titre = null;
    private ?string $description = null;
    private ?string $type_mouvement = null;
    private ?int $quantite = null;

    public function __construct(?int $id_produit = null, ?string $titre = null, ?string $description = null, ?string $type_mouvement = null, ?int $quantite = null) {
        $this->id_produit = $id_produit;
        $this->titre = $titre;
        $this->description = $description;
        $this->type_mouvement = $type_mouvement;
        $this->quantite = $quantite;
    }

    public function getId(): ?int { return $this->id; }
    public function getIdProduit(): ?int { return $this->id_produit; }
    public function getTitre(): ?string { return $this->titre; }
    public function getDescription(): ?string { return $this->description; }
    public function getTypeMouvement(): ?string { return $this->type_mouvement; }
    public function getQuantite(): ?int { return $this->quantite; }

    public function setId(?int $id): void { $this->id = $id; }
    public function setIdProduit(?int $id_produit): void { $this->id_produit = $id_produit; }
    public function setTitre(?string $titre): void { $this->titre = $titre; }
    public function setDescription(?string $desc): void { $this->description = $desc; }
    public function setTypeMouvement(?string $type): void { $this->type_mouvement = $type; }
    public function setQuantite(?int $qte): void { $this->quantite = $qte; }
}
?>
