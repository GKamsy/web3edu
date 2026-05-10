<?php
declare(strict_types=1);

namespace App\Controllers;
use App\Core\Controller;
use App\Services\Security;
use App\Services\AuthService;
use App\Services\ViewRenderer;
use App\Services\MailService;
use App\Core\Csrf;


class AuthController extends Controller {
    
    private AuthService $auth;

    // Inject AuthService
    public function __construct() {
        require_once __DIR__ . '/../../config/config.php'; // $conn
        $this->auth = new AuthService($conn);
    }
    
    // Generate a digit code
    public function generateCode(int $length): string {
        return str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    // Send mail to a client
    public function sendMail(
        
        string $template_file,    // e.g. 'verify-email.php'
        string $from,
        string $to,
        string $subject,
        array  $mail_data = []
    ): void {
        
        // Start the sessiuon
        $this->bootSession();

        // Make the email template
        $message = ViewRenderer::render(
            __DIR__ . "/../Views/emails/$template_file",
            array_merge(
                [
                    'school' => $this->schoolInfo,
                ],
                $mail_data
            )
        );

        MailService::send($from, $to, $subject, $message );
    }
    

    // Prepare the login process
    public function login(): void {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Delete expired tokens
        $this->auth->purgeExpiredEmailVerifications();
        
        // Enforce POST (extra safety)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            (new PageController())->notAllowed();
            exit;
        }
        
        if(isset($_POST["username"])) {
            // Collect input safely
            $credentials = [
                'role'     => $_POST['role']     ?? null,
                'roleLabel' => ucwords($_POST['role'])  ?? null,
                'username' => $_POST['username'] ?? null,
                'password' => $_POST['password'] ?? null,
                '_csrf'    => $_POST['_csrf']    ?? null,
            ];
            
            // Process the user credentials
            $this->loginUser( $credentials );
            exit;
        }
        elseif(isset($_SESSION['_username'])) {
            // Process the verification code
            $credentials = [
                'code'     => Security::clean($_POST['code'])  ?? null,
                '_csrf'    => $_POST['_csrf']    ?? null,
            ];
            $this->verifyLoginCode( $credentials );
            exit;
        }
        else{
            http_response_code(405);
            (new PageController())->notAllowed();
            exit;
        }
    }
    
    
    // Login process
    public function loginUser(array $credentials): void {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Clean the user inputs
        $role = Security::clean($credentials['role']);
        $_csrf = Security::clean($credentials['_csrf']);
        $username = Security::clean($credentials['username']);
        $_SESSION['_csrf']     = $credentials['_csrf'];

        // CSRF validation (FIRST)
        if (!Csrf::validate($_SESSION['_csrf'] ?? null)) {
            http_response_code(419);
            $credentials['messageErr'] = 'Session expired! Please try again.';
        }

        // Validate inputs
        elseif (empty($credentials['username']) || empty($credentials['password'])) {
            $credentials['messageErr'] = 'Username and password are required.';
        }
        else{
            $credentials['messageErr'] = '';
        }
        
        if($credentials['messageErr'] !== ''){
            unset($credentials['password']);
            $this->redirectBack( '/home/login', 'lock.png', $credentials );
            exit;
        }

        // Credential verification
        $user = $this->auth->findUserByUsername($credentials['username'], "users");
        if (!$user || !$this->auth->verifyPassword(
                $_POST['password'],
                $user['password_hash']
            )) {
            $not_yet = $this->auth->findUserByUsername($credentials['username'], "applicants");
            // Enforce email confirmation before loging in
            if ($not_yet && empty($user['email_verified_at'])) {
                $credentials['messageErr'] = 'Please verify your email before logging in.';
                $this->resendVMail();
                //$this->redirectBack( '/home/login', 'lock.png', $credentials );
                exit;
            }
        }
        if (!$user || !$this->auth->verifyPassword(
            $_POST['password'],
            $user['password_hash']
        )) {
            $credentials['messageErr'] = 'Wrong username or password!';
            unset($credentials['password']);
            $this->redirectBack( '/home/login', 'lock.png', $credentials );
            exit;
        }

        // Set temporary sessions
        $_SESSION['_user_id'] = $user['id'];
        $_SESSION['_firstname'] = $user['firstname'];
        $_SESSION['_username'] = $user['username'];

        // Step-up verification for staff
        if (in_array($user['role'], ['teacher', 'principal admin'], true)) {

            // Send a verification code
            $this->sendVCode($user);
            
            // Request a login code for processing
            $this->redirectBack('/home/login-code', 'email.png', );
            exit;
        }
        
        // Finalize login process
        $this->finalizeLogin($user);
    }
    
