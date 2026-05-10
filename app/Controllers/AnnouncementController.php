<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Security;
use App\Services\ViewRenderer;
use App\Services\AnnouncementService;

class AnnouncementController extends Controller {
    
    
    /*================================================
                DB connection
    ================================================*/
    private AnnouncementService $db;
    public function __construct() {
        require_once __DIR__ . '/../../config/config.php';
        $this->db = new AnnouncementService($conn);
    }
    
    
    
    /*================================================
               Announcements
    ================================================*/

    
    public function create(): void {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Authorize the user
        $this->requireAuth();
        
        $this->layout = 'dashboard';
        $this->view('messages/create-announcement', [
            'title' => 'Create Announcement'
        ]);
    }


    // Send announcement
    public function send(): void {
        $this->bootSession();
        $this->requireAuth();

        $userId = (int) $_SESSION['user_id'];
        $targets = $_POST['targets'] ?? [];
        if (!is_array($targets)) {
            $targets = [Security::clean($_POST['targets'])];
        }

        $targets = array_map(fn($v) => Security::clean($v), $targets);

        $title   = Security::clean($_POST['title'] ?? '');
        $message = Security::clean($_POST['message'] ?? '');

        if (empty($targets) || empty($title) || empty($message)) {
            return;
        }

        $this->db->createAnnouncement( $userId, $title, $targets, $message );
        header("Location: /announcements");
        exit;
    }


    // Get unread announcements
    public function unread(): void {
        $this->bootSession();
        $this->requireAuth();

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        if (!$userId || !$role) {
            echo json_encode(['count' => 0]);
            exit;
        }
        
        $count = $this->db->countAnnouncements($userId, $role);
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }

    
    public function showAnnouncements(): void {
        $this->bootSession();
        $this->requireAuth();

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $courseId = $_SESSION['course_id'] ?? null;

        $filter = $_GET['filter'] ?? 'unread';
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $announcements = $this->db->getUserAnnouncements(
            $userId,
            $role,
            $courseId,
            $filter,
            $page,
            $limit
        );

        $count = $this->db->countAnnouncements($userId, $role, $courseId, $filter);
        $totalPages = ceil($count / $limit);

        $this->layout = 'dashboard';
        $this->view('messages/announcements', [
            'announcements' => $announcements,
            'count' => $count,
            'filter' => $filter,
            'page'  => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    // Mark an announcement as read
    public function markAsRead($id): void {

        $this->bootSession();
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $id = (int) Security::clean($id);
        $this->db->markAsRead($id, $userId);
        exit;
    }

    // Delete an announcement
    public function delete($id): void {
        
        $this->bootSession();
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $id = (int) Security::clean($id);
        $this->db->deleteAnnouncement($id, $userId);
        exit;
    }

    // Mark several announcements as read at once
    public function bulkRead() {
        
        $this->bootSession();
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $data = json_decode(file_get_contents("php://input"), true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) return;
        foreach ($ids as $id){
            $id = (int) Security::clean($id);
            $this->db->markAsRead($id, $userId);
        }
    }
    
    // Delete several announcements at once
    public function bulkDelete() {
    
        $this->bootSession();
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $data = json_decode(file_get_contents("php://input"), true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) return;
        foreach ($ids as $id){
            $id = (int) Security::clean($id);
            $this->db->deleteAnnouncement($id, $userId);
        }
        exit;
    }
}
