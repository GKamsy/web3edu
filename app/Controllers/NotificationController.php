<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Security;
use App\Services\ViewRenderer;
use App\Services\NotificationService;

class NotificationController extends Controller {
    
    
    /*================================================
                DB connection
    ================================================*/
    private NotificationService $db;
    public function __construct() {
        require_once __DIR__ . '/../../config/config.php';
        $this->db = new NotificationService($conn);
    }
    
    
    /*================================================
               Notifications
    ================================================*/

    // Create notification
    public function create(): void {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Authorize the user
        $this->requireAuth();
        
        $this->layout = 'dashboard';
        $this->view('messages/create-notification', [
            'title' => 'Create Notification'
        ]);
    }


    
    public function send(): void {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Authorize the user
        $this->requireAuth();

        $senderId = (int) $_SESSION['user_id'];
        //$target = Security::clean($_POST['target']);
        $recipientId = Security::clean($_POST['recipient_id']);
        $type = Security::clean($_POST['type']);
        $title = Security::clean($_POST['title']);
        $message = Security::clean($_POST['message']);
        $link = Security::clean($_POST['link']);
/*
        if (empty($target) || empty($type) || empty($title) || empty($message)) {
            return;
        }
*/
        if (empty($recipientId) || empty($type) || empty($title) || empty($message)) {
            return;
        }
        $this->db->createNotification($recipientId, $type, $title, $message, $link);
/*
        $ids = $this->db->findIdsByRole($target, "users");
        foreach ($ids as $id) {
            $this->db->createNotification($id, $type, $title, $message, $link);
        }
*/
        header("Location: /notifications");
        exit;
    }


    // Get unread notifications
    public function unread(): void {
        $this->bootSession();
        $this->requireAuth();

        $userId = $_SESSION['user_id'];
        if (!$userId) {
            echo json_encode(['count' => 0]);
            exit;
        }
        
        $count = $this->db->countNotifications($userId);
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }
    
    
    // View required notifications
    public function showNotifications() {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Authorize the user
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $filter = $_GET['filter'] ?? 'unread';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $notifications = $this->db->getNotifications($userId, $filter, $limit, $offset);
        $count = $this->db->countNotifications($userId, $filter);
        $totalPages = ceil($count / $limit);

        // Set total notifications view
        $this->layout = 'dashboard';
        $this->view('messages/notifications', [
            'notifications' => $notifications,
            'count' => $count,
            'filter'  => $filter,
            'page'   => $page,
            'totalPages'  => $totalPages
        ]);
    }
    
    // Mark a notification as read
    public function markAsRead(int $id): void {

        $this->bootSession();
        $this->requireAuth();

        $userId = $_SESSION['user_id'];
        $id = (int) Security::clean($id);
        $notification = $this->db->find($id, $userId);

        if (!$notification) {
            http_response_code(404);
            echo json_encode(['error' => 'Notification not found']);
            exit;
        }

        if ($notification['is_read'] === 0 && $notification['user_id'] == $userId) {
            $this->db->markNotificationAsRead($id, $userId);
            $notification['is_read'] = 1;
        }
        exit;
    }

    // Delete a notification
    public function delete(int $id): void {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Authorize the user
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $id = (int) Security::clean($id);
        $this->db->deleteNotification($id, $userId);
        exit;
    }

    // Mark several notifications as read at once
    public function bulkRead() {
        
        // Start the session
        $this->bootSession();
        
        // Authorize the user
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $data = json_decode(file_get_contents("php://input"), true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) return;
        foreach ($ids as $id){
            $id = (int) Security::clean($id);
            $this->db->markNotificationAsRead($id, $userId);
        }
    }
    
    // Delete several notifications at once
    public function bulkDelete() {
        
        // Start the session
        $this->bootSession();
        
        // Authorize the user
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $data = json_decode(file_get_contents("php://input"), true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) return;
        foreach ($ids as $id){
            $id = (int) Security::clean($id);
            $this->db->deleteNotification($id, $userId);
        }
        exit;
    }
}