    // Send a verification code via email
    public function sendVCode(array $user): void {
        
        // Start the sessiuon
        $this->bootSession();

        // Generate & store code
        $code = $this->auth->createLoginVerification($user['id']);

        // Send email
        $this->sendMail(
            'login-code.php',
            'no_reply@forchange.com',
            $user['email'],
            'Your login verification code',
            [
                'firstname' => $user['firstname'],
                'code'     => $code,
            ]
        );
    }

    // Resend verification code
    public function resendVCode(): void {
        
        // Start the sessiuon
        $this->bootSession();

        if (!isset($_SESSION['_username'])) {
            $this->redirectBack('shared/not-allowed', 'oops.png');
            exit;
        }

        // Create user info
        $user = $this->auth->findUserByUsername($_SESSION['_username'], "users");
        
        // Send a verification code via email
        $this->sendVCode($user);
        $this->redirectBack('/home/login-code', 'email.png', );
        exit;
    }

    // Code verification process
    public function verifyLoginCode(array $credentials): void {
        
        // Start the sessiuon
        $this->bootSession();

        if (!isset($_SESSION['_username'])) {
            (new PageController())->notAllowed();
            exit;
        }

        if (!Csrf::validate($credentials['_csrf'] ?? null)) {
            http_response_code(419);
            $this->redirectBack('/home/login-code', 'email.png');
            echo 'Invalid or expired csrt code detected.';
            exit;
        }

        $user = $this->auth->findUserByUsername($_SESSION['_username'], 'users');

        if (!$this->auth->verifyLoginCode($user['id'], $credentials['code'])) {
            $this->redirectBack('/home/login-code', 'email.png');
            echo 'Invalid or expired verification code.';
            exit;
        }
        
        // Finalize login process
        $this->finalizeLogin($user);
    }


