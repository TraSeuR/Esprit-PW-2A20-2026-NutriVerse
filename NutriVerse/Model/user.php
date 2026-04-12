<?php

class user
{

    private int $id_user;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $mot_de_passe;
    private string $role;
    private string $remember_me;
    private string $etat_compte;
    private string $date_inscription;

    public function __construct(string $nom, string $prenom, string $email, string $mot_de_passe, string $role, string $remember_me, string $etat_compte)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
        $this->role = $role;
        $this->remember_me = $remember_me;
        $this->etat_compte = $etat_compte;
    }

    public function getId(): int
    {
        return $this->id_user;
    }

    public function setId(int $id): void
    {
        $this->id_user = $id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getMotDePasse(): string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): void
    {
        $this->mot_de_passe = $mot_de_passe;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getRememberMe(): string
    {
        return $this->remember_me;
    }

    public function setRememberMe(string $remember_me): void
    {
        $this->remember_me = $remember_me;
    }

    public function getEtatCompte(): string
    {
        return $this->etat_compte;
    }

    public function setEtatCompte(string $etat_compte): void
    {
        $this->etat_compte = $etat_compte;
    }

    public function getDateInscription(): string
    {
        return $this->date_inscription;
    }

    public function setDateInscription(string $date_inscription): void
    {
        $this->date_inscription = $date_inscription;
    }





}
