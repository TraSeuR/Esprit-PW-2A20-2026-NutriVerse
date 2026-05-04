-

<?php
require_once __DIR__ . '/../model/LivraisonModel.php';

class LivraisonController
{
    private $pdo;

    // C'est ici qu'on prépare le contrôleur qui gère les livraisons.
    public function __construct($pdo)
    {
        // On enregistre la connexion à la base de données.
        $this->pdo = $pdo;
    }

    // LISTER LES LIVRAISONS AVEC JOINTURE
    // Cette fonction permet d'afficher la liste de toutes les livraisons prévues.
    public function listLivraisons()
    {
        // On demande à la base de données les livraisons avec le nom du client et le prix total.
        $sql = "SELECT l.*, c.nom_client, c.montant_total 
                FROM livraison l 
                JOIN commande c ON l.id_commande = c.id_commande 
                ORDER BY l.date_livraison DESC";
                
        $stmt = $this->pdo->query($sql);
        $livraisons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // On affiche la page des livraisons pour l'administrateur.
        require __DIR__ . '/../view/back/livraisons.php';
    }

    // AFFICHER LE FORMULAIRE D'ASSIGNATION DE LIVREUR
    // Cette fonction prépare le formulaire pour donner une livraison à un livreur.
    public function assignForm()
    {
        // On récupère l'identifiant de la livraison.
        $id_livraison = (int)$_GET['id'];
        
        // (Note: Cette fonction pourra être complétée plus tard pour afficher une page spéciale).
    }

    // METTRE A JOUR LE LIVREUR OU LE STATUT
    // Cette fonction permet de changer le livreur ou l'état de la livraison (ex: "en cours").
    public function updateLivraison()
    {
        // Si on a envoyé de nouvelles informations :
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_livraison = (int)$_POST['id_livraison'];
            $nom_livreur = $_POST['nom_livreur'];
            $status_livraison = $_POST['status_livraison'];

            // On met à jour la livraison dans la base de données.
            $sql = "UPDATE livraison SET nom_livreur = ?, statut_livraison = ? WHERE id_livraison = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nom_livreur, $status_livraison, $id_livraison]);

            // On retourne à la liste des livraisons.
            header('Location: index.php?action=admin_livraisons');
            exit();
        }
    }

    // SUPPRIMER UNE LIVRAISON
    // Cette fonction permet de supprimer une livraison de la liste.
    public function deleteLivraison()
    {
        // Si on a l'identifiant de la livraison à supprimer :
        if (isset($_GET['id'])) {
            $id_livraison = (int)$_GET['id'];
            // On la supprime de la base de données.
            $sql = "DELETE FROM livraison WHERE id_livraison = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_livraison]);
        }
        // On retourne à la liste des livraisons.
        header('Location: index.php?action=admin_livraisons');
        exit();
    }
}
