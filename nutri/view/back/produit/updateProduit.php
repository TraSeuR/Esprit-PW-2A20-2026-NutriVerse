<?php
require_once __DIR__.'/../../../controller/ProduitController.php';

$produitController = new ProduitController();
$error = "";
$produitActuel = null;

if (isset($_GET['id'])) {
    $produitActuel = $produitController->getProduitById($_GET['id']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['idproduit'];
    $nom = trim($_POST['nom']);
    $prix = trim($_POST['prix']);
    $quantite = trim($_POST['quantite']);
    $seuil = trim($_POST['seuil']);
    $categorie = trim($_POST['categorie']);
    $date_expiration = trim($_POST['date_expiration']);
    $statut = trim($_POST['statut']);

    if (empty($nom) || empty($prix) || !isset($quantite) || empty($seuil) || empty($categorie) || empty($date_expiration)) {
        $error = "Veuillez remplir tous les champs obligatoires (y compris la date d'expiration).";
        // On remet les valeurs soumises pour éviter de les perdre
        $produitActuel = [
            'idproduit' => $id, 'nom' => $nom, 'prix' => $prix, 'quantite_stock' => $quantite,
            'seuil_alerte' => $seuil, 'categorie' => $categorie, 'date_expiration' => $date_expiration, 'statut' => $statut
        ];
    } elseif (!is_numeric($prix) || $prix <= 0) {
        $error = "Le prix doit être un nombre positif.";
        $produitActuel = $_POST;
        $produitActuel['quantite_stock'] = $quantite;
        $produitActuel['seuil_alerte'] = $seuil;
    } elseif (!filter_var($quantite, FILTER_VALIDATE_INT) && $quantite !== '0' || $quantite < 0) {
        $error = "La quantité doit être un entier (0 ou plus).";
        $produitActuel = $_POST;
        $produitActuel['quantite_stock'] = $quantite;
        $produitActuel['seuil_alerte'] = $seuil;
    } elseif (!filter_var($seuil, FILTER_VALIDATE_INT) && $seuil !== '0' || $seuil < 0) {
        $error = "Le seuil d'alerte doit être un entier positif.";
        $produitActuel = $_POST;
        $produitActuel['quantite_stock'] = $quantite;
        $produitActuel['seuil_alerte'] = $seuil;
    } elseif (strtotime($date_expiration) <= strtotime('today')) {
        $error = "La date d'expiration doit être ultérieure à la date d'aujourd'hui.";
        $produitActuel = $_POST;
        $produitActuel['quantite_stock'] = $quantite;
        $produitActuel['seuil_alerte'] = $seuil;
    } else {
        $p = new Produit($nom, (float)$prix, (int)$quantite, (int)$seuil, $categorie, !empty($date_expiration) ? $date_expiration : null, $statut);
        $produitController->updateProduit($p, $id);
        
        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            // remove existing images to prevent extension overlap
            $existing = glob(__DIR__ . '/../images/produit_' . $id . '.*');
            if ($existing) {
                foreach($existing as $file) { unlink($file); }
            }
            $upload_path = __DIR__ . '/../images/produit_' . $id . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
        }
        
        header('Location: listProduit.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modifier Produit - NutriVerse</title>
  <link rel="stylesheet" href="../../back/assets/back.css" />
  <link rel="stylesheet" href="../../../Produit Locaux/adminproduitlocaux.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
      .error-msg { background: #ffdddd; color: #d8000c; padding: 10px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #d8000c;}
      .error-text { color: #e74c3c; font-size: 0.85rem; margin-top: 5px; display: block; height: 1.2rem; }
  </style>
</head>
<body>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
      <div class="brand">
        <img src="../../back/images/logo.png" alt="Logo NutriVerse" class="brand-logo" />
        <div>
          <h2>NutriVerse</h2>
          <p>Back Office</p>
        </div>
      </div>
    </div>
    <nav class="sidebar-menu">
      <a href="../../back/back.html" class="menu-item">
        <i data-feather="grid"></i>
        <span>Dashboard</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="book-open"></i>
        <span>Recettes</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="users"></i>
        <span>Utilisateurs</span>
      </a>

      <a href="listProduit.php" class="menu-item active">
        <i data-feather="package"></i>
        <span>Produits</span>
      </a>

      <a href="../movement/listMovement.php" class="menu-item">
        <i data-feather="activity"></i>
        <span>Mouvements Stock</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="shopping-cart"></i>
        <span>Commandes</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="activity"></i>
        <span>Suivi Santé</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="heart"></i>
        <span>Programmes</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="settings"></i>
        <span>Paramètres</span>
      </a>
    </nav>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <h2>Gestion des Produits</h2>
      </div>
    </header>

    <main class="dashboard-content">
      <section class="page-header">
        <div>
          <span class="section-badge">Mode Edition</span>
          <h1>Modifier un produit local</h1>
        </div>
        <a class="mini-btn" href="listProduit.php">
           <i data-feather="arrow-left"></i>
           Retour à la liste
        </a>
      </section>

      <?php if(!empty($error)): ?>
          <div class="error-msg"><?= $error ?></div>
      <?php endif; ?>

      <?php if($produitActuel): ?>
      <section class="pl-panel">
        <form id="produitForm" action="updateProduit.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="idproduit" value="<?= htmlspecialchars($produitActuel['idproduit']) ?>" />
          <div class="pl-form-grid">
            <div class="pl-form-group">
              <label>Nom du produit (*)</label>
              <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($produitActuel['nom']) ?>" />
              <span id="err-nom" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Prix (TND) (*)</label>
              <input type="text" name="prix" id="prix" value="<?= htmlspecialchars($produitActuel['prix']) ?>" />
              <span id="err-prix" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Quantité en stock (*)</label>
              <input type="text" name="quantite" id="quantite" value="<?= htmlspecialchars($produitActuel['quantite_stock']) ?>" />
              <span id="err-quantite" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Seuil d'alerte (*)</label>
              <input type="text" name="seuil" id="seuil" value="<?= htmlspecialchars($produitActuel['seuil_alerte']) ?>" />
              <span id="err-seuil" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Catégorie (*)</label>
              <select name="categorie" id="categorie" style="width: 100%; padding: 8px;">
                  <option value="">Sélectionnez une catégorie</option>
                  <option value="Fruits & légumes" <?= ($produitActuel['categorie'] == 'Fruits & légumes') ? 'selected' : '' ?>>🥦 Fruits & légumes</option>
                  <option value="Boulangerie" <?= ($produitActuel['categorie'] == 'Boulangerie') ? 'selected' : '' ?>>🥖 Boulangerie</option>
                  <option value="Produits laitiers" <?= ($produitActuel['categorie'] == 'Produits laitiers') ? 'selected' : '' ?>>🥛 Produits laitiers</option>
                  <option value="Viandes & poissons" <?= ($produitActuel['categorie'] == 'Viandes & poissons') ? 'selected' : '' ?>>🍗 Viandes & poissons</option>
                  <option value="Boissons" <?= ($produitActuel['categorie'] == 'Boissons') ? 'selected' : '' ?>>🥤 Boissons</option>
              </select>
              <span id="err-categorie" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Statut (*)</label>
              <select name="statut" id="statut" style="width: 100%; padding: 8px;">
                  <option value="actif" <?= $produitActuel['statut'] == 'actif' ? 'selected' : '' ?>>Actif</option>
                  <option value="inactif" <?= $produitActuel['statut'] == 'inactif' ? 'selected' : '' ?>>Inactif</option>
              </select>
              <span id="err-statut" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Image du produit (Optionnel)</label>
              <input type="file" name="image" id="image" accept="image/*" />
              <?php 
              $existingImage = glob(__DIR__ . '/../images/produit_' . $produitActuel['idproduit'] . '.*');
              if($existingImage): 
              ?>
                <p style="font-size:12px; color: green;">Une image existe déjà.</p>
              <?php endif; ?>
              <span id="err-image" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Date d'expiration (*)</label>
              <input type="date" name="date_expiration" id="date_expiration" value="<?= htmlspecialchars($produitActuel['date_expiration'] ?? '') ?>" />
              <span id="err-date_expiration" class="error-text"></span>
            </div>
            <div class="pl-form-actions">
              <button type="submit" class="export-btn">Mettre à jour</button>
            </div>
          </div>
        </form>
      </section>
      <?php else: ?>
          <p>Produit introuvable.</p>
      <?php endif; ?>
    </main>
  </div>
  <script>
    feather.replace();
    if(document.getElementById("produitForm")) {
        document.getElementById("produitForm").addEventListener('submit', function(e) {
            let isValid = true;

            // Nettoyage des erreurs précédentes
            document.querySelectorAll('.error-text').forEach(el => el.textContent = "");

            // Récupération des valeurs
            const nom = document.getElementById("nom").value.trim();
            const prix = document.getElementById("prix").value.trim();
            const quantite = document.getElementById("quantite").value.trim();
            const seuil = document.getElementById("seuil").value.trim();
            const categorie = document.getElementById("categorie").value.trim();
            const dateExp = document.getElementById("date_expiration").value.trim();

            // Validation Nom
            if (nom === "") {
                document.getElementById("err-nom").textContent = "Le nom du produit est obligatoire.";
                isValid = false;
            }

            // Validation Prix
            if (prix === "" || isNaN(prix) || parseFloat(prix) <= 0) {
                document.getElementById("err-prix").textContent = "Le prix doit être un nombre strictement positif.";
                isValid = false;
            }

            // Validation Quantité
            if (quantite === "" || isNaN(quantite) || !Number.isInteger(Number(quantite)) || parseInt(quantite) < 0) {
                document.getElementById("err-quantite").textContent = "La quantité doit être un entier positif (0 ou plus).";
                isValid = false;
            }

            // Validation Seuil
            if (seuil === "" || isNaN(seuil) || !Number.isInteger(Number(seuil)) || parseInt(seuil) < 0) {
                document.getElementById("err-seuil").textContent = "Le seuil d'alerte doit être un entier positif (0 ou plus).";
                isValid = false;
            }

            // Validation Catégorie
            if (categorie === "") {
                document.getElementById("err-categorie").textContent = "Veuillez sélectionner une catégorie.";
                isValid = false;
            }

            // Validation Date Expiration
            if (dateExp === "") {
                document.getElementById("err-date_expiration").textContent = "La date d'expiration est obligatoire.";
                isValid = false;
            } else {
                const selectedDate = new Date(dateExp);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (selectedDate <= today) {
                    document.getElementById("err-date_expiration").textContent = "La date d'expiration doit être ultérieure à aujourd'hui.";
                    isValid = false;
                }
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
