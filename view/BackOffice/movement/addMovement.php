<?php
require_once __DIR__.'/../../../Controller/MovementController.php';
require_once __DIR__.'/../../../Controller/ProduitController.php';
require_once __DIR__.'/../../../Model/Movement.php';

$produitController = new ProduitController();
$produits = $produitController->getProduits();

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_produit = trim($_POST['id_produit']);
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $type_mouvement = trim($_POST['type_mouvement']);
    $quantite = trim($_POST['quantite']);

    // PHP Validation
    if (empty($id_produit) || empty($titre) || empty($type_mouvement) || empty($quantite)) {
        $error = "Les champs Produit, Titre, Type et Quantité sont obligatoires.";
    } elseif (!filter_var($quantite, FILTER_VALIDATE_INT) || $quantite <= 0) {
        $error = "La quantité doit être un entier strictement positif.";
    } elseif (!in_array($type_mouvement, ['achat', 'vente', 'ajout_stock', 'alerte_stock'])) {
        $error = "Type de mouvement invalide.";
    } else {
        $movement = new Movement((int)$id_produit, $titre, $description, $type_mouvement, (int)$quantite);
        $movementController = new MovementController();
        $movementController->addMovement($movement);
        header('Location: listMovement.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ajouter Mouvement - NutriVerse</title>
  <link rel="stylesheet" href="../assets/back.css" />
  <link rel="stylesheet" href="../assets/adminproduitlocaux.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
      .error-msg { background: #ffdddd; color: #d8000c; padding: 10px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #d8000c;}
      .error-text { color: #e74c3c; font-size: 0.85rem; margin-top: 5px; display: block; height: 1.2rem; }
      .pl-panel { padding: 40px; border-radius: 28px; }
      .pl-form-group input, .pl-form-group select, .pl-form-group textarea { background: #fdfdfd; width: 100%; }
      textarea { border: 1px solid var(--border); border-radius: 14px; padding: 14px; font-family: inherit; }
  </style>
</head>
<body>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
      <div class="brand">
        <img src="../images/logo.png" alt="Logo NutriVerse" class="brand-logo" />
        <div>
          <h2>NutriVerse</h2>
          <p>Back Office</p>
        </div>
      </div>
    </div>
    <nav class="sidebar-menu">
      <a href="../nutri_back.php" class="menu-item">
        <i data-feather="grid"></i>
        <span>Dashboard</span>
      </a>

      <a href="../RECETTE/admin.php" class="menu-item">
        <i data-feather="book-open"></i>
        <span>Recettes</span>
      </a>

      <a href="../../shop.php?action=admin_users" class="menu-item">
        <i data-feather="users"></i>
        <span>Utilisateurs</span>
      </a>

      <a href="../produit/listProduit.php" class="menu-item">
        <i data-feather="package"></i>
        <span>Produits</span>
      </a>

      <a href="listMovement.php" class="menu-item active">
        <i data-feather="activity"></i>
        <span>Mouvements Stock</span>
      </a>

      <a href="../notifications/listNotifications.php" class="menu-item">
        <i data-feather="bell"></i>
        <span>Notifications</span>
      </a>

      <a href="../../shop.php?action=admin_orders" class="menu-item">
        <i data-feather="shopping-cart"></i>
        <span>Commandes</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="activity"></i>
        <span>Suivi Santé</span>
      </a>

      <a href="../programme/admin_dashboard.php" class="menu-item">
        <i data-feather="heart"></i>
        <span>Programmes</span>
      </a>
    </nav>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <h2>Gestion des Mouvements</h2>
      </div>
    </header>

    <main class="dashboard-content">
      <section class="page-header">
        <div>
          <span class="section-badge" style="background: #eaf8ef; color: #27ae60;">Stock local</span>
          <h1>Ajouter un mouvement de stock</h1>
        </div>
        <a class="mini-btn" href="listMovement.php" style="text-decoration: none; display: flex; align-items: center; gap: 8px; color: #4a148c; border-radius: 12px; padding: 10px 18px; border: 1px solid #eee; background: white;">
           <i data-feather="arrow-left" style="width: 18px;"></i>
           Retour à la liste
        </a>
      </section>

      <?php if(!empty($error)): ?>
          <div class="error-msg"><?= $error ?></div>
      <?php endif; ?>

      <section class="pl-panel">
        <form id="movementForm" action="addMovement.php" method="POST">
          <div class="pl-form-grid" style="display: flex; flex-direction: column; gap: 20px;">
            <div class="pl-form-group">
              <label>Produit Concerné (*)</label>
              <select name="id_produit" id="id_produit">
                  <option value="">-- Choisir un produit --</option>
                  <?php foreach($produits as $p): ?>
                      <option value="<?= $p['idproduit'] ?>" <?= (isset($_POST['id_produit']) && $_POST['id_produit'] == $p['idproduit']) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($p['nom']) ?> (Stock actuel: <?= $p['quantite_stock'] ?>)
                      </option>
                  <?php endforeach; ?>
              </select>
              <span id="err-id_produit" class="error-text"></span>
            </div>
            
            <div class="pl-form-group">
              <label>Titre de l'opération (*)</label>
              <input type="text" name="titre" id="titre" value="<?= isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : '' ?>" />
              <span id="err-titre" class="error-text"></span>
            </div>

            <div class="pl-form-group">
              <label>Type de mouvement (*)</label>
              <select name="type_mouvement" id="type_mouvement">
                  <option value="ajout_stock" <?= (isset($_POST['type_mouvement']) && $_POST['type_mouvement'] == 'ajout_stock') ? 'selected' : '' ?>>Ajout de Stock (+)</option>
                  <option value="achat" <?= (isset($_POST['type_mouvement']) && $_POST['type_mouvement'] == 'achat') ? 'selected' : '' ?>>Achat Fournisseur (+)</option>
                  <option value="vente" <?= (isset($_POST['type_mouvement']) && $_POST['type_mouvement'] == 'vente') ? 'selected' : '' ?>>Vente Client (-)</option>
                  <option value="alerte_stock" <?= (isset($_POST['type_mouvement']) && $_POST['type_mouvement'] == 'alerte_stock') ? 'selected' : '' ?>>Alerte (-)</option>
              </select>
              <span id="err-type_mouvement" class="error-text"></span>
            </div>

            <div class="pl-form-group">
              <label>Description</label>
              <textarea name="description" id="description" rows="3"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
              <span id="err-description" class="error-text"></span>
            </div>

            <div class="pl-form-group">
              <label>Quantité Impactée (*)</label>
              <input type="text" name="quantite" id="quantite" value="<?= isset($_POST['quantite']) ? htmlspecialchars($_POST['quantite']) : '' ?>" placeholder="Ex: 5" />
              <span id="err-quantite" class="error-text"></span>
            </div>

            <div class="pl-form-actions">
              <button type="submit" class="export-btn" style="background: #27ae60; padding: 14px 35px; border-radius: 12px; font-weight: 600; width: fit-content;">Enregistrer le Mouvement & Mettre à jour le stock</button>
            </div>
          </div>
        </form>
      </section>
    </main>
  </div>
  <script>
    feather.replace();
    if(document.getElementById("movementForm")) {
        document.getElementById("movementForm").addEventListener('submit', function(e) {
            let isValid = true;
            
            // Nettoyage des erreurs
            document.querySelectorAll('.error-text').forEach(el => el.textContent = "");

            // Récupération des valeurs
            const idProduit = document.getElementById("id_produit").value.trim();
            const titre = document.getElementById("titre").value.trim();
            const typeMvt = document.getElementById("type_mouvement").value.trim();
            const quantite = document.getElementById("quantite").value.trim();

            // Validation Produit
            if (idProduit === "") {
                document.getElementById("err-id_produit").textContent = "Veuillez sélectionner le produit concerné.";
                isValid = false;
            }

            // Validation Titre
            if (titre === "") {
                document.getElementById("err-titre").textContent = "Le titre de l'opération est obligatoire.";
                isValid = false;
            }

            // Validation Type
            if (typeMvt === "") {
                document.getElementById("err-type_mouvement").textContent = "Veuillez sélectionner le type de mouvement.";
                isValid = false;
            }

            // Validation Quantité
            if (quantite === "" || isNaN(quantite) || !Number.isInteger(Number(quantite)) || parseInt(quantite) <= 0) {
                document.getElementById("err-quantite").textContent = "La quantité impactée doit être un entier strictement positif.";
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
            return isValid;
        });
    }
  </script>
</body>
</html>
