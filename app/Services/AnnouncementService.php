<?php

namespace App\Services;
use mysqli;

class AnnouncementService {
    
    
    /*================================================
                DB connection
    ================================================*/
    private mysqli $db;
    public function __construct(mysqli $db) { $this->db = $db; }
    
    

    /*================================================
                Announcements
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


    // Create announcement
    public function createAnnouncement( int $userId, string $title, array $targets,
        string $message, ?int $courseId = null ): void {

        // Default all roles to 0
        $roles = [  'principal' => 0,  'teacher'   => 0,  'student'   => 0, 'member'    => 0, 'guardian'  => 0 ];

        // Set selected roles to 1
        foreach ($targets as $role) {
            if (array_key_exists($role, $roles)) {
                $roles[$role] = 1;
            }
        }

        $stmt = $this->db->prepare("
            INSERT INTO announcements
            (title, content, created_by,
             principal, teacher, student, member, guardian, course_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(  "ssiiiiiii", $title, $message, $userId,
            $roles['principal'], $roles['teacher'], $roles['student'],
            $roles['member'], $roles['guardian'], $courseId
        );

        $stmt->execute();
    }

    
    // Get announcements
    public function getUserAnnouncements( int $userId, string $role, ?int $courseId = null,
        string $filter = 'unread', int $page = 1, int $perPage = 10 ): array {

        $allowedRoles = ['principal', 'teacher', 'student', 'member', 'guardian'];
        $allowedFilters = ['all', 'unread', 'read'];

        if (!in_array($role, $allowedRoles)) {
            return [];
        }

        if (!in_array($filter, $allowedFilters)) {
            $filter = 'all';
        }

        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT 
            a.*,
            ar.read_at,
            CASE 
                WHEN ar.read_at IS NULL THEN 0
                ELSE 1
            END AS is_read
            FROM announcements a
            LEFT JOIN announcement_reads ar
                ON ar.announcement_id = a.id
                AND ar.user_id = ?
            WHERE 
                a.$role = 1
                AND (
                    a.course_id IS NULL
                    OR (? IS NOT NULL AND a.course_id = ?)
                )
        ";

        // 🔹 Apply read filter
        if ($filter === 'unread') {
            $sql .= " AND ar.read_at IS NULL ";
        }

        if ($filter === 'read') {
            $sql .= " AND ar.read_at IS NOT NULL ";
        }

        $sql .= "
            ORDER BY a.created_at DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiiii", $userId, $courseId, $courseId, $perPage, $offset);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Count announcements for pagination UI
    public function countAnnouncements( int $userId, string $role,
    ?int $courseId = null, string $filter = 'unread'): int {

        $allowedRoles = ['principal', 'teacher', 'student', 'member', 'guardian'];
        $allowedFilters = ['all', 'unread', 'read'];

        if (!in_array($role, $allowedRoles)) {
            return 0;
        }

        if (!in_array($filter, $allowedFilters)) {
            $filter = 'unread';
        }

        $sql = "
            SELECT COUNT(*) as total
            FROM announcements a
            LEFT JOIN announcement_reads ar
                ON ar.announcement_id = a.id
                AND ar.user_id = ?
            WHERE 
                a.$role = 1
                AND (
                    a.course_id IS NULL
                    OR a.course_id = ?
                )
        ";

        if ($filter === 'unread') {
            $sql .= " AND ar.read_at IS NULL ";
        }

        if ($filter === 'read') {
            $sql .= " AND ar.read_at IS NOT NULL ";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $userId, $courseId);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        return (int) $result['total'];
    }


    // Mark announcement as read
    public function markAsRead(int $announcementId, int $userId): void {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO announcement_reads (announcement_id, user_id)
            VALUES (?, ?)
        ");

        $stmt->bind_param("ii", $announcementId, $userId);
        $stmt->execute();
    }
    
    
    // Delete single announcement
    public function deleteAnnouncement(int $announcementId, int $userId): void {
        $stmt = $this->db->prepare("
            DELETE FROM announcements
            WHERE id = ?
            AND created_by = ?
        ");

        $stmt->bind_param("ii", $announcementId, $userId);
        $stmt->execute();
    }


}
