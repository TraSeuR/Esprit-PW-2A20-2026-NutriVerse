<?php
require_once __DIR__.'/../../../Controller/ProduitController.php';
require_once __DIR__.'/../../../Model/Produit.php';

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prix = trim($_POST['prix']);
    $quantite = trim($_POST['quantite']);
    $seuil = trim($_POST['seuil']);
    $categorie = trim($_POST['categorie']);
    $date_expiration = trim($_POST['date_expiration']);

    // PHP Validation (No HTML5 validation allowed)
    if (empty($nom) || empty($prix) || empty($quantite) || empty($seuil) || empty($categorie) || empty($date_expiration)) {
        $error = "Veuillez remplir tous les champs obligatoires (y compris la date d'expiration).";
    } elseif (!is_numeric($prix) || $prix <= 0) {
        $error = "Le prix doit être un nombre positif.";
    } elseif (!filter_var($quantite, FILTER_VALIDATE_INT) || $quantite < 0) {
        $error = "La quantité doit être un entier positif.";
    } elseif (!filter_var($seuil, FILTER_VALIDATE_INT) || $seuil < 0) {
        $error = "Le seuil d'alerte doit être un entier positif.";
    } elseif (strtotime($date_expiration) <= strtotime('today')) {
        $error = "La date d'expiration doit être ultérieure à la date d'aujourd'hui.";
    } else {
        $produit = new Produit($nom, (float)$prix, (int)$quantite, (int)$seuil, $categorie, !empty($date_expiration) ? $date_expiration : null);
        $produitController = new ProduitController();
        $new_id = $produitController->addProduit($produit);
        
        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            // Save inside view/back/images/ with predictable name
            $upload_path = __DIR__ . '/../images/produit_' . $new_id . '.' . $ext;
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
  <title>Ajouter Produit - NutriVerse</title>
  <link rel="stylesheet" href="../assets/back.css" />
  <link rel="stylesheet" href="../assets/adminproduitlocaux.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
      .error-msg { background: #ffdddd; color: #d8000c; padding: 10px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #d8000c;}
      .error-text { color: #e74c3c; font-size: 0.85rem; margin-top: 5px; display: block; height: 1.2rem; }
      .pl-panel { padding: 40px; border-radius: 28px; }
      .pl-form-group input, .pl-form-group select { background: #fdfdfd; }
  </style>
<style>
    .user-menu-container { position: relative; }
    .user-dropdown {
      position: absolute; top: 110%; right: 0; width: 180px;
      background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      z-index: 10001; display: none; border: 1px solid #eee; overflow: hidden; padding: 8px;
    }
    .user-dropdown.show { display: block; animation: slideDownUser 0.2s ease; }
    .user-dropdown a {
      display: flex; align-items: center; gap: 10px; padding: 12px;
      color: #e74c3c; text-decoration: none; font-size: 14px; transition: 0.2s;
      text-align: left; font-weight: 600;
    }
    .user-dropdown a:hover { background: #fff5f5; }
    .admin-box { cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 12px; background: white; padding: 6px 16px 6px 6px; border-radius: 20px; border: 1px solid #eee; }
    .admin-box:hover { transform: translateY(-2px); }
    .admin-avatar { width: 40px; height: 40px; border-radius: 50%; background: #27ae60; color: white; display: grid; place-items: center; font-weight: 700; }
    .notif-badge-ui { 
        position: absolute; top: 0; right: 0; background: #ff9800; 
        border-radius: 50%; width: 10px; height: 10px; border: 2px solid #fff;
    }
    @keyframes slideDownUser { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/sidebar.php'; ?>

  <div class="main-content">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/topbar.php'; ?>

    <main class="dashboard-content">
      <section class="page-header">
        <div>
          <span class="section-badge" style="background: #eaf8ef; color: #27ae60;">Nouveau Produit</span>
          <h1>Ajouter un produit local</h1>
        </div>
        <a class="mini-btn" href="listProduit.php" style="text-decoration: none; display: flex; align-items: center; gap: 8px; color: #4a148c; border-radius: 12px; padding: 10px 18px; border: 1px solid #eee; background: white;">
           <i data-feather="arrow-left" style="width: 18px;"></i>
           Retour à la liste
        </a>
      </section>

      <?php if(!empty($error)): ?>
          <div class="error-msg"><?= $error ?></div>
      <?php endif; ?>

      <section class="pl-panel">
        <form id="produitForm" action="addProduit.php" method="POST" enctype="multipart/form-data">
          <div class="pl-form-grid">
            <div class="pl-form-group">
              <label>Nom du produit (*)</label>
              <input type="text" name="nom" id="nom" value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>" />
              <span id="err-nom" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Prix (TND) (*)</label>
              <input type="text" name="prix" id="prix" value="<?= isset($_POST['prix']) ? htmlspecialchars($_POST['prix']) : '' ?>" />
              <span id="err-prix" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Quantité en stock (*)</label>
              <input type="text" name="quantite" id="quantite" value="<?= isset($_POST['quantite']) ? htmlspecialchars($_POST['quantite']) : '' ?>" />
              <span id="err-quantite" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Seuil d'alerte (*)</label>
              <input type="text" name="seuil" id="seuil" value="<?= isset($_POST['seuil']) ? htmlspecialchars($_POST['seuil']) : '' ?>" />
              <span id="err-seuil" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Catégorie (*)</label>
              <select name="categorie" id="categorie">
                  <option value="">Sélectionnez une catégorie</option>
                  <option value="Fruits & légumes" <?= (isset($_POST['categorie']) && $_POST['categorie'] == 'Fruits & légumes') ? 'selected' : '' ?>>🥦 Fruits & légumes</option>
                  <option value="Boulangerie" <?= (isset($_POST['categorie']) && $_POST['categorie'] == 'Boulangerie') ? 'selected' : '' ?>>🥖 Boulangerie</option>
                  <option value="Produits laitiers" <?= (isset($_POST['categorie']) && $_POST['categorie'] == 'Produits laitiers') ? 'selected' : '' ?>>🥛 Produits laitiers</option>
                  <option value="Viandes & poissons" <?= (isset($_POST['categorie']) && $_POST['categorie'] == 'Viandes & poissons') ? 'selected' : '' ?>>🍗 Viandes & poissons</option>
                  <option value="Boissons" <?= (isset($_POST['categorie']) && $_POST['categorie'] == 'Boissons') ? 'selected' : '' ?>>🥤 Boissons</option>
              </select>
              <span id="err-categorie" class="error-text"></span>
            </div>
            <div class="pl-form-group">
              <label>Image du produit (Optionnel)</label>
              <input type="file" name="image" id="image" accept="image/*" />
              <span id="err-image" class="error-text"></span>
            </div>
            <div class="pl-form-group" style="grid-column: 1 / -1;">
              <label>Date d'expiration (*)</label>
              <input type="date" name="date_expiration" id="date_expiration" value="<?= isset($_POST['date_expiration']) ? htmlspecialchars($_POST['date_expiration']) : '' ?>" />
              <span id="err-date_expiration" class="error-text"></span>
            </div>
            <div class="pl-form-actions">
              <button type="submit" class="export-btn" style="background: #27ae60; padding: 14px 35px; border-radius: 12px; font-weight: 600;">Enregistrer</button>
            </div>
          </div>
        </form>
      </section>
    </main>
  </div>
  <script>
    feather.replace();
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
  </script>
</body>
</html>
