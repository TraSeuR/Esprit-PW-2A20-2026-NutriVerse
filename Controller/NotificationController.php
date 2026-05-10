<?php
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../Model/Notification.php';

class NotificationController {
    public function getNotifications(): array {
        $db = config::getConnexion();
        try {
            $query = $db->query("SELECT * FROM notifications WHERE is_deleted = 0 ORDER BY date_created DESC");
            return $query->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function getUnreadCount(): int {
        $db = config::getConnexion();
        try {
            $query = $db->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0 AND is_deleted = 0");
            return (int)$query->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

    public function addNotification(Notification $notif): void {
        $db = config::getConnexion();
        try {
            $query = $db->prepare(
                "INSERT INTO notifications (message, type, id_related) VALUES (:msg, :type, :id)"
            );
            $query->execute([
                'msg' => $notif->getMessage(),
                'type' => $notif->getType(),
                'id' => $notif->getIdRelated()
            ]);
        } catch (Exception $e) {
            // Silently fail or log for background tasks
        }
    }

    public function addNotificationUnique(Notification $notif): void {
        $db = config::getConnexion();
        try {
            // Check if any notification (even deleted/read) for the same type and id_related exists
            $check = $db->prepare("SELECT id FROM notifications WHERE type = :type AND id_related = :id");
            $check->execute(['type' => $notif->getType(), 'id' => $notif->getIdRelated()]);
            if ($check->fetch()) {
                return; // Already exists, don't recreate even if deleted
            }
            $this->addNotification($notif);
        } catch (Exception $e) { }
    }

    public function markAsRead(int $id): void {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id");
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function markAllAsRead(): void {
        $db = config::getConnexion();
        try {
            $db->query("UPDATE notifications SET is_read = 1");
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function deleteNotification(int $id): void {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("UPDATE notifications SET is_deleted = 1 WHERE id = :id");
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function deleteAllNotifications(): void {
        $db = config::getConnexion();
        try {
            $db->query("UPDATE notifications SET is_deleted = 1");
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
}
?>
