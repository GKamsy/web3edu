<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Security;
use App\Services\ProfileService;

class ProfileController extends Controller {
    private ProfileService $db;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        $this->db = new ProfileService($conn);
    }

    // Show profile
    public function index(): void {
        $this->bootSession();
        $this->requireAuth();

        $userId = $_SESSION['user_id'];
        $user = $this->db->findUser($userId);

        $this->layout = 'dashboard';
        $this->view('profile/index', [
            'title' => 'My Profile',
            'user'  => $user
        ]);
    }

    // Update profile details
    public function update(): void
    {
        $this->bootSession();
        $this->requireAuth();

        $userId = $_SESSION['user_id'];

        $firstname = Security::clean($_POST['firstname']);
        $surname   = Security::clean($_POST['surname']);
        $email     = Security::clean($_POST['email']);

        $this->db->updateProfile($userId, $firstname, $surname, $email);

        // update session
        $_SESSION['firstname'] = $firstname;
        $_SESSION['surname']   = $surname;
        $_SESSION['email']     = $email;

        header("Location: /profile");
        exit;
    }

    // Change password
    public function changePassword(): void {
        $this->bootSession();
        $this->requireAuth();

        header('Content-Type: application/json');

        $current  = $_POST['current_password'] ?? '';
        $new      = $_POST['new_password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'All fields are required.'
            ]);
            return;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->db->findUser($userId);
        if (!$user || !password_verify($current, $user['password_hash'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Enter correct current password.'
            ]);
            return;
        }

        if ($new !== $confirm) {
            echo json_encode([
                'status' => 'error',
                'message' => 'New passwords do not match.'
            ]);
            return;
        }

        if (!preg_match('/[A-Z]/', $new)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Password must contain a capital letter.'
            ]);
            return;
        }

        if (!preg_match('/\d/', $new)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Password must contain a number.'
            ]);
            return;
        }

        if (!preg_match('/[^a-zA-Z0-9\s]/', $new)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Password must contain a special character.'
            ]);
            return;
        }

        if (strlen($new) < 8 || strlen($new) > 15) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Password must be between 8 and 15 characters.'
            ]);
            return;
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);

        $this->db->updatePassword($userId, $hash);

        echo json_encode([
            'status' => 'success',
            'message' => 'Password updated successfully.'
        ]);
    }
    
    // Change avator
    public function handleAvatarUpload(): void {
        $this->bootSession();
        $this->requireAuth();

        if (!isset($_FILES['avatar'])) {
            http_response_code(400);
            echo 'No file uploaded';
            exit;
        }

        $userId = $_SESSION['user_id'];
        $newAvatar = $this->db->handleAvatarUpload($userId, $_FILES['avatar']);
        $_SESSION['avatar'] = $newAvatar;
        header("Location: /dashboard");
        exit;
    }
}