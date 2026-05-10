<?php
require_once __DIR__.'/../../../Controller/NotificationController.php';
$notifController = new NotificationController();

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'read_all') {
        $notifController->markAllAsRead();
    } elseif ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $notifController->deleteNotification((int)$_GET['id']);
    } elseif ($_GET['action'] == 'delete_all') {
        $notifController->deleteAllNotifications();
    }
    exit;
}

$notifs = $notifController->getNotifications();

// Only limit if not showing all
if (!isset($_GET['show']) || $_GET['show'] !== 'all') {
    $notifs = array_slice($notifs, 0, 8); // Increased slightly
}

if (empty($notifs)) {
    echo '<p style="padding: 20px; text-align: center; color: #888;">Aucune notification.</p>';
    exit;
}
?>
<style>
    .notif-card {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .notif-card:hover { background-color: #fafafa; }
    .notif-card.unread { background-color: #f0f9ff; border-left: 3px solid #3498db; }
    
    .notif-card.type-stock_low { border-left: 3px solid #f1c40f; }
    .notif-card.type-price_drop { border-left: 3px solid #2ecc71; }
    .notif-card.type-expiration { border-left: 3px solid #e67e22; }

    .notif-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        font-weight: 600;
        color: #444;
    }
    .notif-time { font-size: 11px; color: #999; font-weight: 400; }
    .notif-body { font-size: 13px; color: #666; line-height: 1.4; }
    .notif-delete-btn {
        background: none;
        border: none;
        color: #ccc;
        cursor: pointer;
        font-size: 18px;
        padding: 0;
        line-height: 1;
        transition: color 0.2s;
    }
    .notif-delete-btn:hover { color: #e74c3c; }
</style>

<?php foreach ($notifs as $n): 
    $typeLabel = 'Notification';
    $icon = '🔔';
    if ($n['type'] == 'stock_low') { $typeLabel = 'Stock Faible'; $icon = '⚠️'; }
    elseif ($n['type'] == 'price_drop') { $typeLabel = 'Baisse de prix'; $icon = '📉'; }
    elseif ($n['type'] == 'expiration') { $typeLabel = 'Expiration proche'; $icon = '⏰'; }
?>
<div class="notif-card <?= $n['is_read'] ? '' : 'unread' ?> type-<?= $n['type'] ?>" id="notif-<?= $n['id'] ?>">
    <div class="notif-header">
        <span style="display: flex; align-items: center; gap: 6px;">
            <span><?= $icon ?></span>
            <span><?= $typeLabel ?></span>
        </span>
        <div style="display: flex; gap: 10px; align-items: center;">
            <span class="notif-time"><?= date('H:i', strtotime($n['date_created'])) ?></span>
            <button class="notif-delete-btn" onclick="deleteNvNotif(<?= $n['id'] ?>)" title="Supprimer">×</button>
        </div>
    </div>
    <div class="notif-body">
        <?= htmlspecialchars($n['message']) ?>
    </div>
</div>
<?php endforeach; ?>
