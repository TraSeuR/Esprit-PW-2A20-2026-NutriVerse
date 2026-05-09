<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/Planning.php';

class PlanningController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Gère la requête pour la création d'un planning (Étape 2)
     */
    public function handleRequest($id_regime)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->createPlanning($_POST)) {
                header("Location: summary.php?id_regime=" . $id_regime);
                exit();
            }
        }
    }

    // ─── READ ─────────────────────────────────────────────────────────────────

    public function listPlannings()
    {
        $stmt = $this->db->query("SELECT * FROM planning");
        $plannings = [];
        while ($row = $stmt->fetch()) {
            $plannings[] = $this->hydrate($row);
        }
        return $plannings;
    }

    /**
     * Récupère tous les plannings avec le nom du régime associé
     * via une INNER JOIN entre les tables planning et regime_alimentaire.
     * backkkkkk
     * 

     */
    public function listPlanningsWithRegimes()
    {
        $sql = "SELECT 
                    planning.id_planning,
                    planning.id_regime,
                    planning.titre_planning,
                    planning.programme_sport,
                    planning.sommeil,
                    planning.description,
                    planning.statut,
                    planning.commentaire,
                    regime_alimentaire.nom AS nom_regime,
                    regime_alimentaire.type AS regime_type
                FROM planning
                INNER JOIN regime_alimentaire 
                    ON planning.id_regime = regime_alimentaire.id_regime
                ORDER BY planning.id_planning DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * JOIN : (view_ready_plannings.php).
     */
    public function listAcceptedPlanningsWithRegimes()
    {
        $sql = "SELECT 
                    planning.id_planning,
                    planning.id_regime,
                    planning.titre_planning,
                    planning.programme_sport,
                    planning.sommeil,
                    planning.description,
                    planning.statut,
                    regime_alimentaire.nom AS regime_nom,
                    regime_alimentaire.calorie_jour,
                    regime_alimentaire.type AS regime_type,
                    regime_alimentaire.proteine,
                    regime_alimentaire.glucide,
                    regime_alimentaire.lipides,
                    regime_alimentaire.heures_semaine
                FROM planning
                INNER JOIN regime_alimentaire 
                    ON planning.id_regime = regime_alimentaire.id_regime
                WHERE planning.statut = 'accepte'
                ORDER BY planning.id_planning DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }


    public function getPlanningWithRegime($id_regime)
    {
        $sql = "SELECT 
                    planning.id_planning,
                    planning.id_regime,
                    planning.titre_planning,
                    planning.programme_sport,
                    planning.sommeil,
                    planning.description,
                    planning.statut,
                    regime_alimentaire.nom,
                    regime_alimentaire.type,
                    regime_alimentaire.calorie_jour,
                    regime_alimentaire.proteine,
                    regime_alimentaire.glucide,
                    regime_alimentaire.lipides,
                    regime_alimentaire.heures_semaine
                FROM planning
                INNER JOIN regime_alimentaire 
                    ON planning.id_regime = regime_alimentaire.id_regime
                WHERE planning.id_regime = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_regime]);
        return $stmt->fetch();
    }

    public function getPlanningByRegime($id_regime)
    {
        $stmt = $this->db->prepare("SELECT * FROM planning WHERE id_regime = ? LIMIT 1");
        $stmt->execute([$id_regime]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function getPlanningById($id_planning)
    {
        $stmt = $this->db->prepare("SELECT * FROM planning WHERE id_planning = ? LIMIT 1");
        $stmt->execute([$id_planning]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    // ─── CREATE ────────────

    public function createPlanning($data)
    {
        $sql = "INSERT INTO planning (id_regime, programme_sport, sommeil, titre_planning, description, statut, commentaire)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id_regime'],
            $data['programme_sport'],
            $data['sommeil'],
            $data['titre_planning'],
            $data['description'] ?? '',
            'en_attente',
            null
        ]);
    }

    public function createPlanningAdmin($data)
    {
        $sql = "INSERT INTO planning (id_regime, programme_sport, sommeil, titre_planning, description, statut, commentaire)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id_regime'],
            $data['programme_sport'],
            $data['sommeil'],
            $data['titre_planning'],
            $data['description'] ?? '',
            'accepte',   // Admin : validation automatique
            null
        ]);
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    public function updatePlanning($id, $data)
    {
        $sql = "UPDATE planning
                SET programme_sport = ?, sommeil = ?, titre_planning = ?, description = ?
                WHERE id_planning = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['programme_sport'],
            $data['sommeil'],
            $data['titre_planning'],
            $data['description'] ?? '',
            $id
        ]);
    }

    public function updateStatut($id, $statut)
    {
        $stmt = $this->db->prepare("UPDATE planning SET statut = ? WHERE id_planning = ?");
        return $stmt->execute([$statut, $id]);
    }

    public function updateStatutByRegime($id_regime, $statut)
    {
        $stmt = $this->db->prepare("UPDATE planning SET statut = ? WHERE id_regime = ?");
        return $stmt->execute([$statut, $id_regime]);
    }

    // ─── DELETE ───────────────────────────────────────────────────────────────

    public function deletePlanning($id)
    {
        $stmt = $this->db->prepare("DELETE FROM planning WHERE id_planning = ?");
        return $stmt->execute([$id]);
    }

    // ─── HELPER ───────────────────────────────────────────────────────────────

    private function hydrate($row)
    {
        return new Planning(
            $row['id_planning'],
            $row['id_regime'],
            $row['programme_sport'],
            $row['sommeil'],
            $row['titre_planning'],
            $row['description'],
            $row['statut'],
            $row['commentaire']
        );
    }
}
?>