    // SUCCESS — finalize login
    public function finalizeLogin(array $user): void {
        
        // Start the sessiuon
        $this->bootSession();

        if (!isset($_SESSION['_username'])) {
            (new PageController())->notAllowed();
            exit;
        }
        
        session_regenerate_id(true);

        // Login success, set sessions
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['surname'] = $user['surname'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role']  = $user['role'];
        $_SESSION['roleLabel'] = ucwords($user['role']);
        $_SESSION['avatar'] = $user['avatar'];

        unset($_SESSION['_firstname']);
        unset($_SESSION['_username']);

        // Redirect to the dashboard
        header("Location: /dashboard");
        //(new DashboardController())->index();
        exit;
    }

    // Application process
    public function apply(): void {
        
        // Start the session
        $this->bootSession();
        
        // Delete expired tokens
        $this->auth->purgeExpiredEmailVerifications();
        
        // Enforce POST request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            (new PageController())->notAllowed();
            exit;
        }

        // Collect input safely
        $credentials = [
            'role'     => $_POST['role']     ?? null,
            'roleLabel' => ucwords($_POST['role'])  ?? null,
            'firstname' => $_POST['firstname'] ?? null,
            'surname' => $_POST['surname'] ?? null,
            'username' => $_POST['username'] ?? null,
            'email' => $_POST['email'] ?? null,
            'password' => $_POST['password'] ?? null,
            'password2' => $_POST['password2'] ?? null,
            '_csrf'    => $_POST['_csrf']    ?? null,
        ];

        // Clean data for security reasons against the database
        $role = Security::clean($credentials['role']);
        $_csrf = Security::clean($credentials['_csrf']);
        $firstname = Security::clean($credentials['firstname']);
        $surname = Security::clean($credentials['surname']);
        $username = Security::clean($credentials['username']);
        $email = Security::clean($credentials['email']);
        $password = $credentials['password'];
        $password2 = $credentials['password2'];

        // Prepare the error messages
        if (empty($role)) {
            $credentials['messageErr'] = 'Please select your role';
        }
        elseif (empty($firstname)) {
            $credentials['messageErr'] = 'Firstname is required';
        }
        elseif (empty($surname)) {
            $credentials['messageErr'] = 'Surname is required';
        }
        elseif (empty($username)) {
            $credentials['messageErr'] = 'Username is required';
        }
        elseif (empty($email)) {
            $credentials['messageErr'] = 'Email is required';
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $credentials['messageErr'] = "Invalid email format, check closely.";
        }
        elseif (empty($password)) {
            $credentials['messageErr'] = 'Password is required';
        }
        elseif(!preg_match('/[A-Z]/', $password)){
            $credentials['messageErr'] = "Weak Password (no capital letter)";
        }
        elseif(!preg_match('/\d/', $password)){
            $credentials['messageErr'] = "Weak Password (no number)";
        }
        elseif(!preg_match('/[^a-zA-Z0-9\s]/', $password)){
            $credentials['messageErr'] = "Weak Password (no special character eg: !, #, @)";
        } 
        elseif(strlen($password) < 8){
            $credentials['messageErr'] = "Password is too short (8 characters minimum)";
        }
        elseif(strlen($password) > 15){
            $credentials['messageErr'] = "Password is too long (15 characters maxmum)";
        }
        elseif (empty($credentials['password2'])) {
            $credentials['messageErr'] = 'Please enter your password again.';
        }
        elseif ($password !== $credentials['password2']) {
            $credentials['messageErr'] = 'Passwords are not matching!';
        }
        elseif ($this->auth->usernameExists($username)) { // Username already  exists
            $credentials['messageErr'] = 'Username already exists';
        }
        elseif ($this->auth->emailExists($email)) { // Email already  exists
            $credentials['messageErr'] = 'Email already exists';
        }
        else {
            $credentials['messageErr'] = "";
        }
        
        // Redirect back if there is an error
        if ($credentials['messageErr'] !== "" ) {
            unset($credentials['password']);
            unset($credentials['password2']);
            $this->redirectBack( '/home/apply', 'admission.png', $credentials );
            exit;
        }
        
        // Check against username and email
        $_SESSION['_username'] = $username;
        $_SESSION['_firstname'] = $firstname;
        $user = $this->auth->findUserByUsername($username, "applicants");
        if (isset($user['username']) && $user['username'] === $username) {
            $credentials['messageErr'] = 'Username already exists, try again!';
            unset($credentials['username']);
            unset($credentials['password']);
            unset($credentials['password2']);
            $this->redirectBack( '/home/apply', 'admission.png', $credentials );
            exit;
        }
        if (isset($user['email']) && $user['email'] === $email) {
            $credentials['messageErr'] = 'Email already exists, try another one!';
            unset($credentials['email']);
            unset($credentials['password']);
            unset($credentials['password2']);
            $this->redirectBack( '/home/apply', 'admission.png', $credentials );
            exit;
        }

        // Insert data into applicant table
        $userInfo = [
            'role'         => $role,
            'firstname'     => $firstname,
            'surname'      => $surname,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'username'      => $username,
            'timestamp'      => date('Y-m-d H:i:s'),
        ];
        $userId = $this->auth->createApplicant($userInfo);
        $_SESSION['_user_id'] = $userId;

        // Redirect back if the database failed
        if ($userId === 0) {
            $credentials['messageErr'] = 'Registration failed. Please try again or contact us.';
            unset($credentials['password']);
            unset($credentials['password2']);
            $this->redirectBack( '/home/apply', 'admission.png', $credentials );
            exit;
        }

        // Send a confirmation email
        $this->sendVmail($userInfo);
        
        // Application successfull, inform about the email
        $this->redirectBack( '/home/confirm-email', 'email.png', $credentials );
        exit;
    }
    
    // Send a confirmation email
    public function sendVmail(array $user ): void {
        
        // Start the sessiuon
        $this->bootSession();
    
        $verify = $this->auth->createEmailVerification($_SESSION['_user_id']);

        $link = sprintf(
            '%s/verify-vmail?token=%s',
            $_ENV['APP_URL'] ?? 'http://localhost',
            $verify['token']
        );

        // Send mail
        $this->sendMail(
            'confirm-email.php',
            "no_reply@forchange.com",
            $user['email'],
            'Confirm your email address',
            [
                'firstname'   => $user['firstname'],
                'verifyLink' => $link,
            ]
        );
    }
    
