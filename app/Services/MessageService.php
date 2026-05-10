<?php

namespace App\Services;
use mysqli;

class MessageService {
    
    
    /*================================================
                DB connection
    ================================================*/
    private mysqli $db;
    public function __construct(mysqli $db) { $this->db = $db; }
    
    
    
    /*================================================
               Messages
    ================================================*/
    
    
    public function getMessages(
        int $userId,
        string $filter = 'inbox',
        int $limit = 10,
        int $offset = 0
    ): array {

        switch ($filter) {

            case 'sent':
                $sql = "
                    SELECT m.*, u.firstname, u.surname
                    FROM messages m
                    JOIN users u ON u.id = m.receiver_id
                    WHERE m.sender_id = ?
                      AND m.sender_deleted = 0
                      AND m.parent_id IS NULL
                    ORDER BY m.created_at DESC
                    LIMIT $limit OFFSET $offset
                ";
                break;

            case 'trash':
                $sql = "
                    SELECT m.*
                    FROM messages m
                    WHERE (
                        (m.receiver_id = ? AND m.receiver_deleted = 1)
                        OR
                        (m.sender_id = ? AND m.sender_deleted = 1)
                    )
                    AND m.parent_id IS NULL
                    ORDER BY m.created_at DESC
                    LIMIT $limit OFFSET $offset
                ";
                break;

            default: // inbox
                $sql = "
                    SELECT m.*, u.firstname, u.surname
                    FROM messages m
                    JOIN users u ON u.id = m.sender_id
                    WHERE m.receiver_id = ?
                      AND m.receiver_deleted = 0
                      AND m.parent_id IS NULL
                    ORDER BY m.created_at DESC
                    LIMIT $limit OFFSET $offset
                ";
        }

        $stmt = $this->db->prepare($sql);

        if ($filter === 'trash') {
            $stmt->bind_param("ii", $userId, $userId);
        } else {
            $stmt->bind_param("i", $userId);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    
    
    public function countMessages(int $userId, string $filter = 'inbox'): int {
        switch ($filter) {

            case 'sent':
                $sql = "SELECT COUNT(*) FROM messages
                        WHERE sender_id = ?
                          AND sender_deleted = 0
                          AND parent_id IS NULL";
                break;

            case 'trash':
                $sql = "SELECT COUNT(*) FROM messages
                        WHERE (
                            (receiver_id = ? AND receiver_deleted = 1)
                            OR
                            (sender_id = ? AND sender_deleted = 1)
                        )
                        AND parent_id IS NULL";
                break;

            default:
                $sql = "SELECT COUNT(*) FROM messages
                        WHERE receiver_id = ?
                          AND receiver_deleted = 0
                          AND parent_id IS NULL";
        }

        $stmt = $this->db->prepare($sql);

        if ($filter === 'trash') {
            $stmt->bind_param("ii", $userId, $userId);
        } else {
            $stmt->bind_param("i", $userId);
        }

        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        return (int)$count;
    }

    
    
    
    
    
    
    
    
    
    
    
    
    // Ger folder messages
    public function getFolderMessages(int $userId, string $folder): array {
        switch ($folder) {
            case 'sent':
                $sql = "SELECT * FROM messages 
                        WHERE sender_id = ? 
                        AND sender_deleted = 0
                        AND parent_id IS NULL
                        ORDER BY created_at DESC";
                break;

            case 'trash':
                $sql = "SELECT * FROM messages 
                        WHERE (
                            (receiver_id = ? AND receiver_deleted = 1)
                            OR
                            (sender_id = ? AND sender_deleted = 1)
                        )
                        AND parent_id IS NULL
                        ORDER BY created_at DESC";
                break;

            default: // inbox
                $sql = "SELECT * FROM messages 
                        WHERE receiver_id = ? 
                        AND receiver_deleted = 0
                        AND parent_id IS NULL
                        ORDER BY created_at DESC";
        }

        $stmt = $this->db->prepare($sql);

        if ($folder === 'trash') {
            $stmt->bind_param("ii", $userId, $userId);
        } else {
            $stmt->bind_param("i", $userId);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    // Get thread
    public function getThread(int $threadId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM messages
            WHERE id = ? OR parent_id = ?
            ORDER BY created_at ASC
        ");

        $stmt->bind_param("ii", $threadId, $threadId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Send message
    public function send(
        int $senderId,
        int $receiverId,
        string $subject,
        string $body,
        ?int $parentId = null
    ): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO messages
            (sender_id, receiver_id, subject, body, parent_id)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iissi",
            $senderId,
            $receiverId,
            $subject,
            $body,
            $parentId
        );

        return $stmt->execute();
    }

    // Inbox messages
    public function inbox(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.firstname, u.surname
             FROM messages m
             JOIN users u ON u.id = m.sender_id
             WHERE m.receiver_id = ?
               AND m.receiver_deleted = 0
             ORDER BY m.created_at DESC"
        );

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Sent messages
    public function sent(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.firstname, u.surname
             FROM messages m
             JOIN users u ON u.id = m.receiver_id
             WHERE m.sender_id = ?
               AND m.sender_deleted = 0
             ORDER BY m.created_at DESC"
        );

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // View single message
    public function find(int $messageId, int $userId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM messages
             WHERE id = ?
               AND (sender_id = ? OR receiver_id = ?)
             LIMIT 1"
        );

        $stmt->bind_param("iii", $messageId, $userId, $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    // Mark messages as read
    public function markMessageAsRead(int $messageId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE messages
             SET is_read = 1
             WHERE id = ?"
        );

        $stmt->bind_param("i", $messageId);
        $stmt->execute();
    }

    // Delete messages (soft delete)
    public function deleteMessage(int $messageId, int $userId): void {
        
        // Step 1: Soft delete for this user
        $stmt = $this->db->prepare(
            "UPDATE messages
             SET receiver_deleted = IF(receiver_id = ?, 1, receiver_deleted),
                 sender_deleted = IF(sender_id = ?, 1, sender_deleted)
             WHERE id = ?
               AND (receiver_id = ? OR sender_id = ?)"
        );

        $stmt->bind_param("iiiii", 
            $userId, 
            $userId, 
            $messageId, 
            $userId, 
            $userId
        );

        $stmt->execute();
        $stmt->close();

        // Step 2: Permanently delete if both sides deleted
        $stmt = $this->db->prepare(
            "DELETE FROM messages
             WHERE id = ?
               AND sender_deleted = 1
               AND receiver_deleted = 1"
        );

        $stmt->bind_param("i", $messageId);
        $stmt->execute();
        $stmt->close();
    }

    // Get unread messages
    public function getUnreadMessages( int $userId): int {

        $sql = "SELECT COUNT(*) FROM messages 
                 WHERE receiver_id = ? AND is_read = 0 AND sender_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return (int) $count;
    }
}
