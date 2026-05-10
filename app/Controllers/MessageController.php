<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ViewRenderer;
use App\Services\Security;
use App\Services\MessageService;

class MessageController extends Controller {
    
    
    /*================================================
                DB connection
    ================================================*/
    private MessageService $db;
    public function __construct() {
        require_once __DIR__ . '/../../config/config.php';
        $this->db = new MessageService($conn);
    }
    
    
    
    
    /*================================================
               Messages
    ================================================*/
    public function thread(int $id): void {

        $this->bootSession();
        $this->requireAuth();

        $userId = $_SESSION['user_id'];
        $message = $this->db->find($id, $userId);

        if (!$message) {
            http_response_code(404);
            echo json_encode(['error' => 'Message not found']);
            exit;
        }

        if (!$message['is_read'] && $message['receiver_id'] == $userId) {
            $this->db->markMessageAsRead($id);
            $message['is_read'] = 1;
        }

        header('Content-Type: application/json');
        echo json_encode($message);
        exit;
    }
    
    public function markAsRead(int $id): void {

        $this->bootSession();
        $this->requireAuth();

        $userId = $_SESSION['user_id'];
        $message = $this->db->find($id, $userId);

        if (!$message) {
            http_response_code(404);
            echo json_encode(['error' => 'Message not found']);
            exit;
        }

        if (!$message['is_read'] && $message['receiver_id'] == $userId) {
            $this->db->markMessageAsRead($id);
            $message['is_read'] = 1;
        }
        exit;
    }


    public function messageList(): void {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Authorize the user
        $this->requireAuth();

        $folder = $_GET['folder'] ?? 'inbox';
        $userId = $_SESSION['user_id'];

        $messages = $this->auth->inbox($userId, $folder);

        header('Content-Type: application/json');
        echo json_encode($messages);
        exit;
    }


    public function index(): void {

        $this->bootSession();
        $this->requireAuth();

        $userId = $_SESSION['user_id'];

        $filter = $_GET['filter'] ?? 'inbox';
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $messages = $this->db->getMessages(
            $userId,
            $filter,
            $limit,
            $offset
        );

        $count = $this->db->countMessages($userId, $filter);
        $totalPages = ceil($count / $limit);

        $this->layout = 'dashboard';
        $this->view('messages/messages', [
            'messages'   => $messages,
            'count'     => $count,
            'filter'     => $filter,
            'page'       => $page,
            'totalPages' => $totalPages
        ]);
    }

    
    public function compose(): void {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Authorize the user
        $this->requireAuth();
        
        $this->layout = 'dashboard';
        $this->view('messages/compose', [
            'title' => 'Compose Message'
        ]);
    }

    
    public function send(): void {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Authorize the user
        $this->requireAuth();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $senderId = (int) $_SESSION['user_id'];
        $receiverId = (int) $_POST['receiver_id'];
        $subject = Security::clean($_POST['subject']);
        $body = Security::clean($_POST['body']);
        $parentId = Security::clean($_POST['parent_id'] ?? null);

        if (empty($subject) || empty($body)) {
            die("Subject and message body are required.");
        }

        $this->db->send(
            $senderId,
            $receiverId,
            $subject,
            $body,
            $parentId ? (int)$parentId : null
        );

        header("Location: /messages?folder=sent");
        exit;
    }

    public function deleteMessage(int $id): void {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Authorize the user
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $id = (int) Security::clean($id);
        $this->db->deleteMessage($id, $userId);
        exit;
    }

    // Delete several messages at once
    public function bulkDeleteMessages() {
        
        // Start the session and authorize the user
        $this->bootSession();
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $data = json_decode(file_get_contents("php://input"), true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) return;
        foreach ($ids as $id){
            $id = (int) Security::clean($id);
            $this->db->deleteMessage($id, $userId);
        }
    }
    
    // Get unread messages
    public function unread(): void {

        $this->bootSession();
        $this->requireAuth();

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['count' => 0]);
            exit;
        }

        $count = $this->db->getUnreadMessages((int)$userId);
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }

}