    // Resend verification email
    public function resendVMail(): void {
        
        // Start the sessiuon
        $this->bootSession();

        if (!isset($_SESSION['_user_id'])) {
            $this->redirectBack('shared/not-allowed', 'oops.png');
            exit;
        }

        if (!Csrf::validate($_SESSION['_csrf'] ?? null)) {
            $this->redirectBack('shared/not-allowed', 'oops.png');
            exit;
        }

        // Send a confirmation email 
        $user = $this->auth->findUserByUsername($_SESSION['_username'], "applicants");
        $this->sendVmail($user);
        
        // Application successfull, inform about the email
        $this->redirectBack( '/home/confirm-email', 'email.png', $user );
        exit;
    }

    
    // Verify email
    public function verifyEmail(): void {
        
        // Start the sessiuon
        $this->bootSession();

        $token = $_GET['token'] ?? '';

        if ($token === '') {
            $this->redirectBack('/home/apply', 'admission.png', [
                'messageErr' => 'Verification link is invalid.'
            ]);
            return;
        }

        $applicant = $this->auth->verifyEmailToken($token);

        if (!$applicant) {
            $this->redirectBack('/home/apply', 'admission.png', [
                'messageErr' => 'Verification link is invalid or has expired.'
            ]);
            return;
        }
        
        // Mark pending applicants as pending_approvals
        $this->auth->markPendingApproval($applicant['id']);
        $this->redirectBack('/home/application-done', 'tick.png', [
            '_firstname' => $applicant['firstname']
        ]);
        exit;
    }
    
    // Fetch all pending approval (for principal only)
    public function getPendingApprovals(): array {
        
        // Start the sessiuon
        $this->bootSession();

        // Restict access
        if (($_SESSION['role'] ?? '') !== 'principal') {
            http_response_code(403);
            (new PageController())->notAllowed();
            exit;
        }
       
        // Get all pendings
        return $this->auth->fetchPendingApplicants();
    }
    
    // Approved aplicant (for principal only)
    public function approveApplicant(): void {
        
        // Start the session
        $this->bootSession();

        // Restrict access
        if (($_SESSION['role'] ?? '') !== 'principal') {
            http_response_code(403);
            (new PageController())->notAllowed();
            return;
        }

        if (empty($_POST['approve_ids']) || !is_array($_POST['approve_ids'])) {
            $this->redirectBack('/home/application-done', 'tick.png');
            return;
        }

        // Sanitize IDs
        $ids = array_map('intval', $_POST['approve_ids']);

        foreach ($ids as $applicantId) {
            $user = $this->auth->approveApplicant($applicantId);

            // Send welcome email
            $this->sendMail(
                'welcome.php',
                'no_reply@forchange.com',
                $user['email'],
                'Welcome to ForChange School',
                [
                    'firstname' => $user['firstname'],
                    'role'      => $user['role'],
                ]
            );
        }

        $this->redirectBack('/home/application-done', 'tick.png', [
            '_firstname' => $user['firstname']
        ]);
    }

    // Dashboard landing page
    public function dashboard(): void {
        $this->bootSession();
        $this->requireAuth();

        // Dashboard layout
        $this->layout = 'dashboard';
        $this->breadcrumbs([['label' => 'Dashboard']]);


        $this->view('dashboard/index', [
            'title'     => 'Dashboard',
            'firstname' => $_SESSION['firstname'] ?? '',
            'role'      => $_SESSION['role'] ?? '',
            'roleLabel' => $_SESSION['roleLabel'] ?? '',
        ]);
    }

    // Logout of the session
    public function logout(): void {
        
        // Start the sessiuon
        $this->bootSession();
        
        // Delete expired tokens
        $this->auth->purgeExpiredEmailVerifications();

        if (isset($_COOKIE['remember'])) {
            setcookie('remember', '', time() - 3600, '/');
        }
        
        Csrf::destroy();
        session_destroy();
        
        //redirect to login page
        header("Location: /campus");
        exit;
    }

}
