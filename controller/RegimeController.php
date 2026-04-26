<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/Regime.php';

class RegimeController
{
    private $db;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Gère la requête pour la création/modification d'un régime (Étape 1)
     */
    public function handleRequest(&$id_regime = null, $source = null)
    {
        if (!$id_regime && isset($_SESSION['last_id_regime'])) {
            $id_regime = $_SESSION['last_id_regime'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = null;
            try {
                // IMPORTANT FIX: Vérifier si le régime existe AVANT de faire un UPDATE
                $existing = $id_regime ? $this->getRegime($id_regime) : null;

                if ($existing) {
                    if ($this->updateRegime($id_regime, $_POST)) {
                        $id = $id_regime;
                    }
                } else {
                    // Si l'ID est fourni mais introuvable, on force une NOUVELLE création
                    $id = $this->createRegime($_POST);
                }

                if ($id && $id != "0") {
                    $_SESSION['last_id_regime'] = $id;
                    $id_regime = $id;
                    if ($source === 'back') {
                        header("Location: ../BackOffice/admin_dashboard.php");
                    } else {
                        header("Location: add_planning.php?id_regime=" . $id);
                    }
                    exit();
                } else {
                    echo "<div style='background:red; color:white; padding: 20px;'>Erreur: Impossible de sauvegarder. Vérifiez les champs ou la base de données. ID généré: " . var_export($id, true) . "</div>";
                }
            } catch (Exception $e) {
                echo "<div style='background:red; color:white; padding: 20px; z-index: 9999; position: relative;'>Exception PHP/SQL: " . $e->getMessage() . "</div>";
            }
        }

        return $id_regime ? $this->getRegime($id_regime) : null;
    }

    // ─── READ ─────────────────────────────────────────────────────────────────

    public function listRegimes()
    {
        $stmt = $this->db->query("SELECT * FROM regime_alimentaire ORDER BY id_regime DESC");
        $regimes = [];
        while ($row = $stmt->fetch()) {
            $regimes[] = $this->hydrate($row);
        }
        return $regimes;
    }

    public function getRegime($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM regime_alimentaire WHERE id_regime = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    // ─── CREATE ───────────────────────────────────────────────────────────────

    public function createRegime($data)
    {
        // Encodage des heures de la semaine si elles existent
        $heures_semaine = null;
        if (isset($data['heures_semaine']) && is_array($data['heures_semaine'])) {
            $heures_semaine = json_encode($data['heures_semaine']);
        } elseif (isset($data['heures_semaine'])) {
            $heures_semaine = $data['heures_semaine']; // Si c'est déjà un JSON via Ajax 
        }

        $sql = "INSERT INTO regime_alimentaire (nom, type, calorie_jour, proteine, glucide, lipides, description, heures_semaine)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([
            $data['nom'],
            $data['type'],
            $data['calorie_jour'],
            $data['proteine'],
            $data['glucide'],
            $data['lipides'],
            $data['description'] ?? '',
            $heures_semaine
        ]);
        return $res ? $this->db->lastInsertId() : false;
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    public function updateRegime($id, $data)
    {
        // Encodage des heures de la semaine si elles existent
        $heures_semaine = null;
        if (isset($data['heures_semaine']) && is_array($data['heures_semaine'])) {
            $heures_semaine = json_encode($data['heures_semaine']);
        } elseif (isset($data['heures_semaine'])) {
            $heures_semaine = $data['heures_semaine'];
        }

        $sql = "UPDATE regime_alimentaire
                SET nom = ?, type = ?, calorie_jour = ?, proteine = ?, glucide = ?, lipides = ?, description = ?, heures_semaine = ?
                WHERE id_regime = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nom'],
            $data['type'],
            $data['calorie_jour'],
            $data['proteine'],
            $data['glucide'],
            $data['lipides'],
            $data['description'] ?? '',
            $heures_semaine,
            $id
        ]);
    }

    // ─── DELETE ───────────────────────────────────────────────────────────────

    public function deleteRegime($id)
    {
        // Supprimer d'abord les plannings associés pour éviter les erreurs de contrainte
        $stmt1 = $this->db->prepare("DELETE FROM planning WHERE id_regime = ?");
        $stmt1->execute([$id]);

        $stmt2 = $this->db->prepare("DELETE FROM regime_alimentaire WHERE id_regime = ?");
        return $stmt2->execute([$id]);
    }

    // ─── HELPER ───────────────────────────────────────────────────────────────

    private function hydrate($row)
    {
        return new Regime(
            $row['id_regime'],
            $row['nom'],
            $row['type'],
            $row['calorie_jour'],
            $row['proteine'],
            $row['glucide'],
            $row['lipides'],
            $row['description'],
            $row['heures_semaine'] ?? null
        );
    }
}
?>