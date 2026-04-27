<?php
require_once __DIR__.'/../../../controller/NotificationController.php';
$notifController = new NotificationController();

if (isset($_GET['action']) && $_GET['action'] == 'read_all') {
    $notifController->markAllAsRead();
    header('Location: listNotifications.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'read' && isset($_GET['id'])) {
    $notifController->markAsRead($_GET['id']);
    header('Location: listNotifications.php');
    exit;
}

$notifications = $notifController->getNotifications();
$unreadCount = $notifController->getUnreadCount();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Notifications</title>
  <link rel="stylesheet" href="../../back/assets/back.css" />
  <link rel="stylesheet" href="../../../Produit Locaux/adminproduitlocaux.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
      .notif-card {
          background: white;
          padding: 15px 20px;
          border-radius: 12px;
          margin-bottom:  profile;
          margin-bottom: 15px;
          box-shadow: 0 2px 10px rgba(0,0,0,0.05);
          display: flex;
          align-items: center;
          gap: 15px;
          border-left: 5px solid transparent;
          transition: all 0.3s ease;
      }
      .notif-unread {
          border-left-color: #3498db;
          background: #f7fbff;
      }
      .notif-type-stock_low { border-left-color: #e74c3c; }
      .notif-type-price_drop { border-left-color: #2ecc71; }
      .notif-type-expiration { border-left-color: #f39c12; }
      
      .notif-icon {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
      }
      .notif-content { flex: 1; }
      .notif-message { font-weight: 500; margin-bottom: 5px; }
      .notif-date { font-size: 12px; color: #888; }
      .notif-actions { display: flex; gap: 10px; }
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
      <a href="../produit/listProduit.php" class="menu-item">
        <i data-feather="package"></i>
        <span>Produits</span>
      </a>
      <a href="../movement/listMovement.php" class="menu-item">
        <i data-feather="activity"></i>
        <span>Mouvements Stock</span>
      </a>
      <a href="listNotifications.php" class="menu-item active">
        <i data-feather="bell"></i>
        <span>Notifications</span>
        <?php if($unreadCount > 0): ?>
            <span class="pl-badge pl-badge-low" style="margin-left:auto"><?= $unreadCount ?></span>
        <?php endif; ?>
      </a>
    </nav>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <h2>Notifications & Alertes</h2>
      </div>
      <div class="topbar-right">
          <a href="listNotifications.php?action=read_all" class="pl-btn-icon pl-btn-edit">Tout marquer comme lu</a>
      </div>
    </header>

    <main class="dashboard-content">
      <section class="page-header">
        <div>
          <span class="section-badge">Suivi intelligent</span>
          <h1>Notifications Systèmes</h1>
        </div>
      </section>

      <section class="notif-list">
        <?php foreach ($notifications as $notif): ?>
        <div class="notif-card <?= $notif['is_read'] ? '' : 'notif-unread' ?> notif-type-<?= $notif['type'] ?>">
            <div class="notif-icon" style="background: <?= $notif['type'] == 'stock_low' ? '#ffeded' : ($notif['type'] == 'price_drop' ? '#ebffef' : '#fff9eb') ?>">
                <i data-feather="<?= $notif['type'] == 'stock_low' ? 'alert-triangle' : ($notif['type'] == 'price_drop' ? 'trending-down' : 'clock') ?>" 
                   style="color: <?= $notif['type'] == 'stock_low' ? '#e74c3c' : ($notif['type'] == 'price_drop' ? '#2ecc71' : '#f39c12') ?>"></i>
            </div>
            <div class="notif-content">
                <div class="notif-message"><?= htmlspecialchars($notif['message']) ?></div>
                <div class="notif-date"><?= htmlspecialchars($notif['date_created']) ?></div>
            </div>
            <div class="notif-actions">
                <?php if(!$notif['is_read']): ?>
                    <a href="listNotifications.php?action=read&id=<?= $notif['id'] ?>" class="pl-btn-icon pl-btn-edit">Lire</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($notifications)): ?>
            <div style="text-align:center; padding: 50px; color: #888;">
                <i data-feather="check-circle" style="width: 48px; height: 48px; margin-bottom: 15px;"></i>
                <p>Aucune notification pour le moment.</p>
            </div>
        <?php endif; ?>
      </section>
    </main>
  </div>
  <script>feather.replace();</script>
</body>
</html>
