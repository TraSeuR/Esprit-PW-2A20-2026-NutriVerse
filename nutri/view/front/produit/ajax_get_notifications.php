<?php
require_once __DIR__.'/../../../controller/NotificationController.php';
$notifController = new NotificationController();

if (isset($_GET['action']) && $_GET['action'] == 'read_all') {
    $notifController->markAllAsRead();
    exit;
}

$notifs = $notifController->getNotifications();

// Only limit if not showing all
if (!isset($_GET['show']) || $_GET['show'] !== 'all') {
    $notifs = array_slice($notifs, 0, 5);
}

if (empty($notifs)) {
    echo '<p style="padding: 20px; text-align: center; color: #888;">Aucune notification.</p>';
    exit;
}

foreach ($notifs as $n): 
    $typeLabel = '';
    $icon = '🔔';
    if ($n['type'] == 'stock_low') { $typeLabel = 'Stock Faible'; $icon = '⚠️'; }
    elseif ($n['type'] == 'price_drop') { $typeLabel = 'Baisse de prix'; $icon = '📉'; }
    elseif ($n['type'] == 'expiration') { $typeLabel = 'Expiration proche'; $icon = '⏰'; }
?>
<div class="notif-item <?= $n['is_read'] ? '' : 'unread' ?>">
    <div class="notif-item-header">
        <span><?= $icon ?> <?= $typeLabel ?></span>
        <span><?= date('H:i', strtotime($n['date_created'])) ?></span>
    </div>
    <div class="notif-item-msg">
        <?= htmlspecialchars($n['message']) ?>
    </div>
</div>
<?php endforeach; ?>
