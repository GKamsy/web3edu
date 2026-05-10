<?php

namespace App\Services;
use mysqli;

class NotificationService {
    
    
    /*================================================
                DB connection
    ================================================*/
    private mysqli $db;
    public function __construct(mysqli $db) { $this->db = $db; }


    // Get unread messages, notifications, and announcements
    public function getUnreadCounts(int $userId): array {
        return [
            'messages' => $this->counter(
                "SELECT COUNT(*) FROM messages 
                 WHERE receiver_id = ? AND is_read = 0",
                $userId
            ),
            'notifications' => $this->counter(
                "SELECT COUNT(*) FROM notifications 
                 WHERE user_id = ? AND is_read = 0",
                $userId
            ),
            'announcements' => $this->counter(
                "SELECT COUNT(a.id) FROM announcements a
                LEFT JOIN announcement_reads ar
                    ON a.id = ar.announcement_id AND ar.user_id = ?
                WHERE ar.id IS NULL", 
                $userId
            )
        ];
    }


    /**
     * Generic counter method (mysqli correct version)
     */
    private function counter(string $sql, ?int $param = null): int {
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $param);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return (int) $count;
    }
    
    
    
    
    /*================================================
                Notifications
    ================================================*/

    // Find IDs from a particular table using a role
    public function findIdsByRole(string $role, string $table): array {
        $stmt = $this->db->prepare(
            "SELECT id FROM $table WHERE role = ?"
        );

        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return array_column($result, 'id');
    }

    // Create notifications
    public function createNotification($recipientId, $type, $title, $message, $link = null) {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, type, title, message, link)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issss", $recipientId, $type, $title, $message, $link);
        $stmt->execute();
    }
    
    // Get required notifications
    public function getNotifications($userId, $filter = 'unread', $limit = 10, $offset = 0) {
        $where = "user_id = ?";

        if ($filter === 'unread') {
            $where .= " AND is_read = 0";
        } elseif ($filter === 'read') {
            $where .= " AND is_read = 1";
        }

        $stmt = $this->db->prepare("
            SELECT *
            FROM notifications
            WHERE $where
            ORDER BY created_at DESC
            LIMIT $limit OFFSET $offset
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Count notifications for Pagination
    public function countNotifications($userId, $filter = 'unread') {
        $where = "user_id = ?";

        if ($filter === 'unread') {
            $where .= " AND is_read = 0";
        } elseif ($filter === 'read') {
            $where .= " AND is_read = 1";
        }

        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM notifications WHERE $where
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return (int) $count;
    }

    // Mark notification as read
    public function markNotificationAsRead($id, $userId) {
        $stmt = $this->db->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();
    }

    // Delete notification
    public function deleteNotification($id, $userId) {
        $stmt = $this->db->prepare("
            DELETE FROM notifications
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();
    }

    // Find single notification
    public function find(int $notificationId, int $userId): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifications
             WHERE id = ?
               AND user_id = ? 
             LIMIT 1"
        );

        $stmt->bind_param("ii", $notificationId, $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }
}
